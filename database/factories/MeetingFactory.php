<?php

namespace Database\Factories;

use App\Models\Meeting;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class MeetingFactory extends Factory
{
    protected $model = Meeting::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'userId' => User::withoutTrashed()->get()->random()->id,
            'meetingID' => null,
            'attendeePW' => Crypt::encrypt(Str::random()),
            'moderatorPW' => Crypt::encrypt(Str::random()),
            'date' => fake()->dateTimeBetween('now', '+6 months'),
            'name' => fake()->realTextBetween(10, 50),
            'welcome' => fake()->realText(250),
            'record' => fake()->boolean(70),
            'autoStartRecording' => true,
            'webcamsOnlyForModerator' => fake()->boolean(30),
            'muteOnStart' => fake()->boolean(50),
            'meetingLayout' => fake()->randomElement(['SMART_LAYOUT', 'CUSTOM_LAYOUT', 'PRESENTATION_FOCUS', 'VIDEO_FOCUS']),
        ];

    }

    public function configure(): static
    {
        return $this->afterMaking(function (Meeting $meeting) {
            // ...
        })->afterCreating(function (Meeting $meeting) {

            //organizer
            $organizer = new Participant();
            $organizer->userId = $meeting->userId;
            $organizer->meetingId = $meeting->id;
            $organizer->isModerator = true;
            $organizer->isOrganizer = true;
            $organizer->defaultLayout = $meeting->meetingLayout;
            $organizer->redirect = false;
            $organizer->errorRedirectUrl = null;
            $organizer->guest = false;
            $organizer->excludeFromDashboard = false;

            $organizer->save();


            /**
             * 'userId',
             * 'meetingId',
             * 'isModerator',
             * //'createTime',
             * 'defaultLayout',
             * 'redirect',
             * 'errorRedirectUrl',
             * 'guest',
             * 'excludeFromDashboard'
             */
            for ($i = 0; $i < fake()->numberBetween(5, 100); $i++) {
                $randomUser = User::withoutTrashed()->get()->random();
                try {
                    if ($randomUser->id !== $organizer->id) {
                        $participant = new Participant();
                        $participant->userId = $randomUser->id;
                        $participant->meetingId = $meeting->id;
                        $participant->isModerator = fake()->boolean(4);
                        $participant->isOrganizer = false;
                        $participant->defaultLayout = $meeting->meetingLayout;
                        $participant->redirect = false;
                        $participant->errorRedirectUrl = null;
                        $participant->guest = false;
                        $participant->excludeFromDashboard = false;
                        $participant->save();
                    }
                } catch (\Exception $exception) {
                    dump($exception->getMessage());
                }

            }
        });
    }
}
