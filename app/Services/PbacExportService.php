<?php

namespace App\Services;

use App\Exports\ParticipantPbacExport;
use App\Helpers\CommonHelper;
use App\Jobs\AdminPbacExportJob;
use App\Jobs\ExportParticipantPbacCsv;
use App\Jobs\GenerateParticipantPbacChartPdf;
use App\Models\Participant;
use App\Models\Pbac;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class PbacExportService
{
    /**
     * Get summarized chart data for a participant within a date range.
     */
    public function getParticipantPbacData(int $participantId, string $startDate, string $endDate): Collection
    {
        return Pbac::where('participant_id', $participantId)
            ->whereBetween('reported_date', [$startDate, $endDate])
            ->orderBy('reported_date')
            ->get()
            ->groupBy('reported_date')
            ->map(function ($items, $date) {
                return [
                    'date' => $date,
                    'total_score' => $items->sum(function ($item) {
                        return $item->blood_loss ? $item->blood_loss['amount'] : 0;
                    }),
                ];
            })
            ->values();
    }

    /**
     * Queue researcher export (all participants) and return tracking payload.
     */
    public function queueAdminExport(Request $request, int $userId): array
    {
        [$startDate, $endDate] = CommonHelper::getDateRangeFromPreset(
            $request->input('preset', 'month'),
            $request
        );

        $format = $request->input('format', 'csv');
        $ext = $format === 'xlsx' ? 'xlsx' : ($format === 'json' ? 'json' : 'csv');
        $filename = 'pbac_all_'.$startDate.'_to_'.$endDate.'_'.Str::random(6).'.'.$ext;

        $tracker = app(ExportTrackingService::class);
        $job = $tracker->createJob(0, 'csv', [
            'user_id' => $userId,
            'preset' => $request->input('preset'),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'format' => $ext,
            'filename' => $filename,
        ]);

        AdminPbacExportJob::dispatch($startDate, $endDate, $ext, $filename, $job->id);

        return $tracker->toPayload($job);
    }

    /**
     * Export participant PBAC data to Excel/CSV/JSON based on request.
     */
    public function exportForParticipant(Request $request, int $participantId): Response
    {
        [$startDate, $endDate] = CommonHelper::getDateRangeFromPreset(
            $request->input('preset', 'month'),
            $request
        );

        $format = $request->input('format', 'xlsx');
        $filename = "participant_pbac_export_{$startDate}_to_{$endDate}.".$format;

        if ($format === 'json') {
            $data = Pbac::where('participant_id', $participantId)
                ->whereBetween('reported_date', [$startDate, $endDate])
                ->orderBy('reported_date')
                ->get();

            return response()->json($data, 200, [
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                'Content-Type' => 'application/json',
            ]);
        }

        $export = new ParticipantPbacExport($participantId, $startDate, $endDate);

        return Excel::download($export, $filename);
    }

    /**
     * Export PBAC chart to PDF.
     */
    public function exportPdfChartForParticipant(Request $request, int $participantId): Response
    {
        [$startDate, $endDate] = CommonHelper::getDateRangeFromPreset(
            $request->input('preset', 'month'),
            $request
        );

        $data = $this->getParticipantPbacData($participantId, $startDate, $endDate);
        $participant = Participant::find($participantId);

        $pdf = Pdf::loadView('exports.participant-pbac-chart-pdf', [
            'data' => $data,
            'participant' => $participant,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);

        return $pdf->download("pbac_chart_{$startDate}_to_{$endDate}.pdf");
    }

    /**
     * Format PBAC records into chart-ready structure.
     * Returns the same nested structure as the participant API for consistency.
     */
    public function getChartDataFormatted(Collection $pbacRecords): Collection
    {
        return $pbacRecords->map(function ($record) {
            return [
                'reported_date' => $record->reported_date,
                'pillars' => [
                    'blood_loss' => $record->blood_loss,
                    'pain' => $record->pain,
                    'impact' => $record->impact,
                    'general_health' => $record->general_health,
                    'mood' => $record->mood,
                    'stool_urine' => $record->stool_urine,
                    'sleep' => $record->sleep,
                    'diet' => $record->diet,
                    'exercise' => $record->exercise,
                    'sex' => $record->sex,
                    'notes' => $record->notes,
                ],
            ];
        });
    }

    /**
     * Queue participant CSV export and return tracking payload.
     */
    public function queueParticipantCsv(Request $request, int $participantId, string $context = 'participant'): array
    {
        [$startDate, $endDate] = CommonHelper::getDateRangeFromPreset(
            $request->input('preset', 'month'),
            $request
        );

        $filename = 'pbac_export_'.$startDate.'_to_'.$endDate.'_'.Str::random(6).'.csv';

        $tracker = app(ExportTrackingService::class);
        $job = $tracker->createJob($participantId, 'csv', [
            'preset' => $request->input('preset'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'filename' => $filename,
            'context' => $context,
        ]);

        ExportParticipantPbacCsv::dispatch($participantId, $startDate, $endDate, $filename, $job->id);

        return $tracker->toPayload($job);
    }

    /**
     * Queue PDF generation from a base64 chart image and return tracking payload.
     */
    public function queueChartPdfFromImage(Request $request, int $participantId, string $context = 'participant'): array
    {
        $imageData = $request->input('chart_image');
        $preset = $request->input('preset');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $filename = 'pbac_chart_'.($startDate ?: 'start').'_to_'.($endDate ?: 'end').'_'.Str::random(6).'.pdf';

        $tracker = app(ExportTrackingService::class);
        $job = $tracker->createJob($participantId, 'pdf', [
            'preset' => $preset,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'filename' => $filename,
            'context' => $context,
        ]);

        GenerateParticipantPbacChartPdf::dispatch(
            $participantId,
            $imageData,
            $preset,
            $startDate,
            $endDate,
            $filename,
            $job->id
        );

        return $tracker->toPayload($job);
    }
}
