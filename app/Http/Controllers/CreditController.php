<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CreditApplication;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class CreditController extends Controller
{
    public function index(Request $request)
    {
        $query = CreditApplication::query();
        
        // Filter berdasarkan status jika ada
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search berdasarkan nama atau NIK
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $data = $query->recent()->paginate(10);
        
        $stats = [
            'total' => CreditApplication::count(),
            'submitted' => CreditApplication::where('status', 'Submitted')->count(),
            'approved' => CreditApplication::where('status', 'Approved')->count(),
            'rejected' => CreditApplication::where('status', 'Rejected')->count(),
        ];

        return view('dashboard', compact('data', 'stats'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'customer_name' => 'required|string|max:255',
                'nik' => [
                    'required',
                    'string',
                    'size:16',
                    'regex:/^[0-9]{16}$/',
                    Rule::unique('credit_applications', 'nik')
                ],
                'vehicle_price' => 'required|numeric|min:10000000|max:2000000000',
                'vehicle_type' => 'required|string|max:100',
                'ktp_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ], [
                'customer_name.required' => 'Nama konsumen harus diisi',
                'nik.required' => 'NIK harus diisi',
                'nik.size' => 'NIK harus 16 digit',
                'nik.regex' => 'NIK hanya boleh berisi angka',
                'nik.unique' => 'NIK sudah pernah mengajukan kredit',
                'vehicle_price.min' => 'Harga kendaraan minimal Rp 10.000.000',
                'vehicle_price.max' => 'Harga kendaraan maksimal Rp 2.000.000.000',
                'vehicle_type.required' => 'Jenis kendaraan harus diisi',
                'ktp_image.required' => 'Foto KTP harus diupload',
                'ktp_image.image' => 'File harus berupa gambar',
                'ktp_image.max' => 'Ukuran file maksimal 2MB',
            ]);

            DB::beginTransaction();

            // Convert image to Base64
            $imageFile = $request->file('ktp_image');
            $imageContent = file_get_contents($imageFile->getRealPath());
            $base64 = base64_encode($imageContent);
            $mimeType = $imageFile->getClientMimeType();
            $imageSrc = "data:{$mimeType};base64,{$base64}";

            CreditApplication::create([
                'customer_name' => $validated['customer_name'],
                'nik' => $validated['nik'],
                'vehicle_price' => $validated['vehicle_price'],
                'vehicle_type' => $validated['vehicle_type'],
                'ktp_image_base64' => $imageSrc,
                'status' => 'Submitted'
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Pengajuan kredit berhasil dikirim! Silakan tunggu proses persetujuan.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error storing credit application: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan pengajuan. Silakan coba lagi.');
        }
    }

    public function approve(Request $request, $id)
    {
        try {
            $application = CreditApplication::findOrFail($id);
            
            if ($application->status !== 'Submitted') {
                return redirect()->back()->with('error', 'Pengajuan sudah diproses sebelumnya.');
            }

            $application->update([
                'status' => 'Approved',
                'approved_at' => now(),
                'approved_by' => $request->input('approved_by', 'System'),
                'notes' => $request->input('notes')
            ]);

            return redirect()->back()->with('success', "Pengajuan atas nama {$application->customer_name} berhasil disetujui!");

        } catch (\Exception $e) {
            Log::error('Error approving credit application: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyetujui pengajuan.');
        }
    }

    public function reject(Request $request, $id)
    {
        try {
            $application = CreditApplication::findOrFail($id);
            
            if ($application->status !== 'Submitted') {
                return redirect()->back()->with('error', 'Pengajuan sudah diproses sebelumnya.');
            }

            $application->update([
                'status' => 'Rejected',
                'notes' => $request->input('notes', 'Pengajuan ditolak')
            ]);

            return redirect()->back()->with('success', "Pengajuan atas nama {$application->customer_name} ditolak.");

        } catch (\Exception $e) {
            Log::error('Error rejecting credit application: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menolak pengajuan.');
        }
    }

    public function show($id)
    {
        $application = CreditApplication::findOrFail($id);
        return view('application-detail', compact('application'));
    }
}