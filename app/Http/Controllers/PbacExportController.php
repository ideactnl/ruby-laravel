<?php

namespace App\Http\Controllers;

use App\Models\ExportJob;
use App\Services\ExportLogService;
use App\Services\ExportTrackingService;
use App\Services\PbacExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class PbacExportController extends Controller
{
    protected $exportService;

    public function __construct(PbacExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    /**
     * Recent completed jobs for current user (limit 3).
     */
    public function recent(ExportTrackingService $tracker)
    {
        $userId = Auth::id();
        $jobs = ExportJob::where('meta->user_id', $userId)
            ->where('status', 'completed')
            ->orderByDesc('finished_at')
            ->limit(3)
            ->get();

        $data = $jobs->map(function (ExportJob $job) use ($tracker) {
            $payload = $tracker->toPayload($job);
            $expiresAt = now()->addMinutes(30);
            $downloadUrl = null;
            if ($job->file_path) {
                $downloadUrl = URL::temporarySignedRoute('admin.pbac.exports.download', $expiresAt, ['jobId' => (string) $job->id]);
            }

            return [
                'id' => (string) $job->id,
                'format' => $job->meta['format'] ?? null,
                'filename' => basename($job->file_path ?? ($job->meta['filename'] ?? '')),
                'completed_at' => optional($job->finished_at)->toAtomString(),
                'download_url' => $downloadUrl,
                'expired' => $downloadUrl ? now()->greaterThan($expiresAt) : true,
                'file_exists' => $job->file_path ? Storage::disk('local')->exists($job->file_path) : false,
            ];
        });

        return response()->json(['items' => $data]);
    }

    /**
     * Show export form.
     */
    public function showExportForm()
    {
        return view('pbac.export');
    }

    /**
     * Queue researcher export (all participants) and return job payload.
     */
    public function queue(Request $request)
    {
        $request->validate([
            'preset' => 'nullable|string|in:week,month,quarter,year,custom',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'format' => 'required|string|in:csv,xlsx,json',
        ]);

        if (($request->input('preset') === 'custom') && (! $request->input('start_date') || ! $request->input('end_date'))) {
            return response()->json(['error' => 'Start and end date required for custom preset'], 422);
        }

        $user = Auth::user();
        $job = $this->exportService->queueAdminExport($request, $user->id);

        app(ExportLogService::class)->logQueued($user, [
            'preset' => $request->input('preset'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'format' => $request->input('format'),
            'job_id' => $job['id'] ?? null,
            'filename' => $job['meta']['filename'] ?? null,
        ]);

        return response()->json(['job' => $job], 202);
    }

    /**
     * Get current user's active researcher export job.
     */
    public function active(Request $request, ExportTrackingService $tracker)
    {
        $userId = Auth::id();
        $active = ExportJob::where('meta->user_id', $userId)
            ->whereIn('status', ['queued', 'processing'])
            ->orderByDesc('queued_at')
            ->first();

        return response()->json(['job' => $active ? $tracker->toPayload($active) : null]);
    }

    /**
     * Get job status by id (researcher-owned).
     */
    public function status(string $jobId, ExportTrackingService $tracker)
    {
        $userId = Auth::id();
        $job = $tracker->getById($jobId);
        if (! $job || ($job->meta['user_id'] ?? null) !== $userId) {
            return response()->json(['error' => 'Not found'], 404);
        }

        return response()->json(['job' => $tracker->toPayload($job)]);
    }

    /**
     * Download generated file by job id (signed URL + ownership).
     */
    public function download(string $jobId, ExportTrackingService $tracker)
    {
        $userId = Auth::id();
        $job = $tracker->getById($jobId);
        if (! $job || ($job->meta['user_id'] ?? null) !== $userId) {
            return response()->json(['error' => 'Not found'], 404);
        }
        if ($job->status !== 'completed' || ! $job->file_path) {
            return response()->json(['error' => 'File not ready'], 409);
        }
        if (! Storage::disk('local')->exists($job->file_path)) {
            return response()->json(['error' => 'File missing'], 404);
        }
        $filename = basename($job->file_path);
        $stream = Storage::disk('local')->readStream($job->file_path);
        if (! is_resource($stream)) {
            return response()->json(['error' => 'Unable to read file'], 500);
        }

        return response()->streamDownload(function () use ($stream) {
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, $filename);
    }

    public function logs(Request $request)
    {
        $logs = app(ExportLogService::class)->listLogs($request);

        if ($request->wantsJson() || $request->ajax() || $request->boolean('ajax')) {
            $data = collect($logs->items())->map(function ($a) {
                return [
                    'id' => $a->id,
                    'user' => $a->causer?->name,
                    'format' => $a->properties['format'] ?? null,
                    'status' => $a->properties['event'] ?? null,
                    'description' => $a->description,
                    'file' => $a->properties['file_path'] ?? null,
                    'created_at' => optional($a->created_at)->toAtomString(),
                ];
            });

            return response()->json([
                'data' => $data,
                'meta' => [
                    'current_page' => $logs->currentPage(),
                    'per_page' => $logs->perPage(),
                    'total' => $logs->total(),
                    'last_page' => $logs->lastPage(),
                ],
            ]);
        }

        return view('pbac.logs', compact('logs'));
    }
}
