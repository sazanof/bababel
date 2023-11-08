<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Participants
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Participant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Participant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Participant query()
 * @property int $id
 * @property int $userId
 * @property int $meetingId
 * @property int $isModerator
 * @property string|null $createTime
 * @property string|null $webVoiceConf
 * @property string $defaultLayout
 * @property int $redirect
 * @property string|null $errorRedirectUrl
 * @property int|null $guest
 * @property int $excludeFromDashboard
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Participant whereCreateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Participant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Participant whereDefaultLayout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Participant whereErrorRedirectUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Participant whereExcludeFromDashboard($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Participant whereGuest($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Participant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Participant whereIsModerator($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Participant whereMeetingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Participant whereRedirect($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Participant whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Participant whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Participant whereWebVoiceConf($value)
 * @property int $isOrganizer
 * @method static \Illuminate\Database\Eloquent\Builder|Participant whereIsOrganizer($value)
 * @property string|null $link
 * @method static \Illuminate\Database\Eloquent\Builder|Participant whereLink($value)
 * @mixin \Eloquent
 */
class Participant extends Model
{
    use HasFactory;

    protected $table = 'participants';

    protected $fillable = [
        'userId',
        'meetingId',
        'isModerator',
        'isOrganizer',
        //'createTime',
        'defaultLayout',
        'redirect',
        'errorRedirectUrl',
        'guest',
        'excludeFromDashboard',
        'link'
    ];

    public static array $selectableFields = [
        'participants.userId',
        'participants.meetingId',
        'participants.isModerator',
        'participants.isOrganizer',
        'participants.link',
    ];
}
