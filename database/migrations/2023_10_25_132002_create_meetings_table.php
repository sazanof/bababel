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
        Schema::create('meetings', function (Blueprint $table) {
            $table->id()->startingValue(10); // meetingID must be between 2 and 256 characters long and cannot contain commas
            $table->integer('status')->default(0);
            $table->integer('userId');
            $table->timestamp('date');
            $table->string('name');
            $table->string('meetingID')->nullable();
            $table->string('attendeePW')->nullable();
            $table->string('moderatorPW')->nullable();
            $table->string('welcome');
            $table->string('dialNumber')->nullable();
            $table->integer('voiceBridge')->nullable();
            $table->integer('maxParticipants')->default(0);
            $table->string('logoutURL')->nullable();
            $table->boolean('record')->default(true);
            $table->integer('duration')->default(0);
            $table->boolean('isBreakout')->default(false);
            $table->string('parentMeetingID')->nullable();
            $table->integer('sequence')->nullable();
            $table->boolean('freeJoin')->default(false);
            $table->boolean('breakoutRoomsPrivateChatEnabled')->default(true);
            $table->boolean('breakoutRoomsRecord')->default(false);
            $table->string('meta')->nullable();
            $table->string('moderatorOnlyMessage')->nullable();
            $table->boolean('autoStartRecording')->default(false);
            $table->boolean('allowStartStopRecording')->default(true);
            $table->string('webcamsOnlyForModerator')->default(false);
            $table->string('bannerText')->nullable();
            $table->string('bannerColor')->nullable();
            $table->boolean('muteOnStart')->default(false);
            $table->boolean('allowModsToUnmuteUsers')->default(false);
            $table->boolean('lockSettingsDisableCam')->default(false);
            $table->boolean('lockSettingsDisableMic')->default(false);
            $table->boolean('lockSettingsDisablePrivateChat')->default(false);
            $table->boolean('lockSettingsDisablePublicChat')->default(false);
            $table->boolean('lockSettingsDisableNotes')->default(false);
            $table->boolean('lockSettingsHideUserList')->default(false);
            $table->boolean('lockSettingsLockOnJoin')->default(true);
            $table->boolean('lockSettingsLockOnJoinConfigurable')->default(false);
            $table->boolean('lockSettingsHideViewersCursor')->default(false);
            $table->string('guestPolicy')->default('ALWAYS_ACCEPT');
            $table->boolean('meetingKeepEvents')->default(false);
            $table->boolean('endWhenNoModerator')->default(false);
            $table->integer('endWhenNoModeratorDelayInMinutes')->default(1);
            $table->string('meetingLayout')->default('SMART_LAYOUT');
            $table->integer('learningDashboardCleanupDelayInMinutes')->default(2);
            $table->boolean('allowModsToEjectCameras')->default(false);
            $table->boolean('allowRequestsWithoutSession')->default(true);
            $table->integer('userCameraCap')->default(3);
            $table->integer('meetingCameraCap')->default(0);
            $table->integer('meetingExpireIfNoUserJoinedInMinutes')->default(30);
            $table->integer('meetingExpireWhenLastUserLeftInMinutes')->default(1);
            $table->string('groups')->nullable();
            $table->string('logo')->nullable();
            $table->string('disabledFeatures')->nullable();
            $table->string('disabledFeaturesExclude')->nullable();
            $table->boolean('preUploadedPresentationOverrideDefault')->default(true);
            $table->boolean('notifyRecordingIsOn')->default(false);
            $table->string('presentationUploadExternalUrl')->nullable();
            $table->string('presentationUploadExternalDescription')->nullable();
            $table->boolean('recordFullDurationMedia')->default(false);
            $table->string('preUploadedPresentation')->nullable();
            $table->string('preUploadedPresentationName')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};
