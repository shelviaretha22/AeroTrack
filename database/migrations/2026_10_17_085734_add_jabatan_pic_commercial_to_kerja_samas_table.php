<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kerja_samas', function (Blueprint $table) {
            $table->string('jabatan_pic_commercial')
                ->nullable()
                ->after('pic_commercial');
        });
    }

    public function down(): void
    {
        Schema::table('kerja_samas', function (Blueprint $table) {
            $table->dropColumn('jabatan_pic_commercial');
        });
    }
};