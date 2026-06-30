<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 28px 34px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: #1f2937;
            font-family: "DejaVu Sans", sans-serif;
            font-size: 13px;
            line-height: 1.55;
        }

        .top-rule {
            height: 6px;
            margin-bottom: 20px;
            background: #2563eb;
        }

        .header-table,
        .info-table,
        .analysis-table,
        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }

        .brand {
            color: #2563eb;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: .5px;
            text-transform: uppercase;
        }

        .doc-code {
            color: #6b7280;
            font-size: 11px;
            text-align: right;
        }

        .title {
            margin: 18px 0 8px;
            color: #111827;
            font-size: 25px;
            font-weight: 800;
            letter-spacing: .3px;
            text-align: center;
            text-transform: uppercase;
        }

        .date {
            margin: 0 0 22px;
            color: #374151;
            font-size: 13px;
            text-align: center;
        }

        .section-title {
            margin: 20px 0 8px;
            color: #111827;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: .4px;
            text-transform: uppercase;
        }

        .info-card,
        .note-card {
            border: 1px solid #dbe3ef;
            border-radius: 8px;
            overflow: hidden;
        }

        .info-table td {
            width: 50%;
            padding: 10px 12px;
            border-bottom: 1px solid #edf2f7;
            vertical-align: top;
        }

        .info-table tr:last-child td {
            border-bottom: 0;
        }

        .label {
            display: block;
            color: #6b7280;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .5px;
            text-transform: uppercase;
        }

        .value {
            color: #111827;
            font-size: 14px;
            font-weight: 700;
        }

        .analysis-table {
            margin-top: 10px;
        }

        .image-panel,
        .result-panel {
            border: 1px solid #dbe3ef;
            border-radius: 8px;
            vertical-align: top;
        }

        .image-panel {
            width: 43%;
            padding: 10px;
            text-align: center;
        }

        .result-panel {
            width: 57%;
            padding: 16px 18px;
        }

        .panel-gap {
            width: 12px;
        }

        .image-title {
            margin-bottom: 8px;
            color: #374151;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .4px;
            text-transform: uppercase;
        }

        .scan-image {
            max-width: 100%;
            max-height: 250px;
            border: 1px solid #e5e7eb;
            padding: 5px;
        }

        .empty-image {
            height: 230px;
            padding-top: 92px;
            border: 1px dashed #cbd5e1;
            color: #94a3b8;
            font-size: 12px;
            text-align: center;
        }

        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .5px;
            text-transform: uppercase;
        }

        .badge-danger {
            background: #fee2e2;
            color: #b91c1c;
        }

        .badge-success {
            background: #dcfce7;
            color: #15803d;
        }

        .badge-warning {
            background: #ffedd5;
            color: #c2410c;
        }

        .result-heading {
            margin: 14px 0 6px;
            font-size: 21px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .danger {
            color: #b91c1c;
        }

        .success {
            color: #15803d;
        }

        .warning {
            color: #c2410c;
        }

        .prediction {
            margin-bottom: 16px;
            color: #6b7280;
            font-size: 12px;
        }

        .confidence-row {
            width: 100%;
            margin-top: 8px;
            border-collapse: collapse;
        }

        .confidence-row td {
            padding: 0;
            vertical-align: middle;
        }

        .confidence-label {
            color: #374151;
            font-weight: 800;
        }
        .assessment-text {
            white-space: pre-line;
        }

        .confidence-number {
            color: #111827;
            font-size: 24px;
            font-weight: 800;
            text-align: right;
        }

        .bar {
            height: 9px;
            margin-top: 8px;
            border-radius: 999px;
            background: #e5e7eb;
            overflow: hidden;
        }

        .bar-fill {
            height: 9px;
            border-radius: 999px;
        }

        .bar-danger {
            background: #dc2626;
        }

        .bar-success {
            background: #16a34a;
        }

        .bar-warning {
            background: #f97316;
        }

        .note-card {
            margin-top: 22px;
            padding: 14px 16px;
            background: #f8fafc;
        }

        .note-title {
            margin-bottom: 4px;
            color: #111827;
            font-weight: 800;
        }

        .sign-space {
            height: 64px;
        }

        .signature-table {
            margin-top: 34px;
        }

        .signature-table td {
            width: 50%;
            vertical-align: top;
        }

        .signature {
            text-align: center;
            font-style: italic;
        }

        .doctor-name {
            margin-top: 6px;
            color: #111827;
            font-style: normal;
            font-weight: 700;
        }

        .small {
            color: #6b7280;
            font-size: 11px;
        }
    </style>
</head>
<body>
    @php
        $prediction = (string) $scan->prediction;
        $resultTone = \App\Support\ScanAssessment::tone($prediction);
        $badgeClass = $resultTone === 'danger' ? 'badge-danger' : ($resultTone === 'warning' ? 'badge-warning' : 'badge-success');
        $barClass = $resultTone === 'danger' ? 'bar-danger' : ($resultTone === 'warning' ? 'bar-warning' : 'bar-success');
        $resultText = \App\Support\ScanAssessment::vietnameseStatusLabel($prediction);
        $confidenceValue = $scan->confidence_score ?? $scan->confidence ?? 0;
        $confidencePercent = $confidenceValue <= 1 ? $confidenceValue * 100 : $confidenceValue;
        $confidencePercent = max(0, min(100, $confidencePercent));
        $assessment = \App\Support\ScanAssessment::clinicalRecordComment($prediction, $confidencePercent);
    @endphp

    <div class="top-rule"></div>

    <table class="header-table">
        <tr>
            <td>
                <div class="brand">Hệ thống AI hỗ trợ chẩn đoán ung thư phổi</div>
                <div class="small">Phiếu kết quả phân tích hình ảnh y khoa</div>
            </td>
            <td class="doc-code">
                Mã phiếu: KQ-{{ str_pad((string) $scan->id, 5, '0', STR_PAD_LEFT) }}<br>
                Ngày thực hiện: {{ $date }}
            </td>
        </tr>
    </table>

    <h1 class="title">{{ $title }}</h1>
    <p class="date">Kết quả hỗ trợ chẩn đoán được tạo bởi mô hình TensorFlow Flask</p>
    <div class="section-title">Thông tin bệnh nhân</div>
    <div class="info-card">
        <table class="info-table">
            <tr>
                <td>
                    <span class="label">Mã bệnh nhân</span>
                    <span class="value">{{ $patient->patient_code }}</span>
                </td>
                <td>
                    <span class="label">Họ tên</span>
                    <span class="value">{{ $patient->name }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="label">Giới tính</span>
                    <span class="value">{{ $patient->gender }}</span>
                </td>
                <td>
                    <span class="label">Tuổi</span>
                    <span class="value">{{ $patient->age }}</span>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <span class="label">Bác sĩ chẩn đoán</span>
                    <span class="value">{{ $scan->doctor->name ?? 'Chưa ghi nhận' }}</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title">Kết quả phân tích hình ảnh</div>
    <table class="analysis-table">
        <tr>
            <td class="image-panel">
                <div class="image-title">Ảnh X-quang đã phân tích</div>
                @if(!empty($scanImagePath))
                    <img class="scan-image" src="{{ $scanImagePath }}" alt="Ảnh đã phân tích">
                @else
                    <div class="empty-image">Không tìm thấy ảnh trong hệ thống</div>
                @endif
            </td>
            <td class="panel-gap"></td>
            <td class="result-panel">
                <span class="badge {{ $badgeClass }}">Kết quả AI</span>
                <div class="result-heading {{ $resultTone }}">{{ $resultText }}</div>
                <div class="prediction">Nhãn dự đoán: <strong>{{ mb_strtoupper($prediction) }}</strong></div>

                <table class="confidence-row">
                    <tr>
                        <td class="confidence-label">Độ tin cậy AI</td>
                        <td class="confidence-number">{{ number_format($confidencePercent, 1) }}%</td>
                    </tr>
                </table>
                <div class="bar">
                    <div class="bar-fill {{ $barClass }}" style="width: {{ $confidencePercent }}%;"></div>
                </div>
            </td>
        </tr>
    </table>

    <div class="note-card">
        <div class="note-title">Đánh giá, nhận xét và tư vấn chuyên môn</div>
        <div class="assessment-text">{{ $assessment }}</div>
    </div>

    <div class="note-card">
        <div class="note-title">Ghi chú lâm sàng</div>
        Phiếu kết quả này được tạo tự động bởi hệ thống AI hỗ trợ quyết định y khoa. Đây không phải là chẩn đoán cuối cùng và cần được bác sĩ có chuyên môn diễn giải.
    </div>

    <table class="signature-table">
        <tr>
            <td></td>
            <td class="signature">
                Bác sĩ phụ trách 
                <div class="sign-space"></div>
                <div class="doctor-name">{{ $scan->doctor->name ?? '' }}</div>
                <div>(Ký và ghi rõ họ tên)</div>
            </td>
        </tr>
    </table>
</body>
</html>
