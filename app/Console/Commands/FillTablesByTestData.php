<?php

namespace App\Console\Commands;

use App\Models\Meeting;
use App\Models\Participant;
use Illuminate\Console\Command;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

class FillTablesByTestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bababel:fill-tables-by-test-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fill tables by test data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Participant::truncate();
        Meeting::truncate();
        $statement = "ALTER TABLE `meetings` AUTO_INCREMENT = 10;";
        DB::unprepared($statement);
        $this->error('THIS WILL DESTROY ALL DATA IN SYSTEM');
        if ($this->confirm('Fill tables with test data?')) {
            if ($this->confirm('Are you sure?')) {
                for ($i = 0; $i < 100; $i++) {
                    Meeting::factory()->count(100)->create();
                }
            }

        }
    }
}
