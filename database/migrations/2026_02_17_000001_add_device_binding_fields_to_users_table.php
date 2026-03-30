<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('device_hash', 64)->nullable()->after('role');
            $table->string('last_login_ip')->nullable()->after('device_hash');
            $table->text('last_login_user_agent')->nullable()->after('last_login_ip');
            $table->timestamp('last_login_at')->nullable()->after('last_login_user_agent');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['device_hash', 'last_login_ip', 'last_login_user_agent', 'last_login_at']);
        });
    }
};
