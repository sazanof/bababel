<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('recordings', function (Blueprint $table) {
            $table->id();
            $table->string('recordID');
            $table->integer('meetingID')->index();
            $table->dateTime('startTime')->nullable();
            $table->dateTime('endTime')->nullable();
            $table->integer('state')->default(0); //0 - unpublished 1 - published
            $table->integer('size')->nullable();
            $table->text('url')->nullable();
            $table->integer('processingTime')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recordings');
    }
};
