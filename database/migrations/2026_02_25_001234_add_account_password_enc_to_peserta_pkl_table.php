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
        $table->text('account_password_enc')->nullable()->after('user_id');
    });
}

public function down(): void
{
    Schema::table('peserta_pkl', function (Blueprint $table) {
        $table->dropColumn('account_password_enc');
    });
}
};
