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
use Illuminate\Support\Facades\DB;

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
            $query->with('doctor')->latest();
        }])->findOrFail($id);

    // Lấy tin nhắn
    $messages = ChatMessage::where('patient_id', $id)
                ->latest() // Tương đương orderBy('created_at', 'desc')
                ->take(20)
                ->get()
                ->reverse();

    return view('patients.show', compact('patient', 'messages'));
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
        $scan = ScanResult::with(['patient', 'doctor'])->findOrFail($scan_id);
        $scanImagePath = null;

        if ($scan->image_path) {
            if (str_starts_with($scan->image_path, 'http')) {
                $scanImagePath = $scan->image_path;
            } else {
                $relativeImagePath = ltrim($scan->image_path, '/');
                $publicImagePath = public_path('storage/' . $relativeImagePath);
                $storageImagePath = storage_path('app/public/' . $relativeImagePath);

                $scanImagePath = file_exists($publicImagePath)
                    ? $publicImagePath
                    : (file_exists($storageImagePath) ? $storageImagePath : null);
            }
        }

        $data = [
            'title' => 'PHIẾU KẾT QUẢ CHẨN ĐOÁN HÌNH ẢNH',
            'date' => date('d/m/Y'),
            'scan' => $scan,
            'patient' => $scan->patient,
            'scanImagePath' => $scanImagePath
        ];

        $pdf = Pdf::loadView('patients.pdf_result', $data)
            ->setPaper('a4', 'portrait')
            ->setOption('isRemoteEnabled', true);
        return $pdf->download('KetQua_' . $scan->patient->patient_code . '.pdf');
    }

    public function dashboard()
    {
        $totalPatients = Patient::count();
        $malignantCount = ScanResult::where('prediction', 'LIKE', '%Malignant%')->count();
        $normalCount = ScanResult::where('prediction', 'LIKE', '%Benign%')->count();
        $uncertainCount = ScanResult::where('prediction', 'LIKE', '%Uncertain%')->count();

        $startDate = now()->subMonths(5)->startOfMonth();
        $patientsByMonth = Patient::where('created_at', '>=', $startDate)
            ->get()
            ->groupBy(fn($patient) => $patient->created_at->format('Y-m'));

        $monthlyData = collect(range(0, 5))->map(function ($offset) use ($startDate, $patientsByMonth) {
            $month = $startDate->copy()->addMonths($offset);
            $key = $month->format('Y-m');

            return (object) [
                'month' => $month->format('n'),
                'count' => $patientsByMonth->get($key, collect())->count(),
            ];
        });

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
                    'doctor_id' => $request->user()->id,
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

    
  public function chatAI(Request $request)
{
    try {
        $userMessage = $request->input('message');
        
        // 1. Giữ nguyên việc lấy ID để xác định ai đang chat
        // Mình sẽ lấy ID của bác sĩ (hoặc người dùng đang đăng nhập)
        $doctorId = auth()->id() ?? $request->input('doctor_id') ?? 1;

        // 2. Logic gọi API Gemini (GIỮ NGUYÊN BẢN CỦA BẠN)
        $apiKey = env('GEMINI_API_KEY', 'AIzaSyAWLFh5LIyaDu9Wm5ynSlxWfBwgU4m-Zek');
        $model = 'gemini-3.1-flash-lite-preview';
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . $apiKey;

        $response = \Illuminate\Support\Facades\Http::post($url, [
            'contents' => [
                ['parts' => [['text' => "Bạn là trợ lý y khoa. Trả lời: " . $userMessage]]]
            ]
        ]);

        $data = $response->json();

        if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            $aiReply = $data['candidates'][0]['content']['parts'][0]['text'];

            // 3. THAY ĐỔI VIỆC LƯU DATABASE VÀO BẢNG MỚI (doctor_ai_chats)
            
            // Lưu câu hỏi của Bác sĩ (user)
            \DB::table('doctor_ai_chats')->insert([
                'doctor_id'  => $doctorId,
                'role'       => 'user', 
                'content'    => $userMessage,
                'created_at' => now(),
            ]);

            // Lưu phản hồi của AI (assistant)
            \DB::table('doctor_ai_chats')->insert([
                'doctor_id'  => $doctorId,
                'role'       => 'assistant',
                'content'    => $aiReply,
                'created_at' => now(),
            ]);

            return response()->json(['reply' => $aiReply]);
        }

        return response()->json(['reply' => 'AI bận, mã: ' . $response->status()], 200);

    } catch (\Exception $e) {
        return response()->json(['reply' => 'Lỗi: ' . $e->getMessage()], 200);
    }
}

public function getChatHistory(Request $request)
{
    try {
        // Lấy doctor_id từ request (Frontend gửi lên) hoặc từ Auth
        $doctorId = $request->input('doctor_id') ?? auth()->id() ?? 1;

        // Lấy toàn bộ tin nhắn của bác sĩ này, sắp xếp theo thời gian tăng dần
        $history = DB::table('doctor_ai_chats')
            ->where('doctor_id', $doctorId)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $history
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
}

    
}
