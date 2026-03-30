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
            $table->date('tgl_keluar_pkl')->nullable()->after('tgl_masuk_pkl');
            $table->date('tgl_masuk_pkl_2')->nullable()->after('tgl_keluar_pkl');
            $table->date('tgl_keluar_pkl_2')->nullable()->after('tgl_masuk_pkl_2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peserta_pkl', function (Blueprint $table) {
            //
        });
    }
};
