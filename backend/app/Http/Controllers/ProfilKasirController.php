<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProfilKasir;
use App\Models\User;

class ProfilKasirController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            // Admin sees all cashier profiles with user info
            $profiles = ProfilKasir::with('user:id,name,username')->orderBy('user_id')->orderBy('nama')->get();
            return response()->json($profiles);
        }

        // Kasir sees only their own profiles
        $profiles = ProfilKasir::where('user_id', $user->id)->get();
        return response()->json($profiles);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Hanya admin yang dapat menambahkan profil kasir.'], 403);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'nama' => 'required|string|max:255',
            'kode_khusus' => 'required|string|max:255'
        ]);

        // Verify the target user is actually a kasir
        $targetUser = User::findOrFail($validated['user_id']);
        if ($targetUser->role !== 'kasir') {
            return response()->json(['message' => 'Profil hanya dapat dibuat untuk akun dengan role kasir.'], 422);
        }

        $exists = ProfilKasir::where('user_id', $validated['user_id'])
            ->where('nama', $validated['nama'])
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Nama profil sudah digunakan pada akun kasir ini.'], 422);
        }

        $profile = ProfilKasir::create([
            'user_id' => $validated['user_id'],
            'nama' => $validated['nama'],
            'kode_khusus' => $validated['kode_khusus'],
            'is_active' => false
        ]);

        $profile->load('user:id,name,username');

        return response()->json($profile, 201);
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();

        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Hanya admin yang dapat mengedit profil kasir.'], 403);
        }

        $profile = ProfilKasir::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kode_khusus' => 'nullable|string|max:255'
        ]);

        // Check unique name within the same user
        $exists = ProfilKasir::where('user_id', $profile->user_id)
            ->where('nama', $validated['nama'])
            ->where('id', '!=', $profile->id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Nama profil sudah digunakan pada akun kasir ini.'], 422);
        }

        $profile->nama = $validated['nama'];
        if (!empty($validated['kode_khusus'])) {
            $profile->kode_khusus = $validated['kode_khusus'];
        }
        $profile->save();

        $profile->load('user:id,name,username');

        return response()->json($profile);
    }

    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Hanya admin yang dapat menghapus profil kasir.'], 403);
        }

        $profile = ProfilKasir::findOrFail($id);
        $profile->delete();
        return response()->json(['message' => 'Profil berhasil dihapus.']);
    }

    public function aktifkan(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:profil_kasirs,id',
            'kode_khusus' => 'required|string'
        ]);

        $profile = ProfilKasir::where('user_id', $request->user()->id)->findOrFail($validated['id']);

        if ($profile->kode_khusus !== $validated['kode_khusus']) {
            return response()->json(['message' => 'Kode khusus salah.'], 422);
        }

        // Deactivate other profiles of this user
        ProfilKasir::where('user_id', $request->user()->id)->update(['is_active' => false]);

        // Activate this profile
        $profile->is_active = true;
        $profile->save();

        return response()->json($profile);
    }

    public function nonaktifkan(Request $request)
    {
        ProfilKasir::where('user_id', $request->user()->id)->update(['is_active' => false]);
        return response()->json(['message' => 'Profil kasir berhasil dinonaktifkan.']);
    }

    public function getAktif(Request $request)
    {
        $profile = ProfilKasir::where('user_id', $request->user()->id)
            ->where('is_active', true)
            ->first();
        return response()->json($profile);
    }

    /**
     * Get list of users with role 'kasir' (for admin dropdown)
     */
    public function getKasirUsers(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $users = User::where('role', 'kasir')->where('is_active', true)->get(['id', 'name', 'username']);
        return response()->json($users);
    }
}
