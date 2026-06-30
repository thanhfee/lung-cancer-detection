import tensorflow.compat.v1 as tf
import os
import cv2

# Đường dẫn dữ liệu của Thành
IMAGE_DIR = 'c:/xampp/htdocs/noisystudent/data/images' # Thư mục chứa các folder con 'normal', 'sick'
OUTPUT_PATH = 'c:/xampp/htdocs/noisystudent/data/train.tfrecord'
IMG_SIZE = 224 # Kích thước chuẩn cho EfficientNet

def _bytes_feature(value):
    return tf.train.Feature(bytes_list=tf.train.BytesList(value=[value]))

def _int64_feature(value):
    return tf.train.Feature(int64_list=tf.train.Int64List(value=[value]))

def create_tfrecord():
    writer = tf.io.TFRecordWriter(OUTPUT_PATH)
    labels_map = {'normal': 0, 'sick': 1} # Định nghĩa nhãn
    
    count = 0
    for label_name, label_id in labels_map.items():
        folder_path = os.path.join(IMAGE_DIR, label_name)
        if not os.path.exists(folder_path): continue
        
        for img_name in os.listdir(folder_path):
            img_path = os.path.join(folder_path, img_name)
            try:
                # Đọc và resize ảnh
                img = cv2.imread(img_path)
                img = cv2.resize(img, (IMG_SIZE, IMG_SIZE))
                img_str = cv2.imencode('.jpg', img)[1].tostring()
                
                # Tạo bản ghi
                feature = {
                    'image/encoded': _bytes_feature(img_str),
                    'image/class/label': _int64_feature(label_id),
                }
                
                example = tf.train.Example(features=tf.train.Features(feature=feature))
                writer.write(example.SerializeToString())
                count += 1
            except Exception as e:
                print(f"Lỗi ảnh {img_name}: {e}")
                
    writer.close()
    print(f"Đã tạo xong TFRecord với {count} mẫu tại {OUTPUT_PATH}")

if __name__ == "__main__":
    create_tfrecord()
