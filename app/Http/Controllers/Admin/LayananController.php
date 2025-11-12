<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use Illuminate\Http\Request;

class LayananController extends Controller
{
    /**
     * Menampilkan daftar semua layanan.
     */
    public function index()
    {
        $layanans = Layanan::orderBy('nama_layanan', 'asc')->get();
        return view('admin.layanan.index', compact('layanans'));
    }

    /**
     * [BARU] Menampilkan form untuk membuat layanan baru.
     */
    public function create()
    {
        return view('admin.layanan.create');
    }

    /**
     * Menyimpan layanan baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_layanan' => 'required|string|max:255',
            'prefix' => 'required|string|max:5|unique:layanans,prefix',
            'digit' => 'required|integer|min:1|max:5',
            'status' => 'required|in:aktif,nonaktif',
        ], [
            'prefix.unique' => 'Prefix ini sudah digunakan. Harap gunakan huruf/kode lain.'
        ]);

        Layanan::create($request->all());

        return redirect()->route('admin.layanan.index')
                         ->with('success', 'Layanan baru berhasil ditambahkan.');
    }

    /**
     * [BARU] Menampilkan form untuk mengedit layanan.
     */
    public function edit(Layanan $layanan) // Menggunakan Route Model Binding
    {
        return view('admin.layanan.edit', compact('layanan'));
    }

    /**
     * Memperbarui data layanan yang ada.
     */
    public function update(Request $request, Layanan $layanan) // Menggunakan Route Model Binding
    {
        $request->validate([
            'nama_layanan' => 'required|string|max:255',
            'prefix' => 'required|string|max:5|unique:layanans,prefix,' . $layanan->id,
            'digit' => 'required|integer|min:1|max:5',
            'status' => 'required|in:aktif,nonaktif',
        ], [
            'prefix.unique' => 'Prefix ini sudah digunakan. Harap gunakan huruf/kode lain.'
        ]);

        $layanan->update($request->all());

        return redirect()->route('admin.layanan.index')
                         ->with('success', 'Layanan berhasil diperbarui.');
    }

    /**
     * Menghapus layanan dari database.
     */
    public function destroy(Layanan $layanan) // Menggunakan Route Model Binding
    {
        try {
            if ($layanan->lokets()->count() > 0) {
                return redirect()->route('admin.layanan.index')
                                 ->with('error', 'Layanan tidak dapat dihapus karena masih digunakan oleh loket.');
            }
            
            if ($layanan->antrians()->count() > 0) {
                 return redirect()->route('admin.layanan.index')
                                  ->with('error', 'Layanan tidak dapat dihapus karena memiliki riwayat antrian.');
            }

            $layanan->delete();

            return redirect()->route('admin.layanan.index')
                             ->with('success', 'Layanan berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.layanan.index')
                             ->with('error', 'Gagal menghapus layanan: ' . $e->getMessage());
        }
    }
}