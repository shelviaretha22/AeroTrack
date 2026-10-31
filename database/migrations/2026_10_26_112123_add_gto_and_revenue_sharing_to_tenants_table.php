
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
        Schema::table('tenants', function (Blueprint $table) {
            $table->decimal('gto', 15, 2)
                ->nullable()
                ->after('bentuk_kerja_sama');

            $table->decimal('revenue_sharing', 5, 2)
                ->nullable()
                ->after('gto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'gto',
                'revenue_sharing',
            ]);
        });
    }
};
