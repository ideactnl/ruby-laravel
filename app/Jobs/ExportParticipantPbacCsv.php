<?php

namespace App\Jobs;

use App\Exports\ParticipantPbacExport;
use App\Services\ExportTrackingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel as ExcelWriter;
use Maatwebsite\Excel\Facades\Excel;

class ExportParticipantPbacCsv implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $participantId,
        public string $startDate,
        public string $endDate,
        public string $filename,
        public string $trackingJobId
    ) {}

    public function handle(): void
    {
        $tracker = app(ExportTrackingService::class);
        $tracker->markProcessing($this->trackingJobId);
        $tracker->markProgress($this->trackingJobId, 10);

        $export = new ParticipantPbacExport($this->participantId, $this->startDate, $this->endDate);

        $path = "exports/participant/{$this->participantId}/{$this->filename}";

        Storage::disk('local')->makeDirectory("exports/participant/{$this->participantId}");

        try {
            $tracker->markProgress($this->trackingJobId, 30);
            Excel::store($export, $path, 'local', ExcelWriter::CSV);

            $tracker->markCompleted($this->trackingJobId, $path, null);

            Log::info('Participant PBAC CSV export stored', [
                'participant_id' => $this->participantId,
                'start' => $this->startDate,
                'end' => $this->endDate,
                'path' => $path,
            ]);
        } catch (\Throwable $e) {
            $tracker->markFailed($this->trackingJobId, $e->getMessage());
            Log::error('CSV export failed', [
                'participant_id' => $this->participantId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
