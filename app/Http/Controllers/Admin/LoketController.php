<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use App\Models\Loket;
use Illuminate\Http\Request;

class LoketController extends Controller
{
    /**
     * Menampilkan daftar loket dan form tambah/edit.
     */
    public function index()
    {
        // Ambil semua loket, eager load relasi 'layanan'
        $lokets = Loket::with('layanan')->orderBy('nama_loket', 'asc')->get();
        
        // Ambil semua layanan yang AKTIF untuk dropdown pilihan
        $layanans = Layanan::where('status', 'aktif')->orderBy('nama_layanan', 'asc')->get();
        
        return view('admin.loket.index', compact('lokets', 'layanans'));
    }

    /**
     * Menyimpan loket baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_loket' => 'required|string|max:255',
            'layanan_id' => 'required|integer|exists:layanans,id',
            'status' => 'required|in:aktif,tutup',
        ]);

        Loket::create($request->all());

        return redirect()->route('admin.loket.index')
                         ->with('success', 'Loket baru berhasil ditambahkan.');
    }

    /**
     * Memperbarui data loket.
     */
    public function update(Request $request, string $id)
    {
        $loket = Loket::findOrFail($id);
        
        $request->validate([
            'nama_loket' => 'required|string|max:255',
            'layanan_id' => 'required|integer|exists:layanans,id',
            'status' => 'required|in:aktif,tutup',
        ]);

        $loket->update($request->all());

        return redirect()->route('admin.loket.index')
                         ->with('success', 'Loket berhasil diperbarui.');
    }

    /**
     * Menghapus loket.
     */
    public function destroy(string $id)
    {
        try {
            $loket = Loket::findOrFail($id);

            // Cek apakah loket masih ditugaskan ke pengguna (operator)
            if ($loket->users()->count() > 0) {
                return redirect()->route('admin.loket.index')
                                 ->with('error', 'Loket tidak dapat dihapus karena masih ditugaskan ke operator.');
            }
            
            // Cek apakah loket memiliki riwayat antrian
            if ($loket->antrians()->count() > 0) {
                 return redirect()->route('admin.loket.index')
                                 ->with('error', 'Loket tidak dapat dihapus karena memiliki riwayat antrian.');
            }

            $loket->delete();

            return redirect()->route('admin.loket.index')
                             ->with('success', 'Loket berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.loket.index')
                             ->with('error', 'Gagal menghapus loket: ' . $e->getMessage());
        }
    }

    /*
     * Catatan: Method toggleStatus() dari web.php Anda tidak digunakan di sini
     * karena fungsi edit status sudah ditangani oleh modal 'update'.
     * Ini menjaga kode tetap minimalis.
     */
}