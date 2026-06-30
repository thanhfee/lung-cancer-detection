import tensorflow.compat.v1 as tf
import numpy as np
import os

# Cấu hình để dùng mode 1.x
tf.disable_v2_behavior()

def predict_lung_cancer(image_path):
    # Đường dẫn đến model_output của Thành
    model_dir = r'C:\xampp\htdocs\Do-an-Laravel\lung-cancer-detection\noisystudent\model_output'
    
    # Khởi tạo session và load model từ checkpoint
    with tf.Session() as sess:
        # Load meta graph (cấu trúc mạng)
        saver = tf.train.import_meta_graph(os.path.join(model_dir, 'model.ckpt-5000.meta'))
        # Load trọng số đã train
        saver.restore(sess, os.path.join(model_dir, 'model.ckpt-5000'))
        
        # Tiền xử lý ảnh (Resize 224x224 giống lúc train)
        img_raw = tf.io.read_file(image_path)
        img_tensor = tf.image.decode_image(img_raw, channels=3)
        img_final = tf.image.resize(img_tensor, [224, 224])
        img_final = tf.expand_dims(img_final, 0).eval() # Chuyển thành mảng numpy

        # Lấy các input/output tensor (Thành cần kiểm tra tên tensor trong code main.py)
        # Thông thường là 'input:0' và 'logits:0' hoặc 'final_ret:0'
        graph = tf.get_default_graph()
        input_x = graph.get_tensor_by_name("input:0") 
        logits = graph.get_tensor_by_name("logits:0")

        prediction = sess.run(logits, feed_dict={input_x: img_final})
        res = np.argmax(prediction)
        
        labels = {0: "Lanh tinh/Binh thuong", 1: "Ung thu (Malignant)"}
        return labels[res]

# Chạy thử với 1 tấm ảnh trong bộ dữ liệu của Thành
print("--- KET QUA CHAN DOAN ---")
print(predict_lung_cancer(r'DUONG_DAN_DEN_ANH_TEST.png'))