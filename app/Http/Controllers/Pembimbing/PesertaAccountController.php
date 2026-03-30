<?php

namespace App\Http\Controllers\Pembimbing;

use App\Http\Controllers\Controller;
use App\Models\PesertaPkl;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PesertaAccountController extends Controller
{
    public function store(PesertaPkl $peserta)
    {
        if ($peserta->user_id) {
            return back()->with('info', 'Peserta ini sudah punya akun. Tidak perlu bikin akun kembar, dunia sudah cukup ruwet.');
        }

        if ($peserta->status_pkl !== 'ACTIVE') {
            return back()->with('error', 'Peserta berstatus ARCHIVE, jadi akun baru tidak dibuat.');
        }

        $base = Str::slug($peserta->nama, '.');
        $email = $base.'@pkl.test';
        $i = 1;

        while (User::where('email', $email)->exists()) {
            $email = $base.$i.'@pkl.test';
            $i++;
        }

        $passwordPlain = Str::random(8);

        $user = User::create([
            'name' => $peserta->nama,
            'email' => $email,
            'password' => $passwordPlain,
            'role' => 'peserta',
            'device_hash' => null,
        ]);

        $peserta->update([
            'user_id' => $user->id,
            'account_password_enc' => Crypt::encryptString($passwordPlain),
        ]);

        return back()->with('success', "Akun peserta berhasil dibuat. Email: {$email} | Password: {$passwordPlain}");
    }

    public function resetPassword(PesertaPkl $peserta)
    {
        if (!$peserta->user) {
            return back()->with('error', 'Peserta ini belum punya akun untuk di-reset.');
        }

        $plainPassword = Str::random(10);

        $peserta->user->update([
            'password' => Hash::make($plainPassword),
        ]);

        $peserta->update([
            'account_password_enc' => Crypt::encryptString($plainPassword),
        ]);

        return back()->with('success', "Password akun direset. Email: {$peserta->user->email} | Password baru: {$plainPassword}");
    }
}
