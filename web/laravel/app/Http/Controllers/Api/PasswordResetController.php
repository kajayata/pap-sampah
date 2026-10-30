<?php

namespace App\Http\Controllers\Api;
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class PasswordResetController extends Controller
{
    public function sendCode(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                ['token' => Hash::make($code), 'created_at' => now()]
            );

            Mail::raw(
                "Kode reset kata sandi PapSampah: {$code}. Berlaku 15 menit. Abaikan email ini jika bukan kamu yang meminta.",
                fn ($m) => $m->to($user->email)->subject('Kode Reset Kata Sandi PapSampah')
            );
        }

        // Respons sama baik email terdaftar maupun tidak, agar email tidak bisa ditebak.
        return response()->json(['message' => 'Jika email terdaftar, kode telah dikirim.']);
    }

    public function reset(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'code' => 'required|digits:6',
            'password' => 'required|min:8|confirmed',
        ]);

        $row = DB::table('password_reset_tokens')->where('email', $data['email'])->first();

        if (
            ! $row ||
            Carbon::parse($row->created_at)->addMinutes(15)->isPast() ||
            ! Hash::check($data['code'], $row->token)
        ) {
            return response()->json(['message' => 'Kode salah atau sudah kedaluwarsa.'], 422);
        }

        $user = User::where('email', $data['email'])->firstOrFail();
        $user->forceFill(['password' => Hash::make($data['password'])])->save();

        DB::table('password_reset_tokens')->where('email', $data['email'])->delete();
        $user->tokens()->delete(); // jika memakai Sanctum: paksa login ulang di semua perangkat

        return response()->json(['message' => 'Kata sandi berhasil diubah.']);
    }
}