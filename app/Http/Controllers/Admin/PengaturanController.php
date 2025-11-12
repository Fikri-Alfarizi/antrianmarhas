<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    protected $imageService;

    public function __construct()
    {
        $this->imageService = new CloudinaryService();
    }

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

        // 4. Proses Upload Logo ke ImgBB (Online)
        if ($request->hasFile('logo')) {
            try {
                // Upload ke ImgBB dan dapatkan URL
                $logoUrl = $this->imageService->uploadFile($request->file('logo'));
                $data['logo'] = $logoUrl;
            } catch (\Exception $e) {
                return redirect()->route('admin.pengaturan.index')
                                 ->with('error', 'Gagal upload logo: ' . $e->getMessage());
            }
        }

        // 5. Update data di database
        $pengaturan->update($data);

        // 6. Flush cache agar setting baru langsung ter-load
        \App\Helpers\SettingHelper::flush();

        // 7. Kembali ke halaman pengaturan dengan pesan sukses
        return redirect()->route('admin.pengaturan.index')
                         ->with('success', 'Pengaturan berhasil diperbarui.');
    }

    // JIKA ANDA INGIN MENAMBAHKAN FITUR AUDIO/WAKTU NANTI,
    // ANDA HARUS MENAMBAHKAN MIGRASI, MODEL, DAN METHOD BARU DI SINI
}