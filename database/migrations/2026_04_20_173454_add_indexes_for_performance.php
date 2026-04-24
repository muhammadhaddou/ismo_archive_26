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
        Schema::table('trainees', function (Blueprint $table) {
            $table->index('cin');
            $table->index('cef');
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->index('reference_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trainees', function (Blueprint $table) {
            $table->dropIndex(['cin']);
            $table->dropIndex(['cef']);
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex(['reference_number']);
        });
    }
};
