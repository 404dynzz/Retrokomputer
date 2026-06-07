<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;

class ProfileController extends Controller
{
    public function getProfile(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'role' => $user->role
        ]);
    }

    public function verifyPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = $request->user();

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Kata sandi saat ini tidak cocok'
            ], 422);
        }

        // Generate temporary verification token valid for 15 minutes
        $token = Str::random(40);
        Cache::put("profile_verified_{$user->id}", $token, now()->addMinutes(15));

        return response()->json([
            'message' => 'Verifikasi berhasil',
            'verification_token' => $token
        ]);
    }

    public function sendOtp(Request $request)
    {
        $user = $request->user();

        if (!$user->email) {
            return response()->json([
                'message' => 'Akun Anda tidak memiliki email yang terdaftar. Hubungi admin atau gunakan opsi kata sandi.'
            ], 422);
        }

        // Generate 6-digit OTP
        $otp = sprintf('%06d', mt_rand(0, 999999));
        Cache::put("profile_otp_{$user->id}", $otp, now()->addMinutes(10));

        // Mask email for display in response
        $emailParts = explode('@', $user->email);
        $maskedEmail = substr($emailParts[0], 0, 2) . str_repeat('*', max(0, strlen($emailParts[0]) - 2)) . '@' . $emailParts[1];

        try {
            Mail::raw("Halo {$user->name},\n\nKode OTP Anda untuk mengakses pengaturan profil adalah: {$otp}\n\nKode ini berlaku selama 10 menit. Jangan berikan kode ini kepada siapapun.\n\nSalam,\nRetrokomputer", function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Kode OTP Verifikasi Profil - Retrokomputer');
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengirim email OTP. Silakan gunakan metode verifikasi kata sandi.'
            ], 500);
        }

        return response()->json([
            'message' => "Kode OTP telah dikirim ke {$maskedEmail}",
            'masked_email' => $maskedEmail
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $user = $request->user();
        $cachedOtp = Cache::get("profile_otp_{$user->id}");

        if (!$cachedOtp || $cachedOtp !== $request->otp) {
            return response()->json([
                'message' => 'Kode OTP tidak cocok atau telah kedaluwarsa'
            ], 422);
        }

        // Clear OTP
        Cache::forget("profile_otp_{$user->id}");

        // Generate temporary verification token valid for 15 minutes
        $token = Str::random(40);
        Cache::put("profile_verified_{$user->id}", $token, now()->addMinutes(15));

        return response()->json([
            'message' => 'Verifikasi berhasil',
            'verification_token' => $token
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        // Retrieve token from header or request body
        $token = $request->header('X-Profile-Verification-Token') ?: $request->input('verification_token');
        $cachedToken = Cache::get("profile_verified_{$user->id}");

        if (!$token || !$cachedToken || $token !== $cachedToken) {
            return response()->json([
                'message' => 'Akses ditolak. Silakan lakukan verifikasi keamanan terlebih dahulu.'
            ], 403);
        }

        // Validate payload
        $request->validate([
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $updated = false;

        if ($request->filled('email') && $request->email !== $user->email) {
            $user->email = $request->email;
            $updated = true;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            $updated = true;
        }

        if ($updated) {
            $user->save();
            // Clear verification token so it cannot be reused
            Cache::forget("profile_verified_{$user->id}");
            return response()->json([
                'message' => 'Profil berhasil diperbarui',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'role' => $user->role
                ]
            ]);
        }

        return response()->json([
            'message' => 'Tidak ada perubahan profil yang dilakukan'
        ]);
    }
}
