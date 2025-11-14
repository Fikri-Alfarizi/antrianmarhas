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

        // 4. Proses Upload Logo ke public/logo (Jika ada file baru)
        if ($request->hasFile('logo')) {
            try {
                $file = $request->file('logo');
                $filename = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path('logo');

                // Hapus logo lama jika ada dan berbeda
                if ($pengaturan->logo && file_exists($destinationPath . '/' . $pengaturan->logo)) {
                    @unlink($destinationPath . '/' . $pengaturan->logo);
                }

                $file->move($destinationPath, $filename);
                $data['logo'] = $filename;
            } catch (\Exception $e) {
                \Log::error('Exception saat upload logo lokal: ' . $e->getMessage());
                return redirect()->route('admin.pengaturan.index')
                               ->with('error', 'Error upload: ' . $e->getMessage());
            }
        }

        // 5. Update data di database
        $pengaturan->update($data);

        // 6. Kembali ke halaman pengaturan dengan pesan sukses
        return redirect()->route('admin.pengaturan.index')
                 ->with('success', 'Pengaturan berhasil diperbarui. Logo tersimpan di lokal!');
    }
}