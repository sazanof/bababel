<?php

namespace App\Console\Commands;

use App\Helpers\BababelHelper;
use App\Models\Recording;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SyncRecordingsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bababel:sync-recordings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize recordings from bigbluebutton server';

    protected bool $empty = false;
    protected int $total;
    protected int $limit = 10;
    protected int $offset = 0;
    protected int $pages = 0;
    protected int $start = 0;
    protected int $counter = 0;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Start syncing...');
        while (!$this->empty) {
            $this->getRecordingRecursive();
        }
    }

    private function getRecordingRecursive()
    {
        $recordingsQuery = BababelHelper::getRecordings($this->limit, $this->offset);
        if ($recordingsQuery->isSuccessful()) {
            $data = $recordingsQuery->getData();
            if ($data->key === "noRecordings") {
                $this->empty = true;
                return false;
            }
            $recordings = $data->recordings->recording;
            $pagination = $data->pagination;
            $this->total = (int)$pagination->totalElements;
            $this->limit = (int)$pagination->pageable->limit;
            $this->offset = (int)$pagination->pageable->offset;
            $this->empty = $pagination->empty === "true";
            $this->pages = ceil($this->total / $this->limit);
            $this->start = $this->offset + 1;
            $recordings = $recordings instanceof \stdClass ? [$recordings] : $recordings;
            foreach ($recordings as $recording) {
                $this->counter++;
                //dump($recording->playback);
                $toDB = [
                    'recordID' => $recording->recordID,
                    'meetingID' => $recording->meetingID,
                    'startTime' => Carbon::createFromTimestampMs($recording->startTime),
                    'endTime' => Carbon::createFromTimestampMs($recording->endTime),
                    'state' => $this->makeState($recording->state),
                    'size' => (int)$recording->size,
                    'url' => $recording->playback->format->url,
                    'processingTime' => (int)$recording->playback->format->processingTime
                ];
                Recording::updateOrInsert(['recordID' => $toDB['recordID']], $toDB);
            }
            dump("Limit $this->limit, offset $this->offset, pages $this->pages, counter $this->counter");
            if ($this->counter == $this->total) {
                $this->empty = true;
            }
            $this->offset += $this->limit;
        }
    }

    private function makeState(string $state)
    {
        return match ($state) {
            'unpublished' => 0,
            'published' => 1,
            default => throw new \Exception('Unexpected match value')
        };
    }
}
