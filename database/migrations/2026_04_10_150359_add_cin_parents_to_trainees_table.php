<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trainees', function (Blueprint $table) {
            $table->string('cin_pere')->nullable()->after('cin');
            $table->string('cin_mere')->nullable()->after('cin_pere');
            // Change graduation_year to string to support "2022-2023" format
            $table->string('graduation_year', 10)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('trainees', function (Blueprint $table) {
            $table->dropColumn(['cin_pere', 'cin_mere']);
        });
    }
};
