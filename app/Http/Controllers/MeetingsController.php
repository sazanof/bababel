<?php

namespace App\Http\Controllers;

use App\Exceptions\MeetingsException;
use App\Helpers\MeetingFormRequest;
use App\Http\Requests\MeetingRequest;
use App\Models\Document;
use App\Models\Meeting;
use App\Models\Participant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MeetingsController extends Controller
{
    /**
     * @param MeetingRequest $request
     * @return Model|Meeting
     * @throws MeetingsException
     * @throws \Throwable
     */
    public function addMeeting(MeetingRequest $request): Model|Meeting
    {
        $payload = $request->getPayload();
        if (is_null($payload->get('id'))) {
            return (new MeetingFormRequest($request))->add();
        }
        throw new MeetingsException();
    }

    /**
     * @param int $id
     * @param MeetingRequest $request
     * @return Meeting|Meeting[]|Collection|Model|null
     * @throws MeetingsException
     * @throws \Throwable
     */
    public function editMeeting(int $id, MeetingRequest $request): Model|array|Collection|Meeting|null
    {
        $meeting = Meeting::find($id);
        if (Auth::id() === $meeting->userId) {
            $postMeeting = new MeetingFormRequest($request);
            return $postMeeting->edit();
        }
        throw new MeetingsException();
    }

    /**
     * @param int $id
     * @return Meeting|Meeting[]|Collection|Model|null
     */
    public function getMeeting(int $id)
    {
        $meeting = Meeting::select(Meeting::$selectableFields)->find($id)->load(['participants', 'owner', 'documents']);
        if (Auth::id() === $meeting->userId) {
            return $meeting;
        }
        return null;
    }

    /**
     * @param string $criteria
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getMeetings(string $criteria, Request $request): LengthAwarePaginator
    {
        $meetings = Meeting
            ::with(['owner', 'participants', 'documents'])
            ->select(Meeting::$selectableFields);
        $limit = $request->get('limit') ?? 25;
        $page = $request->get('page') ?? 1;
        /** @var User $user */
        $user = $request->user();
        switch ($criteria) {
            case 'my':
                $meetings->where('date', '>=', Carbon::now());
                $meetings->where('userId', $user->id);
                break;
            case 'invitations':
                $meetings->where('date', '>=', Carbon::now());
                $meetings->whereNot('userId', $user->id);
                $meetings->whereHas('participants', function (Builder $builder) use ($user) {
                    return $builder->where('userId', $user->id);
                });
                break;

            case 'past':
                $meetings->where('date', '<', Carbon::now());
                $meetings->whereHas('participants', function (Builder $builder) use ($user) {
                    return $builder->where('userId', $user->id);
                });
                break;
        }
        return $meetings->orderBy('date', 'ASC')->paginate($limit, [], 'page', $page);
    }

    public function getDashboardMeetings(Request $request)
    {

        /** @var User $user */
        $user = $request->user();
        $today = Meeting
            ::with(['owner', 'participants', 'documents'])
            ->select(Meeting::$selectableFields);
        $today->whereBetween(
            'date',
            [
                Carbon::now(),
                Carbon::now()->add('1 day')->format('Y-m-d')
            ]
        );
        $today->where(function (Builder $builder) {
        });
        $today->whereHas('participants', function (Builder $builder) use ($user) {
            return $builder->where('userId', $user->id);
        });

        $recent = Meeting
            ::with(['owner', 'participants', 'documents'])
            ->select(Meeting::$selectableFields);
        $ids = $today->get()->map(function (Meeting $meeting) {
            return $meeting->id;
        })->toArray();
        $recent->whereHas('participants', function (Builder $builder) use ($user) {
            return $builder->where('userId', $user->id);
        });
        $recent->whereNotIn('id', $ids)->orderBy('date', 'ASC');
        $recent->where('date', '>=', Carbon::now());
        return [
            'today' => [
                'items' => $today->get(),
                'count' => $today->count()
            ],
            'recent' => [
                'items' => $recent->limit(5)->get(),
                'count' => $recent->count()
            ]
        ];
    }

    public function removeDocument(int $id)
    {
        $doc = Document::find($id);
        if ($doc instanceof Document) {
            if (Storage::delete($doc->path)) {
                // TODO check if folder is empty?
                $doc->delete();
            }
        }
    }

    /**
     * @param int $id
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function viewMeeting(int $id, Request $request)
    {
        /** @var User $user */
        $user = $request->user();
        $guest = false;
        $success = false;

        $meeting = Meeting::find($id);

        switch ($meeting->guestPolicy) {
            case 'ALWAYS_ACCEPT':
                $guest = true;
                $success = true;
                break;
            case 'ALWAYS_DENY':
                if (is_null($user)) {
                    throw new \Exception('Access denied to meeting');
                }
                $success = true;
                break;
            case 'ASK_MODERATOR':
                $success = true;
                break;
        }

        /** @var ?Participant $participant */
        $participant = null;
        if ($user instanceof User) {
            $participant =
                $meeting
                    ->participants()
                    ->where('userId', $user->id)
                    ->first();
        }
        $isParticipant = $user instanceof User && $participant instanceof Participant;
        $canJoin = ($meeting->status === 2 || $meeting->status === 1) && ($isParticipant || $success); // PENDING or STARTED
        $isStarted = $meeting->status === 1; // STARTED ON SERVER
        $isOwner = $meeting instanceof Meeting && $user instanceof User && $meeting->userId = $user->id;
        $isModerator = $meeting instanceof Meeting && $participant instanceof Participant && $participant->isModerator;

        if ($success) {
            $m = [
                'name' => $meeting->name,
                'welcome' => $meeting->welcome,
                'date' => $meeting->date
            ];
        } else {
            $m = null;
        }
        return [
            'isModerator' => $isModerator,
            'isParticipant' => $isParticipant,
            'isOwner' => $isOwner,
            'isStarted' => $isStarted,
            'canJoin' => $canJoin,
            'isGuest' => $guest,
            'success' => $success,
            'meeting' => $m
        ];
    }
}
