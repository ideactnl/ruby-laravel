<?php

namespace App\Jobs;

use App\Exports\PbacExport;
use App\Models\ExportJob;
use App\Models\Pbac;
use App\Services\ExportLogService;
use App\Services\ExportTrackingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Log;
use Maatwebsite\Excel\Excel as ExcelWriter;
use Maatwebsite\Excel\Facades\Excel;

class AdminPbacExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $startDate,
        public string $endDate,
        public string $format,
        public string $filename,
        public string $trackingJobId,
    ) {}

    public function handle(): void
    {
        $tracker = app(ExportTrackingService::class);
        $tracker->markProcessing($this->trackingJobId);
        $tracker->markProgress($this->trackingJobId, 15);

        try {
            $dir = 'exports/admin/pbac';
            $path = "$dir/{$this->filename}";
            Storage::disk('local')->makeDirectory($dir);

            if ($this->format === 'json') {
                $tracker->markProgress($this->trackingJobId, 40);
                $rows = Pbac::whereBetween('reported_date', [$this->startDate, $this->endDate])
                    ->orderBy('reported_date')
                    ->get();

                Storage::disk('local')->put($path, $rows->toJson(JSON_PRETTY_PRINT));
            } else {
                $tracker->markProgress($this->trackingJobId, 35);
                $export = new PbacExport($this->startDate, $this->endDate);
                $tracker->markProgress($this->trackingJobId, 55);
                $writer = $this->format === 'csv' ? ExcelWriter::CSV : ExcelWriter::XLSX;
                Excel::store($export, $path, 'local', $writer);
            }

            $tracker->markProgress($this->trackingJobId, 85);

            if (! Storage::disk('local')->exists($path)) {
                throw new \RuntimeException("Export file missing after store: $path");
            }

            $signed = null;
            try {
                $signed = URL::temporarySignedRoute(
                    'admin.pbac.exports.download', now()->addMinutes(30), ['jobId' => $this->trackingJobId]
                );
            } catch (\Throwable $e) {
                $signed = null;
            }

            $tracker->markCompleted($this->trackingJobId, $path, $signed);

            try {
                $ej = ExportJob::find($this->trackingJobId);
                if ($ej && isset($ej->meta['user_id'])) {
                    app(ExportLogService::class)->logCompleted((int) $ej->meta['user_id'], $ej);
                }
            } catch (\Throwable $e) {
            }

            Log::info('Admin PBAC export stored', [
                'start' => $this->startDate,
                'end' => $this->endDate,
                'format' => $this->format,
                'path' => $path,
            ]);
        } catch (\Throwable $e) {
            $tracker->markFailed($this->trackingJobId, $e->getMessage());
            try {
                $ej = ExportJob::find($this->trackingJobId);
                if ($ej && isset($ej->meta['user_id'])) {
                    app(ExportLogService::class)->logFailed((int) $ej->meta['user_id'], $ej, $e->getMessage());
                }
            } catch (\Throwable $ex) {
            }
            Log::error('Admin PBAC export failed', [
                'error' => $e->getMessage(),
                'start' => $this->startDate,
                'end' => $this->endDate,
                'format' => $this->format,
            ]);
            throw $e;
        }
    }
}
