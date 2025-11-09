<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    public function index()
    {
        $pengaturan = Pengaturan::first();
        return view('admin.pengaturan.index', compact('pengaturan'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nama_instansi' => 'required|string|max:255',
            'alamat' => 'required|string',
            'telepon' => 'required|string|max:20',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $pengaturan = Pengaturan::first();
        
        if (!$pengaturan) {
            $pengaturan = new Pengaturan();
        }

        $data = $request->only(['nama_instansi', 'alamat', 'telepon', 'deskripsi']);

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/logo'), $filename);
            $data['logo'] = $filename;
        }

        $pengaturan->fill($data);
        $pengaturan->save();

        return redirect()->route('admin.pengaturan.index')->with('success', 'Pengaturan berhasil diperbarui');
    }
}