<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
