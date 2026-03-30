<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('peserta_pkl', function (Blueprint $table) {
            $table->id();
            $table->date('tgl_masuk_pkl')->nullable();
            $table->string('asal_sekolah')->nullable();
            $table->string('nama');
            $table->string('kompetensi_keahlian')->nullable();
            $table->string('durasi_pkl')->nullable();
            $table->string('no_hp')->nullable();
            $table->text('alamat_rumah')->nullable();
            $table->string('nama_guru_pembimbing')->nullable();
            $table->string('no_hp_guru_pembimbing')->nullable();
            $table->string('foto_path')->nullable();
            $table->timestamps();

            $table->index(['asal_sekolah']);
            $table->index(['kompetensi_keahlian']);
            $table->index(['tgl_masuk_pkl']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peserta_pkl');
    }
};
