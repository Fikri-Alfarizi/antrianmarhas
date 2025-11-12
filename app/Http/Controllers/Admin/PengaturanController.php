<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan; // Pastikan model Anda ada di App\Models\Pengaturan
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Penting untuk mengelola file

class PengaturanController extends Controller
{
    /**
     * Menampilkan halaman pengaturan.
     */
    public function index()
    {
        // Ambil data, atau buat baru jika tabel kosong.
        // Ini mencegah error "property of non-object" di view.
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
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Maks 2MB
        ]);

        // 2. Ambil data pengaturan yang ada
        $pengaturan = Pengaturan::firstOrCreate(['id' => 1]);

        // 3. Ambil semua data teks dari form
        $data = $request->only(['nama_instansi', 'alamat', 'telepon', 'deskripsi']);

        // 4. Proses Upload Logo (Jika ada file baru)
        if ($request->hasFile('logo')) {
            
            // Hapus logo lama dari storage (jika ada)
            if ($pengaturan->logo) {
                // Storage::delete() bekerja di dalam 'storage/app/'
                // 'public/' berarti 'storage/app/public/'
                Storage::delete('public/' . $pengaturan->logo);
            }

            // Simpan logo baru ke 'storage/app/public/logos'
            // store() akan generate nama unik
            $path = $request->file('logo')->store('logos', 'public');
            
            // Simpan path file baru ke array data
            $data['logo'] = $path;
        }

        // 5. Update data di database
        $pengaturan->update($data);

        // 6. Kembali ke halaman pengaturan dengan pesan sukses
        return redirect()->route('admin.pengaturan.index')
                         ->with('success', 'Pengaturan berhasil diperbarui.');
    }
}