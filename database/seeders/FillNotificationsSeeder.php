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
        Notification::insertOrIgnore([
            [
                'key' => 'meeting.create',
                'transKey' => 'notifications.caption.meeting_create'
            ],
            [
                'key' => 'meeting.update.date',
                'transKey' => 'notifications.caption.meeting_update_date'
            ],
            [
                'key' => 'meeting.delete',
                'transKey' => 'notifications.caption.meeting_delete'
            ],
            [
                'key' => 'participant.create',
                'transKey' => 'notifications.caption.participant_create'
            ],
            [
                'key' => 'participant.delete',
                'transKey' => 'notifications.caption.participant_delete'
            ]
        ]);
    }
}
