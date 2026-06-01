<?php

namespace App\Http\Controllers;

use App\Models\ScanResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ScanController extends Controller
{
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

            return back()->withErrors([
                'api_error' => 'Dich vu AI dang ban hoac co loi (Status: ' . $response->status() . ')',
            ]);
        } catch (\Exception $e) {
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
