<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('kontraks', 'klausul_tambahan')) {
            Schema::table('kontraks', function (Blueprint $table) {
                $table->text('klausul_tambahan')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('kontraks', 'klausul_tambahan')) {
            Schema::table('kontraks', function (Blueprint $table) {
                $table->dropColumn('klausul_tambahan');
            });
        }
    }
};