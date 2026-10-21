
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kontraks', function (Blueprint $table) {
            $table->string('status_perpanjangan')->nullable();
            $table->date('tanggal_konfirmasi')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('kontraks', function (Blueprint $table) {
            $table->dropColumn([
                'status_perpanjangan',
                'tanggal_konfirmasi',
            ]);
        });
    }
};
