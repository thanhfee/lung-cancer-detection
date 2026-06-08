<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Bao cao ket qua chan doan</title>
</head>
<body style="margin:0;background:#f4f8fb;font-family:Arial,Helvetica,sans-serif;color:#0f172a;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f8fb;padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="640" cellspacing="0" cellpadding="0" style="max-width:640px;width:100%;background:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #dbeafe;">
                    <tr>
                        <td style="background:#06488f;padding:22px 28px;color:#ffffff;">
                            <div style="font-size:12px;font-weight:700;letter-spacing:2px;text-transform:uppercase;">LungCare AI</div>
                            <h1 style="margin:8px 0 0;font-size:22px;line-height:1.35;">Bao cao ket qua chan doan hinh anh</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px;">
                            <p style="margin:0 0 16px;font-size:15px;line-height:1.7;">Xin chao {{ $patient->name }},</p>
                            <p style="margin:0 0 18px;font-size:15px;line-height:1.7;">
                                He thong gui kem file PDF ket qua phan tich anh scan cua ban. Vui long tai file dinh kem de xem chi tiet.
                            </p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:20px 0;border-collapse:collapse;background:#f8fafc;border-radius:10px;overflow:hidden;">
                                <tr>
                                    <td style="padding:12px 14px;font-size:13px;font-weight:700;color:#64748b;border-bottom:1px solid #e2e8f0;">Ma benh nhan</td>
                                    <td style="padding:12px 14px;font-size:13px;font-weight:700;color:#0f172a;border-bottom:1px solid #e2e8f0;">{{ $patient->patient_code }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 14px;font-size:13px;font-weight:700;color:#64748b;border-bottom:1px solid #e2e8f0;">Ket qua AI</td>
                                    <td style="padding:12px 14px;font-size:13px;font-weight:700;color:#0f172a;border-bottom:1px solid #e2e8f0;">{{ $scan->prediction }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 14px;font-size:13px;font-weight:700;color:#64748b;">Thoi gian scan</td>
                                    <td style="padding:12px 14px;font-size:13px;font-weight:700;color:#0f172a;">{{ $scan->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>

                            <p style="margin:18px 0 0;font-size:13px;line-height:1.7;color:#64748b;">
                                Luu y: Ket qua AI chi co gia tri tham khao, khong thay the chan doan va tu van truc tiep tu bac si chuyen khoa.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 28px;background:#e0f2fe;color:#075985;font-size:12px;font-weight:700;">
                            Hotline: 0394921897 | Email: crosszmagmajelly@gmail.com
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
