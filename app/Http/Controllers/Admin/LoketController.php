<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loket;
use App\Models\Layanan;
use Illuminate\Http\Request;

class LoketController extends Controller
{
    public function index()
    {
        $lokets = Loket::with('layanan')->latest()->get();
        $layanans = Layanan::where('status', 'aktif')->get();
        return view('admin.loket.index', compact('lokets', 'layanans'));
    }

    public function create()
    {
        $layanans = Layanan::where('status', 'aktif')->get();
        return view('admin.loket.create', compact('layanans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_loket' => 'required|string|max:255',
            'layanan_id' => 'required|exists:layanans,id',
            'status' => 'required|in:aktif,tutup',
        ]);

        Loket::create($request->all());

        return redirect()->route('admin.loket.index')->with('success', 'Loket berhasil ditambahkan');
    }

    public function edit(Loket $loket)
    {
        $layanans = Layanan::where('status', 'aktif')->get();
        return view('admin.loket.edit', compact('loket', 'layanans'));
    }

    public function update(Request $request, Loket $loket)
    {
        $request->validate([
            'nama_loket' => 'required|string|max:255',
            'layanan_id' => 'required|exists:layanans,id',
            'status' => 'required|in:aktif,tutup',
        ]);

        $loket->update($request->all());

        return redirect()->route('admin.loket.index')->with('success', 'Loket berhasil diperbarui');
    }

    public function destroy(Loket $loket)
    {
        $loket->delete();
        return redirect()->route('admin.loket.index')->with('success', 'Loket berhasil dihapus');
    }

    public function toggleStatus(Loket $loket)
    {
        $loket->status = $loket->status === 'aktif' ? 'tutup' : 'aktif';
        $loket->save();

        return response()->json(['success' => true, 'status' => $loket->status]);
    }
}