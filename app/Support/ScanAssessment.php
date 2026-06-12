<?php

namespace App\Support;

class ScanAssessment
{
    public static function statusLabel(string $prediction): string
    {
        $tone = self::tone($prediction);

        return match ($tone) {
            'danger' => 'Abnormal finding detected',
            'warning' => 'Further review recommended',
            default => 'No obvious abnormality detected',
        };
    }

    public static function vietnameseStatusLabel(string $prediction): string
    {
        $tone = self::tone($prediction);

        return match ($tone) {
            'danger' => 'Phát hiện dấu hiệu bất thường nghi ngờ',
            'warning' => 'Cần đánh giá thêm bởi bác sĩ chuyên khoa',
            default => 'Chưa ghi nhận dấu hiệu bất thường rõ ràng',
        };
    }

    public static function summary(string $prediction, float|int|null $confidence = null): string
    {
        $confidenceText = self::confidenceText($confidence);
        $tone = self::tone($prediction);

        return match ($tone) {
            'danger' => "The AI model detected imaging features that may be associated with a suspicious lung abnormality{$confidenceText}. This result should be reviewed by a radiologist or pulmonology specialist together with the patient's symptoms, risk factors, and prior imaging.",
            'warning' => "The AI model could not classify the image with high certainty{$confidenceText}. Image quality, positioning, or subtle findings may limit automated interpretation, so a specialist review is recommended.",
            default => "The AI model did not detect obvious suspicious lung findings on this image{$confidenceText}. This does not replace clinical assessment, especially if the patient has symptoms or significant risk factors.",
        };
    }

    public static function recommendation(string $prediction): string
    {
        $tone = self::tone($prediction);

        return match ($tone) {
            'danger' => 'Recommended next steps: arrange specialist consultation, compare with previous imaging if available, and consider confirmatory chest CT or additional tests according to clinical judgement.',
            'warning' => 'Recommended next steps: repeat or improve imaging if needed, request radiology review, and correlate the result with clinical findings before making treatment decisions.',
            default => 'Recommended next steps: continue routine follow-up as clinically indicated, maintain risk-factor control, and seek medical review if respiratory symptoms persist or worsen.',
        };
    }

    public static function fullComment(string $prediction, float|int|null $confidence = null): string
    {
        return self::summary($prediction, $confidence) . "\n\n" . self::recommendation($prediction);
    }

    public static function clinicalRecordComment(string $prediction, float|int|null $confidence = null): string
    {
        $confidenceText = self::vietnameseConfidenceText($confidence);
        $tone = self::tone($prediction);

        return match ($tone) {
            'danger' => "Đánh giá: AI ghi nhận các đặc điểm hình ảnh có thể liên quan đến bất thường nghi ngờ tại phổi{$confidenceText}. Cần xem đây là kết quả hỗ trợ sàng lọc, không thay thế kết luận chẩn đoán của bác sĩ.\n\nNhận xét chuyên môn: Nên đối chiếu với triệu chứng lâm sàng, tiền sử hút thuốc, tiền sử ung thư, kết quả xét nghiệm và phim chụp cũ nếu có. Trường hợp có ho, khó thở, đau ngực, sụt cân, ho ra máu hoặc nguy cơ cao cần được ưu tiên đánh giá sớm.\n\nTư vấn: Khuyến nghị hội chẩn bác sĩ chuyên khoa hô hấp/chẩn đoán hình ảnh, cân nhắc chụp CT ngực liều thích hợp hoặc các xét nghiệm bổ sung theo chỉ định. Không tự ý kết luận ung thư chỉ dựa trên kết quả AI.",
            'warning' => "Đánh giá: AI chưa đủ khả năng kết luận chắc chắn từ ảnh này{$confidenceText}. Nguyên nhân có thể đến từ chất lượng ảnh, tư thế chụp, vùng tổn thương nhỏ hoặc dấu hiệu hình ảnh chưa điển hình.\n\nNhận xét chuyên môn: Cần bác sĩ chuyên khoa xem lại trực tiếp ảnh chụp và đối chiếu với tình trạng lâm sàng của bệnh nhân. Nếu triệu chứng còn tiếp diễn hoặc bệnh nhân có yếu tố nguy cơ, không nên bỏ qua việc theo dõi tiếp.\n\nTư vấn: Khuyến nghị chụp lại ảnh nếu chất lượng chưa đạt, gửi phim cho khoa chẩn đoán hình ảnh hoặc chuyên khoa hô hấp, và cân nhắc CT ngực khi bác sĩ thấy cần thiết.",
            default => "Đánh giá: AI chưa phát hiện dấu hiệu bất thường rõ ràng trên ảnh phổi này{$confidenceText}. Kết quả này có giá trị hỗ trợ sàng lọc, nhưng không loại trừ hoàn toàn bệnh lý nếu bệnh nhân có triệu chứng hoặc nguy cơ cao.\n\nNhận xét chuyên môn: Nên tiếp tục theo dõi lâm sàng, đặc biệt với người hút thuốc, người lớn tuổi, có tiền sử gia đình ung thư phổi, ho kéo dài, đau ngực, khó thở hoặc sụt cân không rõ nguyên nhân.\n\nTư vấn: Duy trì tái khám theo lịch, kiểm soát yếu tố nguy cơ, ngừng hút thuốc nếu có, và liên hệ bác sĩ khi triệu chứng xuất hiện hoặc nặng lên.",
        };
    }

    public static function tone(string $prediction): string
    {
        $value = strtolower($prediction);

        if (str_contains($value, 'malignant') || str_contains($value, 'cancer') || str_contains($value, 'abnormal')) {
            return 'danger';
        }

        if (str_contains($value, 'uncertain') || str_contains($value, 'review')) {
            return 'warning';
        }

        return 'success';
    }

    private static function confidenceText(float|int|null $confidence): string
    {
        if ($confidence === null) {
            return '';
        }

        $percent = $confidence <= 1 ? $confidence * 100 : $confidence;
        $percent = max(0, min(100, $percent));

        return ' with an estimated confidence of ' . number_format($percent, 1) . '%';
    }

    private static function vietnameseConfidenceText(float|int|null $confidence): string
    {
        if ($confidence === null) {
            return '';
        }

        $percent = $confidence <= 1 ? $confidence * 100 : $confidence;
        $percent = max(0, min(100, $percent));

        return ' với độ tin cậy ước tính ' . number_format($percent, 1) . '%';
    }
}
