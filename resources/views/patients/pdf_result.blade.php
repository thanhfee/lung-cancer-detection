<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 14px; }
        .header { text-align: center; text-transform: uppercase; margin-bottom: 30px; }
        .hospital-name { font-weight: bold; color: #1a73e8; }
        .result-box { 
            padding: 20px; 
            border: 2px solid; 
            border-radius: 10px; 
            margin-top: 20px;
            text-align: center;
        }
        .cancer { border-color: #d32f2f; color: #d32f2f; background: #fff5f5; }
        .no-cancer { border-color: #388e3c; color: #388e3c; background: #f1f8e9; }
        .footer { margin-top: 50px; text-align: right; font-style: italic; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        td { padding: 8px; border-bottom: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="header">
        <h3 class="hospital-name">HỆ THỐNG CHẨN ĐOÁN UNG THƯ PHỔI AI</h3>
        <h2>{{ $title }}</h2>
        <p>Ngày thực hiện: {{ $date }}</p>
    </div>

    <h4>THÔNG TIN BỆNH NHÂN</h4>
    <table>
        <tr><td>Mã bệnh nhân: <strong>{{ $patient->patient_code }}</strong></td><td>Giới tính: {{ $patient->gender }}</td></tr>
        <tr><td>Họ tên: <strong>{{ $patient->name }}</strong></td><td>Tuổi: {{ $patient->age }}</td></tr>
        <tr><td colspan="2">Bac si chan doan: <strong>{{ $scan->doctor->name ?? 'Chua ghi nhan' }}</strong></td></tr>
    </table>

    <div class="result-box {{ $scan->prediction == 'Cancer' ? 'cancer' : 'no-cancer' }}">
        <h3>KẾT QUẢ PHÂN TÍCH: {{ mb_strtoupper($scan->prediction) }}</h3>
        <p>Độ tin cậy của AI: <strong>{{ $scan->confidence }}%</strong></p>
    </div>

    <div style="margin-top: 30px;">
        <p><strong>Ghi chú:</strong> Kết quả này được phân tích tự động bằng mô hình Deep Learning ResNet50. Vui lòng tham khảo ý kiến bác sĩ chuyên khoa để có kết luận cuối cùng.</p>
    </div>

    <div class="footer">
        <p>Bác sĩ phụ trách chẩn đoán</p>
        <br><br><br>
        <p>{{ $scan->doctor->name ?? '' }}</p>
        <p>(Ký và ghi rõ họ tên)</p>
    </div>
</body>
</html>
