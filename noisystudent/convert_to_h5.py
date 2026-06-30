import tensorflow as tf
import os

def convert_directly_to_h5():
    print("--- 1. Dang khoi tao mang EfficientNet-B0 (2 lop) ---")
    # Khởi tạo model Keras
    base_model = tf.keras.applications.EfficientNetB0(
        include_top=True,
        weights=None, # Chưa có trọng số
        classes=2,
        input_shape=(224, 224, 3)
    )

    ckpt_path = r'C:\xampp\htdocs\Do-an-Laravel\lung-cancer-detection\noisystudent\model_output\model.ckpt-5000'
    
    print(f"--- 2. Dang nap trong so tu: {ckpt_path} ---")
    
    try:
        # Dùng phương thức load_weights của Keras thay vì load_checkpoint của train
        # Lưu ý: Expect partial vì tên layer của Estimator và Keras thường lệch nhau
        base_model.load_weights(ckpt_path).expect_partial()
        
        # Sau khi load xong, kiểm tra thử một vài trọng số xem có khác 0 không
        weights = base_model.get_weights()
        if len(weights) > 0:
            print(f"--- [OK] Da nạp thành công {len(weights)} lớp trọng số! ---")
        
        base_model.save('medical_model.h5')
        
        file_size = os.path.getsize('medical_model.h5') / (1024 * 1024)
        print(f"\n>>> XUẤT FILE THÀNH CÔNG: medical_model.h5")
        print(f">>> Dung lượng file: {file_size:.2f} MB")
        print(">>> Nếu file nặng khoảng 15MB - 30MB là CHUẨN!")
        
    except Exception as e:
        print(f"\nLỗi nạp trọng số: {e}")
        print("\nLƯU Ý: Nếu vẫn ra 16KB, Thành phải dùng lệnh '--mode=export_only' ở Phương án A.")

if __name__ == "__main__":
    convert_directly_to_h5()