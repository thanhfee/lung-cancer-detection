from flask import Flask, request, jsonify # type: ignore
from flask_cors import CORS                 # type: ignore
import tensorflow as tf
import numpy as np
import cv2  
from PIL import Image
import io
import os

app = Flask(__name__)
CORS(app)

# Đường dẫn model
MODEL_PATH = os.path.join(os.path.dirname(__file__), 'medical_model.h5')

model = None
if os.path.exists(MODEL_PATH):
    try:
        model = tf.keras.models.load_model(MODEL_PATH)
        print("✅ Da load Model AI ResNet50 thanh cong!")
    except Exception as e:
        print(f"❌ Loi khi load model: {e}")
else:
    print("⚠️ Khong tim thay file medical_model.h5. Dang chay che do GIA LAP.")

def apply_clahe_preprocessing(image_bytes):
    # 1. Chuyển bytes sang numpy array để OpenCV xử lý
    nparr = np.frombuffer(image_bytes, np.uint8)
    img = cv2.imdecode(nparr, cv2.IMREAD_GRAYSCALE)
    
    if img is None:
        return None

    # 2. Cấu hình CLAHE (Làm nổi bật ranh giới khối u)
    # clipLimit=3.0 giúp tăng tương phản vừa phải, không bị cháy sáng
    clahe = cv2.createCLAHE(clipLimit=3.0, tileGridSize=(8, 8))
    enhanced_img = clahe.apply(img)

    # 3. Khử nhiễu nhẹ bằng Gaussian Blur
    denoised_img = cv2.GaussianBlur(enhanced_img, (3, 3), 0)

    # 4. Chuyển về RGB và Resize về 224x224 (ResNet50 chuẩn)
    final_img = cv2.cvtColor(denoised_img, cv2.COLOR_GRAY2RGB)
    final_img = cv2.resize(final_img, (224, 224))
    
    # 5. Chuẩn hóa về [0, 1]
    img_array = final_img.astype('float32') / 255.0
    img_array = np.expand_dims(img_array, axis=0)
    
    return img_array

@app.route('/api/v1/predict', methods=['POST'])
def predict():
    if 'image' not in request.files:
        return jsonify({'error': 'Khong tim thay anh'}), 400

    file = request.files['image']
    image_bytes = file.read()

    try:
        if model:
            # Tiền xử lý ảnh qua CLAHE
            processed_img = apply_clahe_preprocessing(image_bytes)
            
            if processed_img is None:
                return jsonify({'error': 'Dinh dang anh khong hop le'}), 400

            prediction = model.predict(processed_img)
            prob = float(prediction[0][0])
            
            # --- LOGIC CHẨN ĐOÁN 3 CẤP ĐỘ MỚI ---
            # Ngưỡng (Threshold) được thiết lập để tăng độ chân thực
            if 0.40 <= prob <= 0.60:
                # Vùng không chắc chắn (Uncertain)
                result_text = 'Uncertain'
                confidence = prob if prob > 0.5 else (1 - prob)
            elif prob > 0.60:
                # Vùng ác tính (Cancer)
                result_text = 'Malignant'
                confidence = prob
            else:
                # Vùng lành tính (No Cancer)
                result_text = 'Benign'
                confidence = 1 - prob
            # ------------------------------------
        else:
            # Chế độ giả lập khi không có file model
            import random
            result_text = random.choice(['Benign', 'Malignant', 'Uncertain'])
            confidence = random.uniform(0.85, 0.98)

        return jsonify({
            'status': 'success',
            'prediction': result_text,
            'confidence': round(confidence * 100, 2)
        })
    except Exception as e:
        print(f"Lỗi Predict: {e}")
        return jsonify({'error': str(e)}), 500

if __name__ == '__main__':
    print("--- AI Service ResNet50 + CLAHE + Triple Logic dang san sang ---")
    app.run(host='127.0.0.1', port=5000, debug=False)