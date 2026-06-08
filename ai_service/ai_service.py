import os

os.environ["TF_CPP_MIN_LOG_LEVEL"] = "2"

from flask import Flask, jsonify, request
import cv2
import numpy as np
import tensorflow as tf


app = Flask(__name__)

CURRENT_DIR = os.path.dirname(__file__)
MODEL_DIR = os.path.abspath(os.path.join(
    CURRENT_DIR,
    "..",
    "noisystudent",
    "model_output",
    "transfer_mobilenetv2",
    "saved_model",
))
CLASSES = ["Benign", "Malignant"]
MIN_IMAGE_SIDE = 160
MAX_COLORFULNESS = 32.0
MAX_AVG_SATURATION = 65.0
MAX_CHANNEL_DIFF = 24.0
MIN_CONTRAST_STD = 22.0
MIN_EDGE_DENSITY = 0.004
MAX_EDGE_DENSITY = 0.28
MAX_SKIN_TONE_RATIO = 0.08
MAX_HIGH_SATURATION_RATIO = 0.08
GRAYSCALE_SATURATION_TOLERANCE = 8.0
GRAYSCALE_CHANNEL_DIFF_TOLERANCE = 6.0

infer = None

try:
    imported = tf.saved_model.load(MODEL_DIR)
    infer = imported.signatures["serving_default"]
    print("\n" + "=" * 45)
    print("--- [OK] SavedModel loaded ---")
    print(f"--- Model dir: {MODEL_DIR} ---")
    print("=" * 45 + "\n")
except Exception as exc:
    print(f"\n--- [ERROR] Cannot load AI model: {exc} ---")
    infer = None


def decode_image(image_bytes):
    try:
        nparr = np.frombuffer(image_bytes, np.uint8)
        img = cv2.imdecode(nparr, cv2.IMREAD_COLOR)
        return img
    except Exception as exc:
        print(f"OpenCV image decoding error: {exc}")
        return None


def colorfulness_score(img):
    b_channel, g_channel, r_channel = cv2.split(img.astype("float32"))
    rg = np.abs(r_channel - g_channel)
    yb = np.abs(0.5 * (r_channel + g_channel) - b_channel)
    std_root = np.sqrt(np.std(rg) ** 2 + np.std(yb) ** 2)
    mean_root = np.sqrt(np.mean(rg) ** 2 + np.mean(yb) ** 2)
    return float(std_root + (0.3 * mean_root))


def assess_xray_image(img):
    if img is None:
        return False, "Invalid image format", {}

    height, width = img.shape[:2]
    if min(height, width) < MIN_IMAGE_SIDE:
        return False, "Image is too small to be a reliable chest X-ray", {
            "width": width,
            "height": height,
        }

    hsv = cv2.cvtColor(img, cv2.COLOR_BGR2HSV)
    gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)
    channel_means = np.mean(img, axis=(0, 1))
    channel_diff = float(np.max(channel_means) - np.min(channel_means))
    avg_saturation = float(np.mean(hsv[:, :, 1]))
    contrast_std = float(np.std(gray))
    brightness_mean = float(np.mean(gray))
    colorful = colorfulness_score(img)
    skin_tone_mask = (
        (hsv[:, :, 0] >= 0)
        & (hsv[:, :, 0] <= 25)
        & (hsv[:, :, 1] >= 25)
        & (hsv[:, :, 1] <= 180)
        & (hsv[:, :, 2] >= 50)
    )
    skin_tone_ratio = float(np.mean(skin_tone_mask))
    high_saturation_ratio = float(np.mean(hsv[:, :, 1] > 80))
    is_grayscale_like = (
        avg_saturation <= GRAYSCALE_SATURATION_TOLERANCE
        and channel_diff <= GRAYSCALE_CHANNEL_DIFF_TOLERANCE
        and colorful <= GRAYSCALE_SATURATION_TOLERANCE
    )

    edges = cv2.Canny(gray, 45, 135)
    edge_density = float(np.mean(edges > 0))

    metrics = {
        "width": width,
        "height": height,
        "brightness_mean": round(brightness_mean, 2),
        "contrast_std": round(contrast_std, 2),
        "avg_saturation": round(avg_saturation, 2),
        "channel_diff": round(channel_diff, 2),
        "colorfulness": round(colorful, 2),
        "edge_density": round(edge_density, 4),
        "skin_tone_ratio": round(skin_tone_ratio, 4),
        "high_saturation_ratio": round(high_saturation_ratio, 4),
        "is_grayscale_like": is_grayscale_like,
    }

    if brightness_mean < 12 or brightness_mean > 245:
        return False, "Image exposure is not consistent with an X-ray", metrics

    if contrast_std < MIN_CONTRAST_STD:
        return False, "Image does not have enough radiographic contrast", metrics

    if edge_density < MIN_EDGE_DENSITY or edge_density > MAX_EDGE_DENSITY:
        return False, "Image structure is not consistent with a chest X-ray", metrics

    if not is_grayscale_like:
        if (
            colorful > MAX_COLORFULNESS
            or avg_saturation > MAX_AVG_SATURATION
            or channel_diff > MAX_CHANNEL_DIFF
        ):
            return False, "Only grayscale chest X-ray images are accepted", metrics

        if skin_tone_ratio > MAX_SKIN_TONE_RATIO:
            return False, "Camera/photo-like image detected; only chest X-ray images are accepted", metrics

        if high_saturation_ratio > MAX_HIGH_SATURATION_RATIO:
            return False, "Image contains too many saturated color regions for a chest X-ray", metrics

    return True, "Accepted as a probable chest X-ray", metrics


def prepare_image(img):
    try:
        if img is None:
            return None

        lab = cv2.cvtColor(img, cv2.COLOR_BGR2LAB)
        l_channel, a_channel, b_channel = cv2.split(lab)
        clahe = cv2.createCLAHE(clipLimit=2.0, tileGridSize=(8, 8))
        enhanced_l = clahe.apply(l_channel)
        img = cv2.merge((enhanced_l, a_channel, b_channel))
        img = cv2.cvtColor(img, cv2.COLOR_LAB2BGR)

        img = cv2.GaussianBlur(img, (3, 3), 0)
        img = cv2.resize(img, (224, 224))
        img = img.astype("float32")
        img = np.expand_dims(img, axis=0)

        return tf.convert_to_tensor(img, dtype=tf.float32)
    except Exception as exc:
        print(f"OpenCV image preprocessing error: {exc}")
        return None


@app.route("/predict", methods=["POST"])
def predict():
    if infer is None:
        return jsonify({"error": "AI model is not loaded"}), 500

    if "file" not in request.files:
        return jsonify({"error": "No image file was uploaded"}), 400

    try:
        image_bytes = request.files["file"].read()
        img = decode_image(image_bytes)
        is_xray, xray_message, xray_metrics = assess_xray_image(img)
        if not is_xray:
            print(f"\n--- [REJECTED] {xray_message} ---")
            print(f"Metrics: {xray_metrics}\n")
            return jsonify({
                "error": "INVALID_XRAY_IMAGE",
                "message": "He thong chi nhan anh X-quang nguc. Vui long tai len dung anh X-quang.",
                "reason": xray_message,
                "metrics": xray_metrics,
            }), 422

        processed_tensor = prepare_image(img)
        if processed_tensor is None:
            return jsonify({"error": "Invalid image format"}), 400

        input_key = list(infer.structured_input_signature[1].keys())[0]
        prediction_result = infer(**{input_key: processed_tensor})
        output_key = list(prediction_result.keys())[0]
        probabilities = prediction_result[output_key].numpy()

        if not np.allclose(np.sum(probabilities, axis=1), 1.0, atol=1e-3):
            probabilities = tf.nn.softmax(probabilities).numpy()

        result_index = int(np.argmax(probabilities[0]))
        label = CLASSES[result_index]
        confidence = float(np.max(probabilities[0]))

        print(f"\n--- [PREDICT] Result: {label} ({confidence * 100:.2f}%) ---")
        print(f"Scores: {probabilities[0]}\n")

        return jsonify({
            "prediction": label,
            "confidence": confidence,
            "raw_scores": probabilities[0].tolist(),
        })
    except Exception as exc:
        print(f"--- [CRASH AI SERVICE] {exc} ---")
        return jsonify({"error": f"AI service error: {exc}"}), 500


if __name__ == "__main__":
    app.run(host="127.0.0.1", port=5000, debug=True)
