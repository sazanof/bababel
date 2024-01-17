<?php

namespace Database\Seeders;

use App\Models\Notification;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FillNotificationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Notification::updateOrInsert(
            ['key' => 'meeting.create'],
            ['transKey' => 'notifications.caption.meeting_create'],
        );
        Notification::updateOrInsert(
            ['key' => 'meeting.update.date'],
            ['transKey' => 'notifications.caption.meeting_update_date'],
        );
        Notification::updateOrInsert(
            ['key' => 'meeting.delete'],
            ['transKey' => 'notifications.caption.meeting_delete'],
        );
        Notification::updateOrInsert(
            ['key' => 'participant.create'],
            ['transKey' => 'notifications.caption.participant_create'],
        );
        Notification::updateOrInsert(
            ['key' => 'participant.delete'],
            ['transKey' => 'notifications.caption.participant_delete'],
        );
        Notification::updateOrInsert(
            ['key' => 'recording.ready'],
            ['transKey' => 'notifications.caption.recording_ready'],
        );
    }
}
