<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property string $id
 * @property int $participant_id
 * @property string $type
 * @property string $status
 * @property int|null $progress
 * @property string|null $file_path
 * @property string|null $download_url
 * @property \Carbon\CarbonInterface|null $queued_at
 * @property \Carbon\CarbonInterface|null $started_at
 * @property \Carbon\CarbonInterface|null $finished_at
 * @property string|null $error
 * @property string|null $queue_job_id
 * @property array|null $meta
 * @property \Carbon\CarbonInterface|null $created_at
 * @property \Carbon\CarbonInterface|null $updated_at
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class ExportJob extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'participant_id',
        'type',
        'status',
        'progress',
        'file_path',
        'download_url',
        'queued_at',
        'started_at',
        'finished_at',
        'error',
        'queue_job_id',
        'meta',
    ];

    protected $casts = [
        'queued_at' => 'datetime',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'meta' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function (self $model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }
}
