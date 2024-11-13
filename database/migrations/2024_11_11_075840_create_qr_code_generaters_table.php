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
        Schema::create('qr_code_generaters', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->string('linkName')->nullable();
            $table->string('type')->nullable();
            $table->string('value')->nullable();
            $table->string('fgColor')->nullable();
            $table->string('bgColor')->nullable();
            $table->string('eyeColor')->nullable();
            $table->string('logoImage')->nullable();
            $table->integer('eyeFrameShape')->nullable();
            $table->integer('eyeShape')->nullable();
            $table->string('isDownload')->nullable();
            $table->string('qrName')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qr_code_generaters');
    }
};
