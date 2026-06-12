<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\ScanResult;
use App\Support\ScanAssessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ScanController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $patient = Patient::findOrFail($request->patient_id);
        $image = $request->file('image');

        try {
            $response = Http::timeout(60)->attach(
                'image',
                file_get_contents($image->getRealPath()),
                $image->getClientOriginalName()
            )->post('http://127.0.0.1:5000/api/v1/predict');

            if (! $response->successful()) {
                Log::warning('AI scan service returned an error.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return back()->with('error', 'AI service could not analyze this image. Please try again.');
            }

            $aiResult = $response->json();
            $prediction = $aiResult['prediction'] ?? 'Uncertain';
            $confidence = $aiResult['confidence'] ?? 0;
            $path = $image->store('scans', 'public');

            ScanResult::create([
                'patient_id' => $patient->id,
                'doctor_id' => $request->user()->id,
                'image_path' => $path,
                'prediction' => $prediction,
                'confidence_score' => $confidence,
                'doctor_comments' => ScanAssessment::clinicalRecordComment($prediction, $confidence),
            ]);

            return redirect()->route('patients.show', $patient->id)
                ->with('success', 'AI analysis completed and the clinical assessment was saved to the medical record.');
        } catch (\Throwable $e) {
            Log::error('AI scan service connection failed.', [
                'message' => $e->getMessage(),
            ]);

            return back()->with('error', 'Cannot connect to AI service. Please start the AI service and try again.');
        }
    }

    public function destroy(ScanResult $scan)
    {
        if ($scan->image_path && Storage::disk('public')->exists($scan->image_path)) {
            Storage::disk('public')->delete($scan->image_path);
        }

        $scan->delete();

        return back()->with('success', 'Scan result deleted successfully.');
    }
}
