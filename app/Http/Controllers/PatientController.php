<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\ScanResult;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; 
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Gemini\Laravel\Facades\Gemini;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $patients = Patient::with('scans')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                             ->orWhere('patient_code', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10);

        return view('patients.index', compact('patients'));
    }

    public function show($id)
    {
        $patient = Patient::with(['scans' => function($query) {
            $query->latest();
        }])->findOrFail($id);

        return view('patients.show', compact('patient'));
    }

    public function scan($id)
    {
        $patient = Patient::findOrFail($id);
        return view('patients.scan', compact('patient'));
    }

    public function create()
    {
        return view('patients.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'required|integer|min:1|max:120',
            'gender' => 'required|in:Male,Female,Other',
            'phone' => 'nullable|string|max:20',
        ]);

        $patientCode = 'BN' . rand(1000, 9999);

        Patient::create([
            'patient_code' => $patientCode,
            'name' => $request->name,
            'age' => $request->age,
            'gender' => $request->gender,
            'phone' => $request->phone,
        ]);

        return redirect()->route('patients.index')->with('success', 'Đã thêm bệnh nhân mới thành công!');
    }

    public function edit($id)
    {
        $patient = Patient::findOrFail($id);
        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'required|integer|min:0',
            'gender' => 'required|string',
            'phone' => 'nullable|string',
        ]);

        $patient = Patient::findOrFail($id);
        $patient->update($request->only(['name', 'age', 'gender', 'phone']));

        return redirect()->route('patients.index')->with('success', 'Cập nhật thông tin bệnh nhân thành công!');
    }

    public function destroy($id)
    {
        $patient = Patient::with('scans')->findOrFail($id);
        
        foreach($patient->scans as $scan) {
            if ($scan->image_path) {
                Storage::disk('public')->delete($scan->image_path);
            }
            $scan->delete();
        }

        $patient->delete();
        return redirect()->route('patients.index')->with('success', 'Đã xóa hồ sơ bệnh nhân!');
    }

    public function exportPDF($scan_id)
    {
        $scan = ScanResult::with('patient')->findOrFail($scan_id);
        $data = [
            'title' => 'PHIẾU KẾT QUẢ CHẨN ĐOÁN HÌNH ẢNH',
            'date' => date('d/m/Y'),
            'scan' => $scan,
            'patient' => $scan->patient
        ];

        $pdf = Pdf::loadView('patients.pdf_result', $data)->setPaper('a4', 'portrait');
        return $pdf->download('KetQua_' . $scan->patient->patient_code . '.pdf');
    }

    public function dashboard()
    {
        $totalPatients = Patient::count();
        $malignantCount = ScanResult::where('prediction', 'LIKE', '%Malignant%')->count();
        $normalCount = ScanResult::where('prediction', 'LIKE', '%Benign%')->count();
        $uncertainCount = ScanResult::where('prediction', 'LIKE', '%Uncertain%')->count();

        $monthlyData = Patient::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $labels = $monthlyData->map(fn($item) => "Tháng " . $item->month);
        $counts = $monthlyData->pluck('count');

        $recentPatients = Patient::with('scans')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $pieData = [$malignantCount, $normalCount, $uncertainCount];

        return view('dashboard', compact(
            'totalPatients', 'malignantCount', 'normalCount', 'uncertainCount', 
            'labels', 'counts', 'recentPatients', 'pieData'
        ));
    }

    public function handleScan(Request $request, $id)
    {
        $request->validate(['image' => 'required|image|mimes:jpeg,png,jpg|max:2048']);
        $patient = Patient::findOrFail($id);
        $image = $request->file('image');

        try {
            $response = Http::attach(
                'image', file_get_contents($image), $image->getClientOriginalName()
            )->post('http://127.0.0.1:5000/api/v1/predict');

            if ($response->successful()) {
                $result = $response->json();
                $path = $image->store('scans', 'public');

                ScanResult::create([
                    'patient_id' => $patient->id,
                    'image_path' => $path,
                    'prediction' => $result['prediction'],
                    'confidence_score' => $result['confidence'],
                    'scan_date' => now(),
                ]);

                return redirect()->route('patients.show', $id)->with('success', 'Đã phân tích xong!');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi kết nối AI Service.');
        }
    }

    /**
     * Tích hợp GEMINI AI - Cú pháp chuẩn bản 2.0
     */
  public function chatAI(Request $request)
{
    try {
        $userMessage = $request->input('message');
        $apiKey = env('GEMINI_API_KEY', 'AIzaSyAWLFh5LIyaDu9Wm5ynSlxWfBwgU4m-Zek');
        
        // Sử dụng model có sẵn trong danh sách của bạn
        $model = 'gemini-3.1-flash-lite-preview';
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . $apiKey;

        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($url, [
            'contents' => [
                ['parts' => [['text' => "Bạn là trợ lý y tế chuyên về ung thư phổi. Câu hỏi: " . $userMessage]]]
            ]
        ]);

        $data = $response->json();

        if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            return response()->json([
                'reply' => $data['candidates'][0]['content']['parts'][0]['text']
            ]);
        }

        return response()->json(['reply' => 'AI không phản hồi, mã lỗi: ' . $response->status()], 200);

    } catch (\Exception $e) {
        return response()->json(['reply' => 'Lỗi kết nối: ' . $e->getMessage()], 200);
    }
}
}