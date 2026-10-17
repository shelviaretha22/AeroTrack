
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kontraks', function (Blueprint $table) {
            $table->string('nama_commercial')->nullable();
            $table->string('jabatan_commercial')->nullable();
            $table->string('final_pdf_path')->nullable();
            $table->timestamp('final_uploaded_at')->nullable();
            $table->boolean('final_signature_confirmed')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('kontraks', function (Blueprint $table) {
            $table->dropColumn([
                'nama_commercial',
                'jabatan_commercial',
                'final_pdf_path',
                'final_uploaded_at',
                'final_signature_confirmed',
            ]);
        });
    }
};
