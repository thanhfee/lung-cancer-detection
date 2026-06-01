import os
os.environ['TF_CPP_MIN_LOG_LEVEL'] = '2'

from flask import Flask, request, jsonify
import tensorflow as tf
import numpy as np
import cv2

app = Flask(__name__)

# Thiết lập đường dẫn chính xác tới thư mục chứa file SavedModel (.pb)
CURRENT_DIR = os.path.dirname(__file__) 
MODEL_DIR = os.path.abspath(os.path.join(CURRENT_DIR, '..', 'noisystudent', 'exported_model'))

infer = None

try:
    imported = tf.saved_model.load(MODEL_DIR)
    infer = imported.signatures['serving_default']
    print("\n" + "="*45)
    print("--- [TIN VUI] SAVED_MODEL (.PB) ĐÃ KHỞI CHẠY! ---")
    print(f"--- Thư mục model: {MODEL_DIR} ---")
    print("="*45 + "\n")
except Exception as e:
    print(f"\n--- [ERROR] Không thể load mô hình AI: {str(e)} ---")
    infer = None

def prepare_image(image_bytes):
    try:
        nparr = np.frombuffer(image_bytes, np.uint8)
        img = cv2.imdecode(nparr, cv2.IMREAD_COLOR)
        
        if img is None:
            return None

        # 1. Nâng cao độ tương phản cục bộ bằng CLAHE (Trị mờ ảnh X-quang)
        lab = cv2.cvtColor(img, cv2.COLOR_BGR2LAB)
        l, a, b = cv2.split(lab)
        clahe = cv2.createCLAHE(clipLimit=2.0, tileGridSize=(8,8))
        cl = clahe.apply(l)
        limg = cv2.merge((cl,a,b))
        img = cv2.cvtColor(limg, cv2.COLOR_LAB2BGR)

        # 2. Lọc nhiễu nhẹ bằng GaussianBlur
        img = cv2.GaussianBlur(img, (3, 3), 0)

        # 3. Đưa kích thước về chuẩn 224x224 (EfficientNet-B0)
        img = cv2.resize(img, (224, 224)) 
        
        # 4. Ép kiểu dữ liệu tính toán và Chuẩn hóa pixel về khoảng [-1, 1]
        img = img.astype('float32')
        img = (img / 127.5) - 1.0 
        
        # 5. Thêm trục Batch (1, 224, 224, 3)
        img = np.expand_dims(img, axis=0)
        
        return tf.convert_to_tensor(img, dtype=tf.float32)
    except Exception as e:
        print(f"Lỗi xảy ra tại bước xử lý ảnh OpenCV: {e}")
        return None

@app.route('/predict', methods=['POST'])
def predict():
    if infer is None:
        return jsonify({'error': 'Dịch vụ AI chưa khởi tạo thành công'}), 500
        
    if 'file' not in request.files:
        return jsonify({'error': 'Không nhận được file ảnh gửi sang'}), 400

    try:
        file = request.files['file']
        image_bytes = file.read()
        
        processed_tensor = prepare_image(image_bytes)
        if processed_tensor is None:
            return jsonify({'error': 'Định dạng hình ảnh đầu vào không hợp lệ'}), 400
        
        # Thực hiện nạp tensor vào cổng dự đoán 'input'
        prediction_result = infer(input=processed_tensor)

        # Lấy kết quả thô từ Node đầu ra
        output_key = list(prediction_result.keys())[0]
        raw_prediction = prediction_result[output_key].numpy()
        
        # Tính toán xác suất lớp thông qua hàm kích hoạt Softmax
        probabilities = tf.nn.softmax(raw_prediction).numpy()
        
        # Đồng bộ mảng nhãn phân loại của bài toán
        classes = ['Malignant', 'Benign'] 
        result_index = np.argmax(probabilities[0])
        
        label = classes[result_index]
        confidence = float(np.max(probabilities[0]))

        # Log trực tiếp tiến trình phân tích ra màn hình console
        print(f"\n--- [LOG DỰ ĐOÁN] Kết quả: {label} ({confidence*100:.2f}%) ---")
        print(f"Xác suất thô: {probabilities[0]}\n")

        return jsonify({
            'prediction': label,
            'confidence': confidence,
            'raw_scores': probabilities[0].tolist()
        })

    except Exception as e:
        print(f"--- [CRASH AI SERVICE] {str(e)} ---")
        return jsonify({'error': f'Lỗi hệ thống AI: {str(e)}'}), 500

if __name__ == '__main__':
    app.run(host='127.0.0.1', port=5000, debug=True)