<?php

namespace App\Http\Controllers;

use App\Models\ScanResult;
use App\Models\Patient; // Đã thêm dòng này
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ScanController extends Controller
{
    /**
     * Hiển thị giao diện Form để tải ảnh X-quang lên quét
     */
    public function showScanForm($id)
    {
        // Tìm bệnh nhân theo ID, nếu không thấy tự động trả về lỗi 404
        $patient = Patient::findOrFail($id);

        // Trả về giao diện blade hiển thị form quét ảnh
        // (Lưu ý: Hãy đảm bảo bạn có file view tại đường dẫn resources/views/scans/create.blade.php
        // Hoặc sửa lại 'scans.create' thành tên view thực tế của nhóm bạn nhé)
        return view('patients.scan', compact('patient'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'xray_image' => 'required|image|mimes:jpeg,png,jpg|max:10240',
            'patient_id' => 'required|exists:patients,id',
        ]);

        $file = $request->file('xray_image');
        $path = $file->store('scans', 'public');

        try {
            $response = Http::timeout(60)->attach(
                'file',
                file_get_contents($file->getRealPath()),
                $file->getClientOriginalName()
            )->post('http://127.0.0.1:5000/predict');

            if ($response->successful()) {
                $aiResult = $response->json();
                $confidence = (float) ($aiResult['confidence'] ?? 0);
                $confidence = $confidence > 1 ? $confidence / 100 : $confidence;

                $scan = ScanResult::create([
                    'patient_id' => $request->patient_id,
                    'doctor_id' => $request->user()?->id,
                    'image_path' => $path,
                    'prediction' => $aiResult['prediction'] ?? 'Unknown',
                    'confidence_score' => round(max(0, min(1, $confidence)), 4),
                ]);

                return redirect()
                    ->route('scans.analysis', $scan->id)
                    ->with('success', 'Phan tich anh thanh cong!');
            }

            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            $aiError = $response->json();
            if ($response->status() === 422 && ($aiError['error'] ?? null) === 'INVALID_XRAY_IMAGE') {
                return back()->withErrors([
                    'api_error' => $aiError['message'] ?? 'Hệ thống chỉ nhận ảnh X-quang ngực. Vui lòng tải lên đúng ảnh X-quang.',
                ]);
            }

            return back()->withErrors([
                'api_error' => $aiError['message'] ?? ('Dich vu AI dang ban hoac co loi (Status: ' . $response->status() . ')'),
            ]);
        } catch (\Exception $e) {
            if (isset($path) && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            Log::error('Loi ket noi AI Service: ' . $e->getMessage());

            return back()->withErrors([
                'conn_error' => 'Khong the ket noi den may chu AI. Hay dam bao file ai_service.py dang chay!',
            ]);
        }
    }

    public function analysis($id)
    {
        $scan = ScanResult::with(['patient', 'doctor'])->findOrFail($id);

        return view('scans.analysis', compact('scan'));
    }

    public function destroy(ScanResult $scan)
    {
        if ($scan->image_path && Storage::disk('public')->exists($scan->image_path)) {
            Storage::disk('public')->delete($scan->image_path);
        }

        $scan->delete();

        return back()->with('success', 'Da xoa ket qua chan doan thanh cong!');
    }
}
