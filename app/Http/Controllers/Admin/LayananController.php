<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use Illuminate\Http\Request;

class LayananController extends Controller
{
    public function index()
    {
        $layanans = Layanan::latest()->get();
        return view('admin.layanan.index', compact('layanans'));
    }

    public function create()
    {
        return view('admin.layanan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_layanan' => 'required|string|max:255',
            'prefix' => 'required|string|max:5|unique:layanans,prefix',
            'digit' => 'required|integer|min:1|max:5',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        Layanan::create($request->all());

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Layanan berhasil ditambahkan'], 201);
        }

        return redirect()->route('admin.layanan.index')->with('success', 'Layanan berhasil ditambahkan');
    }

    public function edit(Layanan $layanan)
    {
        return view('admin.layanan.edit', compact('layanan'));
    }

    public function update(Request $request, Layanan $layanan)
    {
        $request->validate([
            'nama_layanan' => 'required|string|max:255',
            'prefix' => 'required|string|max:5|unique:layanans,prefix,' . $layanan->id,
            'digit' => 'required|integer|min:1|max:5',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $layanan->update($request->all());

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Layanan berhasil diperbarui']);
        }

        return redirect()->route('admin.layanan.index')->with('success', 'Layanan berhasil diperbarui');
    }

    public function destroy(Layanan $layanan)
    {
        // Check if layanan has antrians
        if ($layanan->antrians()->exists()) {
            return redirect()->route('admin.layanan.index')->with('error', 'Tidak dapat menghapus layanan yang memiliki data antrian');
        }

        $layanan->delete();
        
        return redirect()->route('admin.layanan.index')->with('success', 'Layanan berhasil dihapus');
    }
}