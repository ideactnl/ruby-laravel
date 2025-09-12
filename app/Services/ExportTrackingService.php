<?php

namespace App\Services;

use App\Models\ExportJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\URL;

class ExportTrackingService
{
    public function createJob(int $participantId, string $type, array $meta = []): ExportJob
    {
        $job = new ExportJob([
            'participant_id' => $participantId,
            'type' => $type,
            'status' => 'queued',
            'progress' => 0,
            'queued_at' => Carbon::now(),
            'meta' => $meta,
        ]);
        $job->save();

        return $job;
    }

    public function markQueued(string $jobId, ?string $queueJobId = null): void
    {
        ExportJob::whereKey($jobId)->update([
            'status' => 'queued',
            'queue_job_id' => $queueJobId,
        ]);
    }

    public function markProcessing(string $jobId): void
    {
        ExportJob::whereKey($jobId)->update([
            'status' => 'processing',
            'started_at' => Carbon::now(),
        ]);
    }

    public function markProgress(string $jobId, int $progress): void
    {
        ExportJob::whereKey($jobId)->update([
            'progress' => max(0, min(100, $progress)),
        ]);
    }

    public function markCompleted(string $jobId, ?string $filePath = null, ?string $downloadUrl = null): void
    {
        if (! $downloadUrl) {
            try {
                $downloadUrl = URL::temporarySignedRoute(
                    'participant.exports.download',
                    now()->addMinutes(30),
                    ['jobId' => $jobId]
                );
            } catch (\Throwable $e) {
                $downloadUrl = null;
            }
        }

        ExportJob::whereKey($jobId)->update([
            'status' => 'completed',
            'progress' => 100,
            'file_path' => $filePath,
            'download_url' => $downloadUrl,
            'finished_at' => Carbon::now(),
        ]);
    }

    public function markFailed(string $jobId, string $error): void
    {
        ExportJob::whereKey($jobId)->update([
            'status' => 'failed',
            'error' => $error,
            'finished_at' => Carbon::now(),
        ]);
    }

    public function getActiveForParticipant(int $participantId): ?ExportJob
    {
        return ExportJob::where('participant_id', $participantId)
            ->whereIn('status', ['queued', 'processing'])
            ->orderByDesc('queued_at')
            ->first();
    }

    public function getById(string $jobId): ?ExportJob
    {
        return ExportJob::find($jobId);
    }

    public function toPayload(ExportJob $job): array
    {
        $progress = (int) $job->progress;
        if ($job->status === 'processing' && $job->started_at) {
            $elapsed = Carbon::now()->diffInSeconds($job->started_at);
            $estimate = min(95, (int) round(($elapsed / 12) * 95));
            if ($estimate > $progress) {
                $progress = $estimate;
            }
        }

        return [
            'id' => $job->id,
            'type' => $job->type,
            'status' => $job->status,
            'progress' => $progress,
            'file_path' => $job->file_path,
            'download_url' => $job->download_url,
            'download_expires_at' => ($job->download_url && $job->finished_at)
                ? $job->finished_at->copy()->addMinutes(30)->toAtomString()
                : null,
            'queued_at' => optional($job->queued_at)->toAtomString(),
            'started_at' => optional($job->started_at)->toAtomString(),
            'finished_at' => optional($job->finished_at)->toAtomString(),
            'error' => $job->error,
            'meta' => $job->meta,
        ];
    }
}
