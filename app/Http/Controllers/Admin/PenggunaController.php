<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Loket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PenggunaController extends Controller
{
    /**
     * Menampilkan daftar pengguna (admin/operator).
     */
    public function index()
    {
        $users = User::with('loket')->orderBy('name', 'asc')->get();
        
        // Ambil semua loket untuk dropdown di modal
        // Kita akan validasi di controller jika loket sudah terisi
        $lokets = Loket::orderBy('nama_loket', 'asc')->get();
        
        return view('admin.pengguna.index', compact('users', 'lokets'));
    }

    /**
     * Menyimpan pengguna baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,operator',
            'loket_id' => [
                'nullable',
                'required_if:role,operator', // Wajib jika rolenya operator
                'exists:lokets,id',
                'unique:users,loket_id' // Loket tidak boleh dipakai 2 operator
            ],
        ], [
            'loket_id.required_if' => 'Operator harus memilih loket.',
            'loket_id.unique' => 'Loket ini sudah ditugaskan ke operator lain.',
            'username.unique' => 'Username ini sudah digunakan.',
            'email.unique' => 'Email ini sudah digunakan.',
        ]);

        $data = $request->all();
        $data['password'] = Hash::make($request->password);

        // Admin tidak punya loket
        if ($data['role'] == 'admin') {
            $data['loket_id'] = null;
        }

        User::create($data);

        return redirect()->route('admin.pengguna.index')
                         ->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    /**
     * Memperbarui data pengguna.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => [
                'required', 'string', 'max:255',
                Rule::unique('users', 'username')->ignore($user->id)
            ],
            'email' => [
                'required', 'string', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($user->id)
            ],
            'password' => 'nullable|string|min:6', // Password opsional
            'role' => 'required|in:admin,operator',
            'loket_id' => [
                'nullable',
                'required_if:role,operator',
                'exists:lokets,id',
                Rule::unique('users', 'loket_id')->ignore($user->id)
            ],
        ], [
            'loket_id.required_if' => 'Operator harus memilih loket.',
            'loket_id.unique' => 'Loket ini sudah ditugaskan ke operator lain.',
            'username.unique' => 'Username ini sudah digunakan.',
            'email.unique' => 'Email ini sudah digunakan.',
        ]);

        $data = $request->except('password');

        // Hanya update password jika diisi
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Admin tidak punya loket
        if ($data['role'] == 'admin') {
            $data['loket_id'] = null;
        }

        $user->update($data);

        return redirect()->route('admin.pengguna.index')
                         ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Menghapus pengguna.
     */
    public function destroy(string $id)
    {
        // Jangan hapus user ID 1 (biasanya Super Admin)
        if ($id == 1) {
             return redirect()->route('admin.pengguna.index')
                             ->with('error', 'Admin utama (ID 1) tidak dapat dihapus.');
        }
        
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return redirect()->route('admin.pengguna.index')
                             ->with('success', 'Pengguna berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.pengguna.index')
                             ->with('error', 'Gagal menghapus pengguna.');
        }
    }
}