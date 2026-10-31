<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_perusahaan', function (Blueprint $table) {
            $table->string('telepon')->nullable();
            $table->string('email')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('master_perusahaan', function (Blueprint $table) {
            $table->dropColumn(['telepon', 'email']);
        });
    }
};