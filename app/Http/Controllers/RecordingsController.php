<?php

namespace App\Http\Controllers;

use App\Helpers\BababelHelper;
use App\Models\Meeting;
use App\Models\Recording;
use App\Models\User;
use Illuminate\Http\Request;

class RecordingsController extends Controller
{
    /**
     * @param int $id
     * @return bool[]
     * @throws \Throwable
     */
    public function deleteRecording(int $id): array
    {
        $record = Recording::find($id);
        $res = BababelHelper::deleteRecording($record);
        if ($res->isSuccessful()) {
            $record->deleteOrFail();
        }
        return ['success' => true];
    }
}
