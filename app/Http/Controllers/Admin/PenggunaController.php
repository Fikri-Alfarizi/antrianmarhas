<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Loket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PenggunaController extends Controller
{
    public function index()
    {
        $users = User::with('loket')->latest()->get();
        $lokets = Loket::where('status', 'aktif')->get();
        return view('admin.pengguna.index', compact('users', 'lokets'));
    }

    public function create()
    {
        $lokets = Loket::where('status', 'aktif')->get();
        return view('admin.pengguna.create', compact('lokets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,operator',
            'loket_id' => 'nullable|exists:lokets,id',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $data = $request->all();
        $data['password'] = Hash::make($request->password);

        User::create($data);

        return redirect()->route('admin.pengguna.index')->with('success', 'Pengguna berhasil ditambahkan');
    }

    public function edit(User $pengguna)
    {
        $lokets = Loket::where('status', 'aktif')->get();
        return view('admin.pengguna.edit', compact('pengguna', 'lokets'));
    }

    public function update(Request $request, User $pengguna)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $pengguna->id,
            'email' => 'required|email|unique:users,email,' . $pengguna->id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:admin,operator',
            'loket_id' => 'nullable|exists:lokets,id',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $data = $request->except('password');
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $pengguna->update($data);

        return redirect()->route('admin.pengguna.index')->with('success', 'Pengguna berhasil diperbarui');
    }

    public function destroy(User $pengguna)
    {
        $pengguna->delete();
        return redirect()->route('admin.pengguna.index')->with('success', 'Pengguna berhasil dihapus');
    }
}