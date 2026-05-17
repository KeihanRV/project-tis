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
        Schema::table('reports', function (Blueprint $table) {
            $table->unsignedBigInteger('trash_id')->nullable()->after('status');
            $table->string('location')->nullable()->after('trash_id');
            $table->string('image_path')->nullable()->after('location');

            $table->foreign('trash_id')->references('id')->on('trashes')->onDelete('set null');
        });

        Schema::table('trashes', function (Blueprint $table) {
            $table->string('weight_unit')->nullable()->after('weight');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
