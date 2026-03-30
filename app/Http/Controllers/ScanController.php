<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ScanResult;
use App\Models\Patient;
use Illuminate\Support\Facades\Storage;

class ScanController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validate dữ liệu
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $patient = Patient::findOrFail($request->patient_id);

        // 2. Lưu file ảnh vào thư mục storage/app/public/scans
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('scans', 'public');

            // 3. GIẢ LẬP GỌI AI (Sau này Thành sẽ thay bằng logic gọi API Flask/Python)
            // Ở đây mình random kết quả để Thành test giao diện trước
            $predictions = ['Malignant (Ác tính)', 'Benign (Lành tính)', 'Normal (Bình thường)'];
            $result = $predictions[array_rand($predictions)];
            $confidence = rand(85, 99);     
            // 4. Lưu vào Database
            $scan = ScanResult::create([
                'patient_id' => $patient->id,
                'image_path' => $path,
                'prediction' => $result,
                'confidence_score' => $confidence,
                'status' => 'Completed',
            ]);

            return redirect()->route('patients.show', $patient->id)
                             ->with('success', 'Đã phân tích xong hình ảnh cho bệnh nhân!');
        }

        return back()->with('error', 'Có lỗi xảy ra khi tải ảnh lên.');
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