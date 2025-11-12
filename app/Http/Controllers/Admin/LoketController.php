<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use App\Models\Loket;
use App\Events\LoketToggleStatus; // Pastikan Event ini ada jika Anda menggunakannya
use Illuminate\Http\Request;

class LoketController extends Controller
{
    /**
     * Menampilkan daftar loket.
     */
    public function index()
    {
        $lokets = Loket::with('layanan')->orderBy('nama_loket', 'asc')->get();
        $layanans = Layanan::orderBy('nama_layanan', 'asc')->get();
        return view('admin.loket.index', compact('lokets', 'layanans'));
    }

    /**
     * [BARU] Menampilkan form untuk membuat loket baru.
     */
    public function create()
    {
        // Ambil layanan yang 'aktif' untuk dropdown
        $layanans = Layanan::where('status', 'aktif')->orderBy('nama_layanan', 'asc')->get();
        return view('admin.loket.create', compact('layanans'));
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
     * [BARU] Menampilkan form untuk mengedit loket.
     */
    public function edit(Loket $loket) // Menggunakan Route Model Binding
    {
        // Ambil semua layanan (aktif atau tidak) untuk dropdown, 
        // siapa tahu layanan loket ini sudah nonaktif tapi masih terpakai.
        $layanans = Layanan::orderBy('nama_layanan', 'asc')->get();
        return view('admin.loket.edit', compact('loket', 'layanans'));
    }


    /**
     * Memperbarui data loket.
     */
    public function update(Request $request, Loket $loket) // Menggunakan Route Model Binding
    {
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
    public function destroy(Loket $loket) // Menggunakan Route Model Binding
    {
        try {
            if ($loket->users()->count() > 0) {
                return redirect()->route('admin.loket.index')
                                 ->with('error', 'Loket tidak dapat dihapus karena masih ditugaskan ke operator.');
            }
            
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

    /**
     * Toggle Status Loket (AJAX) - (Dipertahankan dari file Anda)
     */
    public function toggleStatus(Request $request, Loket $loket)
    {
        $newStatus = $loket->status === 'aktif' ? 'tutup' : 'aktif';
        
        $loket->update(['status' => $newStatus]);

        // Pastikan Anda sudah setup Event dan Listener untuk broadcast
        // broadcast(new LoketToggleStatus($loket, $newStatus))->toOthers();

        return response()->json([
            'success' => true,
            'status' => $newStatus,
            'message' => 'Status loket berhasil diubah menjadi ' . $newStatus,
        ]);
    }
}