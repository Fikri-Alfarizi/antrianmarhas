<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use App\Services\ImgbbService;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    /**
     * Menampilkan halaman pengaturan.
     */
    public function index()
    {
        $pengaturan = Pengaturan::firstOrCreate(['id' => 1]);
        
        return view('admin.pengaturan.index', compact('pengaturan'));
    }

    /**
     * Menerima form submit dan meng-update pengaturan.
     */
    public function update(Request $request)
    {
        // 1. Validasi semua input
        $request->validate([
            'nama_instansi' => 'required|string|max:255',
            'alamat' => 'required|string',
            'telepon' => 'required|string|max:50',
            'deskripsi' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
        ]);

        // 2. Ambil data pengaturan yang ada
        $pengaturan = Pengaturan::firstOrCreate(['id' => 1]);

        // 3. Ambil semua data teks dari form
        $data = $request->only(['nama_instansi', 'alamat', 'telepon', 'deskripsi']);

        // 4. Proses Upload Logo via ImgBB (Jika ada file baru)
        if ($request->hasFile('logo')) {
            try {
                $imgbbService = new ImgbbService();
                $uploadResult = $imgbbService->upload(
                    $request->file('logo'),
                    'logo-' . time()
                );

                if ($uploadResult && isset($uploadResult['url'])) {
                    $logoUrl = $uploadResult['url'];
                    $isLocal = ($uploadResult['method'] ?? null) === 'local_storage';
                    
                    \Log::info('Logo URL yang akan disimpan: ' . $logoUrl . ($isLocal ? ' (Local Fallback)' : ' (ImgBB)'));
                    $data['logo'] = $logoUrl;
                    
                    // Hapus logo lama dari ImgBB jika ada (opsional)
                    if ($pengaturan->logo && isset($uploadResult['delete_url'])) {
                        // Bisa simpan delete_url di database untuk kemudahan deletion nanti
                    }
                } else {
                    \Log::error('Upload result null atau tidak ada URL');
                    return redirect()->route('admin.pengaturan.index')
                                   ->with('error', 'Gagal upload logo. Cek koneksi internet dan ukuran file (max 2MB).');
                }
            } catch (\Exception $e) {
                \Log::error('Exception saat upload: ' . $e->getMessage());
                return redirect()->route('admin.pengaturan.index')
                               ->with('error', 'Error upload: ' . $e->getMessage());
            }
        }

        // 5. Update data di database
        $pengaturan->update($data);

        // 6. Kembali ke halaman pengaturan dengan pesan sukses
        return redirect()->route('admin.pengaturan.index')
                         ->with('success', 'Pengaturan berhasil diperbarui. Logo tersimpan di online storage!');
    }
}