<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\ScanResult; 
use App\Models\Patient;
use Illuminate\Support\Facades\Log;

class ScanController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validate dữ liệu đầu vào
        $request->validate([
            'xray_image' => 'required|image|mimes:jpeg,png,jpg|max:10240', // Giới hạn 10MB
            'patient_id' => 'required|exists:patients,id', // Đảm bảo bệnh nhân có tồn tại
        ]);

        // 2. Lưu file ảnh vào thư mục storage/app/public/scans
        $file = $request->file('xray_image');
        $path = $file->store('scans', 'public'); 

        try {
            // 3. Gọi đến AI Service (Flask)
            // Tăng timeout lên 60s phòng trường hợp model EfficientNet xử lý chậm trên CPU
            $response = Http::timeout(60)->attach(
                'file', 
                file_get_contents($file->getRealPath()), 
                $file->getClientOriginalName()
            )->post('http://127.0.0.1:5000/predict');

            // 4. Kiểm tra phản hồi từ Flask
            if ($response->successful()) {
                $aiResult = $response->json();
                
                // Lưu kết quả vào Database
                $scan = ScanResult::create([
                    'patient_id'       => $request->patient_id,
                    'image_path'       => $path,
                    'prediction'       => $aiResult['prediction'] ?? 'Unknown',
                    // Chuyển xác suất thành % (ví dụ 0.98 -> 98.00)
                    'confidence_score' => isset($aiResult['confidence']) ? round($aiResult['confidence'] * 100, 2) : 0,
                    // Nếu bạn có cột lưu raw_scores thì có thể thêm:
                    // 'raw_data'      => json_encode($aiResult['raw_scores'] ?? []),
                ]);

                return redirect()->route('scans.analysis', $scan->id)
                                 ->with('success', 'Phân tích ảnh thành công!');
            }

            // Trường hợp Flask trả về lỗi (404, 500...)
            return back()->withErrors(['api_error' => 'Dịch vụ AI đang bận hoặc có lỗi (Status: ' . $response->status() . ')']);

        } catch (\Exception $e) {
            // Ghi log lỗi để dev kiểm tra
            Log::error("Lỗi kết nối AI Service: " . $e->getMessage());

            return back()->withErrors(['conn_error' => 'Không thể kết nối đến máy chủ AI. Hãy đảm bảo file ai_service.py đang chạy!']);
        }
    }

    public function analysis($id)
    {
        // Sử dụng eager loading để lấy luôn thông tin bệnh nhân, tránh lỗi N+1
        $scan = ScanResult::with('patient')->findOrFail($id);
        
        return view('scans.analysis', compact('scan'));
    }
     public function destroy(ScanResult $scan)
{
    // 1. Xóa file ảnh vật lý trong thư mục storage để tiết kiệm bộ nhớ
    if ($scan->image_path && \Storage::disk('public')->exists($scan->image_path)) {
        \Storage::disk('public')->delete($scan->image_path);
    }

    // 2. Xóa dữ liệu trong database
    $scan->delete();

    return back()->with('success', 'Đã xóa kết quả chẩn đoán thành công!');
}
    
}