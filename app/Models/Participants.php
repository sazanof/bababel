<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Participants
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Participants newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Participants newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Participants query()
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
 * @method static \Illuminate\Database\Eloquent\Builder|Participants whereCreateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Participants whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Participants whereDefaultLayout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Participants whereErrorRedirectUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Participants whereExcludeFromDashboard($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Participants whereGuest($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Participants whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Participants whereIsModerator($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Participants whereMeetingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Participants whereRedirect($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Participants whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Participants whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Participants whereWebVoiceConf($value)
 * @mixin \Eloquent
 */
class Participants extends Model
{
    use HasFactory;

    protected $fillable = [
        'userId',
        'meetingId',
        'isModerator',
        //'createTime',
        'defaultLayout',
        'redirect',
        'errorRedirectUrl',
        'guest',
        'excludeFromDashboard'
    ];
}
