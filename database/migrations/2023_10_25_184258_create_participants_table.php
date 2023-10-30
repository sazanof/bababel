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
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->integer('userId')->index(); // users.id
            $table->integer('meetingId')->index(); // meetings.id
            $table->boolean('isModerator')->default(false); // 1 - MODERATOR or 0 - VIEWER
            $table->boolean('isOrganizer')->default(false);
            $table->string('createTime')->nullable();
            $table->string('webVoiceConf')->nullable();
            $table->string('defaultLayout')->default('SMART_LAYOUT');
            $table->boolean('redirect')->default(false);
            $table->string('errorRedirectUrl')->nullable();
            $table->boolean('guest')->nullable();
            $table->boolean('excludeFromDashboard')->default(false);
            $table->unique(['userId', 'meetingId']);
            //avatarURL - from users.photo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
