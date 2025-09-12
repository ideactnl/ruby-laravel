<?php

namespace App\Jobs;

use App\Services\ExportTrackingService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GenerateParticipantPbacChartPdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $participantId,
        public string $imageBase64,
        public ?string $preset = null,
        public ?string $startDate = null,
        public ?string $endDate = null,
        public ?string $filename = null,
        public string $trackingJobId = ''
    ) {}

    public function handle(): void
    {
        $tracker = app(ExportTrackingService::class);
        $tracker->markProcessing($this->trackingJobId);
        $tracker->markProgress($this->trackingJobId, 15);

        $filename = $this->filename ?: 'pbac_chart.pdf';
        $path = "exports/participant/{$this->participantId}/{$filename}";

        try {
            // Start rendering
            $tracker->markProgress($this->trackingJobId, 35);
            $pdf = Pdf::loadView('pdf.participant-pbac-chart', [
                'imageBase64' => $this->imageBase64,
                'preset' => $this->preset,
                'startDate' => $this->startDate,
                'endDate' => $this->endDate,
            ]);

            // Writing to storage
            $tracker->markProgress($this->trackingJobId, 70);
            Storage::disk('local')->makeDirectory("exports/participant/{$this->participantId}");
            Storage::disk('local')->put($path, $pdf->output());

            // Completed
            $tracker->markCompleted($this->trackingJobId, $path, null);

            Log::info('Participant PBAC chart PDF stored', [
                'participant_id' => $this->participantId,
                'path' => $path,
            ]);
        } catch (\Throwable $e) {
            $tracker->markFailed($this->trackingJobId, $e->getMessage());
            Log::error('PDF export failed', [
                'participant_id' => $this->participantId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
