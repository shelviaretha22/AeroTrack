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
    Schema::table('users', function (Blueprint $table) {
        $table->string('avatar_path')->nullable();
        $table->string('theme_preference')->default('light');
        $table->boolean('email_notifications')->default(true);
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn([
            'avatar_path',
            'theme_preference',
            'email_notifications',
        ]);
    });
}
};
