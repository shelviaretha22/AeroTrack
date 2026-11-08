<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aktivasi_progres', function (Blueprint $table) {
            // Nomor konsumen tenant di sistem operasional bandara.
            $table->string('nomor_konsumen', 50)->nullable()->after('kontrak_sistem_status');
        });
    }

    public function down(): void
    {
        Schema::table('aktivasi_progres', function (Blueprint $table) {
            $table->dropColumn('nomor_konsumen');
        });
    }
};
