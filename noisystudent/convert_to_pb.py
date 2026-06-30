import tensorflow as tf
import os
import sys
from absl import flags
import efficientnet_builder

# 1. Đăng ký Flags để tránh lỗi AttributeError
FLAGS = flags.FLAGS
def register_flags():
    flag_defs = {
        'small_image_model': (False, 'Sử dụng cho ảnh nhỏ'),
        'data_format': ('channels_last', 'Định dạng dữ liệu'),
        'fix_layer_num': (-1, 'Số lượng layer cố định'),
        'use_adv_bn': (False, 'Sử dụng Adversarial Batch Norm'),
        'is_teacher': (False, 'Chế độ mô hình giáo viên')
    }
    for name, (default, help_str) in flag_defs.items():
        if name not in FLAGS:
            if isinstance(default, bool): flags.DEFINE_boolean(name, default, help_str)
            elif isinstance(default, int): flags.DEFINE_integer(name, default, help_str)
            else: flags.DEFINE_string(name, default, help_str)

register_flags()

# 2. Cấu hình đường dẫn tuyệt đối
# Đảm bảo đường dẫn này khớp với cấu trúc thư mục trên máy bạn
BASE_DIR = r'C:\xampp\htdocs\Do-an-Laravel\lung-cancer-detection\noisystudent'
CKPT_PATH = os.path.join(BASE_DIR, 'model_output', 'model.ckpt-5000')
EXPORT_DIR = os.path.join(BASE_DIR, 'exported_model')
MODEL_NAME = 'efficientnet-b0'

def export():
    # Kiểm tra file checkpoint trước khi chạy
    if not os.path.exists(CKPT_PATH + '.index'):
        print(f"[LỖI] Không tìm thấy checkpoint tại: {CKPT_PATH}.index")
        return

    if not FLAGS.is_parsed():
        FLAGS(sys.argv)

    # Tắt eager để dùng đồ thị tĩnh (TF 1.x)
    tf.compat.v1.disable_eager_execution()
    
    # Định nghĩa Input (Size chuẩn 224x224 cho B0)
    inputs = tf.compat.v1.placeholder(tf.float32, [None, 224, 224, 3], name='input_tensor')
    
    print(f"--- Đang khởi tạo mô hình {MODEL_NAME} ---")
    with tf.compat.v1.variable_scope('model'):
        # override_params={'num_classes': 2} khớp với bài toán ung thư phổi của bạn
        logits, _ = efficientnet_builder.build_model(
            inputs, 
            model_name=MODEL_NAME, 
            training=False,
            override_params={'num_classes': 2} 
        )
    
    sess = tf.compat.v1.Session()
    sess.run(tf.compat.v1.global_variables_initializer())

    print(f"--- Đang nạp trọng số từ: {os.path.basename(CKPT_PATH)} ---")
    ckpt_vars = {name: shape for name, shape in tf.train.list_variables(CKPT_PATH)}
    curr_vars = tf.compat.v1.global_variables()

    assignment_map = {}
    for v in curr_vars:
        v_full_name = v.op.name
        # Tỉa tiền tố 'model/' để map đúng với tên biến trong checkpoint
        clean_name = v_full_name[6:] if v_full_name.startswith('model/') else v_full_name
        for c_name in ckpt_vars:
            if c_name == clean_name or c_name.endswith(clean_name) or clean_name.endswith(c_name):
                assignment_map[c_name] = v
                break

    # Nạp trọng số cưỡng bức để xử lý bfloat16 và Shape mismatch
    count = 0
    for c_name, v_obj in assignment_map.items():
        try:
            tensor_value = tf.train.load_variable(CKPT_PATH, c_name)
            v_shape = v_obj.get_shape().as_list()
            t_shape = list(tensor_value.shape)
            
            if v_shape == t_shape:
                sess.run(tf.compat.v1.assign(v_obj, tensor_value.astype('float32')))
                count += 1
        except Exception:
            continue

    print(f"--- Nạp thành công {count} biến ---")

    # 3. Xuất mô hình (Xử lý lỗi đường dẫn Windows)
    if os.path.exists(EXPORT_DIR):
        import shutil
        print(f"--- Dọn dẹp thư mục cũ: {EXPORT_DIR} ---")
        shutil.rmtree(EXPORT_DIR)
    
    # Tạo lại thư mục sạch trước khi lưu
    os.makedirs(EXPORT_DIR)

    print(f"--- Đang lưu SavedModel tại: {EXPORT_DIR} ---")
    try:
        tf.compat.v1.saved_model.simple_save(
            sess, 
            EXPORT_DIR,
            inputs={'input': inputs}, 
            outputs={'output': logits}
        )
        print("\n" + "="*40)
        print("XUẤT MÔ HÌNH THÀNH CÔNG!")
        print(f"Thư mục: {EXPORT_DIR}")
        print("="*40)
    except Exception as e:
        print(f"\n[LỖI]: {e}")

if __name__ == '__main__':
    export()