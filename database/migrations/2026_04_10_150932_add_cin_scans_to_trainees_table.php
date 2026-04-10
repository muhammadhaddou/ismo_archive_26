<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trainees', function (Blueprint $table) {
            $table->string('cin_scan')->nullable()->after('cin');         // Scan CIN stagiaire
            $table->string('cin_pere_scan')->nullable()->after('cin_pere'); // Scan CIN père
            $table->string('cin_mere_scan')->nullable()->after('cin_mere'); // Scan CIN mère
        });
    }

    public function down(): void
    {
        Schema::table('trainees', function (Blueprint $table) {
            $table->dropColumn(['cin_scan', 'cin_pere_scan', 'cin_mere_scan']);
        });
    }
};
