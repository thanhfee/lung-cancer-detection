🩺 Trợ lý Phân tích & Phát hiện Ung thư Phổi (Lung Cancer Detection)
<p align="center">
<img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo">
</p>

📝 Giới thiệu dự án
Dự án được xây dựng nhằm hỗ trợ bác sĩ trong việc phân tích hồ sơ bệnh án và hỗ trợ chẩn đoán ung thư phổi. Hệ thống kết hợp giữa nền tảng Web mạnh mẽ của Laravel và trí tuệ nhân tạo từ Google Gemini AI.

Chức năng chính:

Quản lý bệnh án: Lưu trữ và quản lý thông tin bệnh nhân một cách hệ thống.

Trợ lý AI (Gemini 2.0 Flash): Phân tích các triệu chứng và cung cấp gợi ý y tế chuyên sâu.

Dashboard: Giao diện trực quan dành cho bác sĩ (Bác sĩ Thành).

Bảo mật: Áp dụng các tiêu chuẩn OWASP để bảo vệ dữ liệu bệnh nhân.

🛠 Công nghệ sử dụng
Backend: Laravel 10.x / 11.x (PHP 8.2)

Database: MySQL (XAMPP)

AI Integration: Google Gemini API (Generative AI)

Frontend: Blade Template, Tailwind CSS / Bootstrap

Environment: Windows (Visual Studio Code, Unity Hub cho các module mở rộng)

🚀 Hướng dẫn cài đặt
1. Yêu cầu hệ thống
PHP >= 8.2

Composer

XAMPP (MySQL & Apache)

2. Cài đặt các bước
Clone dự án:

Bash
git clone https://github.com/thanhfee/lung-cancer-detection.git
cd lung-cancer-detection
Cài đặt thư viện PHP:

Bash
composer install
Cấu hình môi trường (.env):

Sao chép file mẫu: cp .env.example .env

Cấu hình Database trong .env:

Đoạn mã
DB_DATABASE=lung_cancer_db
DB_USERNAME=root
DB_PASSWORD=
Quan trọng: Thêm API Key của Gemini:

Đoạn mã
GEMINI_API_KEY=AIzaSy...
GEMINI_MODEL=gemini-2.0-flash-lite
Khởi tạo Database:

Bash
php artisan migrate --seed
Chạy ứng dụng:

Bash
php artisan serve
Truy cập: http://127.0.0.1:8000

📂 Cấu trúc thư mục quan trọng
app/Http/Controllers/PatientController.php: Xử lý logic chính và kết nối Gemini AI.

routes/web.php: Định nghĩa các luồng xử lý của hệ thống.

resources/views/dashboard/: Giao diện làm việc của bác sĩ.

ai_service/: (Nếu có) Chứa các scripts Python bổ trợ phân tích hình ảnh.

🛡 Bảo mật & Nguyên tắc thiết kế
Dự án được thiết kế tuân thủ các nguyên tắc SOLID và các tiêu chuẩn bảo mật API:

BOLA/BFLA Protection: Kiểm tra quyền sở hữu bản ghi bệnh nhân.

Input Validation: Ngăn chặn SQL Injection và XSS.

Rate Limiting: Tối ưu hóa số lượng request gửi đến API Gemini.

👨‍💻 Tác giả
Sinh viên thực hiện: Bác sĩ Thành (Software Developer & Medical Researcher)

Đồ án: Phát hiện và phân tích Ung thư phổi - Laravel Framework.

💡 Lưu ý cho người dùng
Khi cài đặt lại môi trường Python trong thư mục ai_service, vui lòng tạo venv mới và chạy pip install -r requirements.txt để đảm bảo các thư viện như TensorFlow hoạt động chính xác.
