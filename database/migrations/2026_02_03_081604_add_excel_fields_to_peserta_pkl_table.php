<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('peserta_pkl', function (Blueprint $table) {
        // kosong dulu, biar gak error duplicate di dalam closure
    });

    // cek kolom di luar closure
    if (!Schema::hasColumn('peserta_pkl', 'no_hp')) {
        Schema::table('peserta_pkl', fn (Blueprint $table) => $table->string('no_hp')->nullable());
    }

    if (!Schema::hasColumn('peserta_pkl', 'alamat_rumah')) {
        Schema::table('peserta_pkl', fn (Blueprint $table) => $table->text('alamat_rumah')->nullable());
    }

    if (!Schema::hasColumn('peserta_pkl', 'nama_guru_pembimbing')) {
        Schema::table('peserta_pkl', fn (Blueprint $table) => $table->string('nama_guru_pembimbing')->nullable());
    }

    if (!Schema::hasColumn('peserta_pkl', 'no_hp_guru_pembimbing')) {
        Schema::table('peserta_pkl', fn (Blueprint $table) => $table->string('no_hp_guru_pembimbing')->nullable());
    }

    if (!Schema::hasColumn('peserta_pkl', 'foto_peserta_pkl')) {
        Schema::table('peserta_pkl', fn (Blueprint $table) => $table->string('foto_peserta_pkl')->nullable());
    }

    // kalau kamu juga punya durasi_pkl di sistem, tambahin ceknya juga:
    if (!Schema::hasColumn('peserta_pkl', 'durasi_pkl')) {
        Schema::table('peserta_pkl', fn (Blueprint $table) => $table->string('durasi_pkl')->nullable());
    }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peserta_pkl', function (Blueprint $table) {
                    $cols = [
            'no_hp',
            'alamat_rumah',
            'nama_guru_pembimbing',
            'no_hp_guru_pembimbing',
            'foto_peserta_pkl',
            'durasi_pkl',
        ];

        foreach ($cols as $col) {
            if (Schema::hasColumn('peserta_pkl', $col)) {
                $table->dropColumn($col);
            }
        }

        });
    }
};
