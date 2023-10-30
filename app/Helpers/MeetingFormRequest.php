<?php

namespace App\Helpers;

use App\Exceptions\MeetingsException;
use App\Http\Requests\MeetingRequest;
use App\Models\Meeting;
use App\Models\Participants;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MeetingFormRequest
{

    /** @var array|null */
    protected ?array $participants;
    /** @var array */
    protected array $meeting;
    /** @var array|UploadedFile|UploadedFile[] */
    protected null|array|UploadedFile $files;

    public function __construct(MeetingRequest $request)
    {
        $this->participants = null;
        $this->files = null;
        $this->meeting = [];
        $this->prepareRequest($request);
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
        $this->participants = $participants;
    }

    /**
     * @return string
     */
    public function generateMeetingId(): string
    {
        return 'conf_' . Meeting::count('id') . '_' . uniqid();
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
     * @param MeetingRequest $request
     */
    protected function prepareRequest(MeetingRequest $request): void
    {
        $data = $request->all();
        foreach ($data as $key => $item) {
            if ($item === "true" || $item === "false") {
                $data[$key] = $item === "true";
            }
        }
        if ($request->has('participants') && !empty($request->get('participants'))) {
            $this->setParticipants($request->get('participants')); // set participants data for db
        }
        if ($request->has('files')) {
            $this->setFiles($request->file('files'));
        }
        unset($data['participants']);
        $data['userId'] = intval($data['userId']);
        $data['date'] = Carbon::parse($data['date']);
        $data['attendeePW'] = !empty($data['attendeePW'])
            ? Crypt::encrypt($data['attendeePW'])
            : self::generateAttendeePW();
        $data['moderatorPW'] = !empty($data['moderatorPW'])
            ? Crypt::encrypt($data['moderatorPW'])
            : self::generateModeratorPW();
        unset($data['id']);

        //TODO create Meeting instantly by new Meeting()
        $this->setMeeting($data);
    }

    public function prepareParticipantsToDb(Meeting $meeting)
    {
        $ar = [];
        $i = 0;
        foreach ($this->getParticipants() as $participant) {
            $ar[$i]['userId'] = intval($participant['id']);
            $ar[$i]['meetingId'] = $meeting->id;
            $ar[$i]['isModerator'] = Auth::id() === intval($participant['id']) || $participant['isModerator'] === "true";
            $ar[$i]['isOrganizer'] = intval($participant['id']) === $meeting->userId;
            $ar[$i]['redirect'] = !empty($participant['redirect']) && $participant['redirect'] === "true";
            $ar[$i]['errorRedirectUrl'] = $participant['errorRedirectUrl'] ?? null;
            $ar[$i]['guest'] = !empty($participant['guest']) && $participant['guest'] === "true";
            $ar[$i]['excludeFromDashboard'] = !empty($participant['excludeFromDashboard']) && $participant['excludeFromDashboard'] === "true";
            $i++;
        }
        return $ar;
    }

    /**
     * @return string
     */
    public function generateAttendeePW()
    {
        return Crypt::encrypt('att' . Str::random());
    }

    /**
     * @return string
     */
    public function generateModeratorPW()
    {
        return Crypt::encrypt('mod' . Str::random());
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
            Participants::insertOrIgnore($this->prepareParticipantsToDb($meeting));
            return $meeting;
        });
    }
}
