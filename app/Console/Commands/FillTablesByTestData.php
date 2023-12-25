<?php

namespace App\Console\Commands;

use App\Models\Document;
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
        $this->error('THIS WILL DESTROY ALL DATA IN SYSTEM');
        if ($this->confirm('Fill tables with test data?')) {
            if ($this->confirm('Are you sure?')) {
                Participant::truncate();
                Meeting::truncate();
                Document::truncate();
                for ($i = 0; $i < 100; $i++) {
                    Meeting::factory()->count(100)->create();
                }
            }

        }
    }
}
