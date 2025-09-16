<?php

namespace App\Services;

use App\Models\ExportJob;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ExportLogService
{
    protected string $logName = 'pbac-exports';

    public function logQueued(User $user, array $data): void
    {
        $fmt = strtoupper((string) ($data['format'] ?? ''));
        $sd = $data['start_date'] ?? '';
        $ed = $data['end_date'] ?? '';
        $preset = $data['preset'] ?? '';
        $desc = trim(sprintf('Queued %s %s → %s%s', $fmt, $sd, $ed, $preset ? " (preset: {$preset})" : ''));
        activity($this->logName)
            ->causedBy($user)
            ->withProperties([
                'event' => 'queued',
                'preset' => $data['preset'] ?? null,
                'start_date' => $data['start_date'] ?? null,
                'end_date' => $data['end_date'] ?? null,
                'format' => $data['format'] ?? null,
                'job_id' => $data['job_id'] ?? null,
                'filename' => $data['filename'] ?? null,
                'ip' => $data['ip'] ?? null,
                'user_agent' => $data['user_agent'] ?? null,
            ])
            ->log($desc ?: 'Admin export queued');
    }

    public function logCompleted(int $userId, ExportJob $job): void
    {
        if ($user = User::find($userId)) {
            $fmt = strtoupper((string) ($job->meta['format'] ?? ''));
            $sd = $job->meta['start_date'] ?? '';
            $ed = $job->meta['end_date'] ?? '';
            $desc = trim(sprintf('Completed %s %s → %s', $fmt, $sd, $ed));
            activity($this->logName)
                ->causedBy($user)
                ->withProperties([
                    'event' => 'completed',
                    'format' => $job->meta['format'] ?? null,
                    'preset' => $job->meta['preset'] ?? null,
                    'start_date' => $job->meta['start_date'] ?? null,
                    'end_date' => $job->meta['end_date'] ?? null,
                    'file_path' => $job->file_path,
                    'job_id' => $job->id,
                    'filename' => $job->meta['filename'] ?? null,
                    'queued_at' => optional($job->queued_at)->toAtomString(),
                    'finished_at' => optional($job->finished_at)->toAtomString(),
                ])
                ->log($desc ?: 'Admin export completed');
        }
    }

    public function logFailed(int $userId, ExportJob $job, string $error): void
    {
        if ($user = User::find($userId)) {
            $fmt = strtoupper((string) ($job->meta['format'] ?? ''));
            $sd = $job->meta['start_date'] ?? '';
            $ed = $job->meta['end_date'] ?? '';
            $desc = trim(sprintf('Failed %s %s → %s — %s', $fmt, $sd, $ed, $error));
            activity($this->logName)
                ->causedBy($user)
                ->withProperties([
                    'event' => 'failed',
                    'format' => $job->meta['format'] ?? null,
                    'preset' => $job->meta['preset'] ?? null,
                    'start_date' => $job->meta['start_date'] ?? null,
                    'end_date' => $job->meta['end_date'] ?? null,
                    'job_id' => $job->id,
                    'error' => $error,
                    'filename' => $job->meta['filename'] ?? null,
                ])
                ->log($desc ?: 'Admin export failed');
        }
    }

    public function listLogs(Request $request): LengthAwarePaginator
    {
        $query = Activity::where('log_name', $this->logName)->with('causer');

        if ($search = $request->input('search')) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('properties->format', 'like', "%{$search}%")
                    ->orWhere('properties->event', 'like', "%{$search}%")
                    ->orWhereHas('causer', fn ($cq) => $cq->where('name', 'like', "%{$search}%"));
            });
        }
        if ($format = $request->input('format')) {
            $query->where('properties->format', $format);
        }
        $status = $request->input('status');
        if ($status) {
            if (in_array($status, ['completed', 'failed'], true)) {
                $query->where('properties->event', $status);
            } else {
                $query->whereIn('properties->event', ['completed', 'failed']);
            }
        } else {
            $query->whereIn('properties->event', ['completed', 'failed']);
        }
        if ($userId = $request->input('user_id')) {
            $query->where('causer_id', $userId);
        }

        return $query->latest()->paginate(15)->appends($request->query());
    }
}
