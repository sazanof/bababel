<?php

namespace App\Helpers;

use App\Exceptions\MeetingsException;
use App\Http\Requests\MeetingRequest;
use App\Models\Document;
use App\Models\Meeting;
use App\Models\Participant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MeetingFormRequest
{

    /** @var array|null */
    protected ?array $participants;
    /** @var array */
    protected array $meeting;
    /** @var array|UploadedFile|UploadedFile[] */
    protected null|array|UploadedFile $files;
    protected ?User $owner;
    protected MeetingRequest $request;
    protected ?int $id = null;
    const PATH = 'public/meeting/';

    public function __construct(MeetingRequest $request)
    {
        $this->request = $request;
        $this->owner = $request->user();
        $this->participants = null;
        $this->files = null;
        $this->meeting = [];
        $this->prepareRequest();
        return $this;
    }

    /**
     * @return array
     */
    public function getMeeting(): array
    {
        return $this->meeting;
    }

    /**
     * @return ?array
     */
    public function getParticipants(): ?array
    {
        return $this->participants;
    }

    /**
     * @param array $meeting
     */
    public function setMeeting(array $meeting): void
    {
        $this->meeting = $meeting;
    }

    /**
     * @param array $participants
     */
    public function setParticipants(array $participants): void
    {
        $this->participants = array_map(function ($item) {
            return [
                'id' => (int)$item['id'],
                'isModerator' => in_array($item['isModerator'], ['true', '1', 1, true], true)
            ];
        }, $participants);
    }

    public function addParticipant(array $participant): void
    {
        if ($this->getParticipants() === null) {
            $this->setParticipants([$participant]);
        } else {
            $this->participants = array_merge_recursive($this->getParticipants(), [$participant]);
        }
    }


    /**
     * @return ?array
     */
    public function getFiles(): ?array
    {
        return $this->files;
    }

    /**
     * @param array $files
     */
    public function setFiles(array $files): void
    {
        $this->files = $files;
    }

    /**
     * @return void
     */
    protected function prepareRequest(): void
    {
        $data = $this->request->all();
        $data['meetingID'] = BababelHelper::generateMeetingId();
        foreach ($data as $key => $item) {
            if (in_array($item, ['true', 'false'])) {
                $data[$key] = $item === "true";
            }
        }
        if ($this->request->has('participants') && !empty($this->request->get('participants'))) {
            $this->setParticipants($this->request->get('participants')); // set participants data for db
        }
        if ($this->request->has('files')) {
            $this->setFiles($this->request->file('files'));
        }
        unset($data['participants']);
        $data['userId'] = $this->owner->id;
        $data['date'] = Carbon::parse($data['date']);
        unset($data['id']);

        //TODO create Meeting instantly by new Meeting()
        $this->setMeeting($data);
    }

    public function prepareParticipantsToDb(Meeting $meeting)
    {
        $ar = [];
        $i = 0;
        if (!is_null($this->getParticipants())) {
            foreach ($this->getParticipants() as $participant) {
                $ar[$i]['userId'] = intval($participant['id']);
                $ar[$i]['meetingId'] = $meeting->id;
                $ar[$i]['isModerator'] = $participant['isModerator'];
                $ar[$i]['isOrganizer'] = intval($participant['id']) === $meeting->userId;
                $ar[$i]['redirect'] = !empty($participant['redirect']) && $participant['redirect'] === "true";
                $ar[$i]['errorRedirectUrl'] = $participant['errorRedirectUrl'] ?? null;
                $ar[$i]['guest'] = !empty($participant['guest']) && $participant['guest'] === "true";
                $ar[$i]['excludeFromDashboard'] = !empty($participant['excludeFromDashboard']) && $participant['excludeFromDashboard'] === "true";
                $i++;
            }
        }

        return $ar;
    }

    /**
     * @return void
     */
    private function addOwnerAsModerator()
    {
        $this->addParticipant(
            [
                'id' => $this->owner->id,
                'isModerator' => true
            ]
        );
    }

    /**
     * @return mixed
     * @throws MeetingsException
     * @throws \Throwable
     */
    public function add()
    {

        if (empty($this->getMeeting())) {
            throw new MeetingsException();
        }
        return DB::transaction(function () {
            $meeting = Meeting::create($this->getMeeting());

            $this->addOwnerAsModerator();
            Participant::insertOrIgnore($this->prepareParticipantsToDb($meeting));
            $this->addFiles($meeting);
            return $meeting;
        });
    }

    /**
     * @return mixed
     * @throws \Throwable
     */
    public function edit()
    {

        $clearDeletedUsersIDs = [];
        $addedUserIDs = [];
        $this->id = $this->request->has('id') ? (int)$this->request->get('id') : null;
        $meeting = Meeting::findOrFail($this->id);
        if ($this->meeting['meetingID']) {
            unset($this->meeting['meetingID']);
        }
        $participants = $meeting->participants();
        $postParticipants = $this->getParticipants();
        if ($postParticipants !== null) {
            $userIDSs = $participants->exists() ? $participants->get()->map(function (User|Participant $p) {
                return $p->id;
            }) : [];
            $postUserIDs = array_map(function ($item) {
                return (int)$item['id'];
            }, $postParticipants);
            // Находим удаленных из формы на фронтэнде пользователей - участников
            $deletedUsersIDs = array_diff(is_array($userIDSs) ? $userIDSs : $userIDSs->toArray(), $postUserIDs);
            $addedUserIDs = array_diff($postUserIDs, is_array($userIDSs) ? $userIDSs : $userIDSs->toArray());
            // Фильтруем массив удаленных пользователей. Оставляем организатора встречи.
            $clearDeletedUsersIDs = array_filter($deletedUsersIDs, function ($id) use ($meeting) {
                return $id !== $meeting->userId;
            });
            //$ids идентификаторы текущих участников
        } else {
            $this->addOwnerAsModerator();
        }
        return DB::transaction(function () use ($meeting, $clearDeletedUsersIDs, $addedUserIDs) {
            /** @var Carbon $d */
            //dd($this->id, $clearDeletedUsersIDs, $this->getMeeting(), $this->prepareParticipantsToDb($meeting));
            $meeting->update($this->getMeeting());
            foreach ($this->prepareParticipantsToDb($meeting) as $p) {
                Participant::updateOrCreate([
                    'userId' => $p['userId'],
                    'meetingId' => $p['meetingId']
                ], $p);
                if (!empty($clearDeletedUsersIDs)) {
                    Participant
                        ::whereIn('userId', $clearDeletedUsersIDs)
                        ->where('meetingId', $meeting->id)
                        ->delete();
                }
            }
            $this->addFiles($meeting);
            $meeting->refresh();
            // Deleted users collection
            if (!empty($clearDeletedUsersIDs)) {
                $notyDeleted = User::whereIn('id', $clearDeletedUsersIDs)->get();
                if ($notyDeleted !== null) {
                    foreach ($notyDeleted as $user) {
                        NotificationHelper::sendNotificationsOnParticipantDelete($user, $meeting);
                    }
                }
            }
            if (!empty($addedUserIDs)) {
                $notyAdded = User::whereIn('id', $addedUserIDs)->get();
                if ($notyAdded !== null) {
                    foreach ($notyAdded as $user) {
                        NotificationHelper::sendNotificationsOnParticipantCreate($user, $meeting);
                    }
                }
            }
            return $meeting;
        });
    }

    /**
     * @param Meeting $meeting
     * @return bool
     */
    public function isDateChanged(Meeting $meeting): bool
    {
        $requestDate = Carbon::make($this->request->get('date'))->toISOString(true);
        $meetingDate = Carbon::make($meeting->date)->toISOString(true);
        return $requestDate !== $meetingDate;
    }

    public function addFiles(Meeting $meeting)
    {
        $files = $this->getFiles();
        if (!is_null($files)) {
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $basename = pathinfo($name);
                $filePath = self::PATH .
                    $meeting->id .
                    DIRECTORY_SEPARATOR .
                    Str::slug($basename['filename']) .
                    '.' .
                    $basename['extension'];
                $id = Document::updateOrCreate(
                    [
                        'userId' => $meeting->userId,
                        'meetingId' => $meeting->id,
                        'path' => $filePath,
                    ],
                    [
                        'mime' => $file->getClientMimeType(),
                        'path' => $filePath,
                        'name' => $file->getClientOriginalName()
                    ]
                );
                if ($id) {
                    Storage::put($filePath, $file->getContent());
                }
            }
        }

    }
}
