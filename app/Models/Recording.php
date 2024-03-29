<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Recording
 *
 * @property int $id
 * @property string $recordId
 * @property int $meetingId
 * @property \Illuminate\Support\Carbon|null $startTime
 * @property \Illuminate\Support\Carbon|null $endTime
 * @property int $state
 * @property int|null $size
 * @property string|null $url
 * @property int $processingTime
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Recording newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Recording newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Recording query()
 * @method static \Illuminate\Database\Eloquent\Builder|Recording whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recording whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recording whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recording whereMeetingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recording whereProcessingTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recording whereRecordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recording whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recording whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recording whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recording whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recording whereUrl($value)
 * @property-read \App\Models\Meeting|null $meeting
 * @mixin \Eloquent
 */
class Recording extends Model
{
    use HasFactory;

    protected $fillable = [
        'recordId',
        'meetingId',
        'startTime',
        'endTime',
        'state',
        'size',
        'url',
        'processingTime'
    ];

    protected $casts = [
        'recordId' => 'string',
        'meetingId' => 'integer',
        'startTime' => 'datetime',
        'endTime' => 'datetime',
        'state' => 'integer',
        'size' => 'integer',
        'url' => 'string',
        'processingTime' => 'integer'
    ];

    public function meeting()
    {
        return $this->belongsTo(Meeting::class, 'meetingId', 'id');
    }
}
