<?php

namespace App\Http\Controllers;

use App\Services\PbacExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;

class PbacExportController extends Controller
{
    protected $exportService;

    public function __construct(PbacExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    /**
     * Show export form.
     */
    public function showExportForm()
    {
        return view('pbac.export');
    }

    public function index(Request $request)
    {
        $query = Activity::where('log_name', 'pbac-exports')->with('causer');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhereHas('causer', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhere('properties->format', 'like', "%{$search}%");
            });
        }

        if ($format = $request->input('format')) {
            $query->where('properties->format', $format);
        }

        $logs = $query->latest()->paginate(10);

        return view('pbac.logs', compact('logs'));
    }

    /**
     * Handle export request.
     */
    public function export(Request $request)
    {
        $format = $request->input('format');
        $preset = $request->input('preset');

        if ($preset && $preset !== 'custom') {
            [$startDate, $endDate] = $this->getPresetRange($preset);
        } else {
            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
        }

        activity('pbac-exports')
            ->causedBy(Auth::user())
            ->withProperties([
                'preset' => $preset ?? 'custom',
                'start_date' => $startDate,
                'end_date' => $endDate,
                'format' => $format,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log("Exported PBAC data from {$startDate} to {$endDate}");

        if ($format === 'json') {
            $json = $this->exportService->exportToJson($startDate, $endDate);

            return response($json, 200)
                ->header('Content-Type', 'application/json')
                ->header('Content-Disposition', 'attachment; filename="pbac_export.json"');
        }

        return $this->exportService->exportToExcel($startDate, $endDate, $format);
    }

    /**
     * Get date range for preset.
     */
    protected function getPresetRange(string $preset): array
    {
        $now = Carbon::now();

        return match ($preset) {
            'week' => [$now->copy()->startOfWeek()->toDateString(), $now->copy()->endOfWeek()->toDateString()],
            'month' => [$now->copy()->startOfMonth()->toDateString(), $now->copy()->endOfMonth()->toDateString()],
            'quarter' => [$now->copy()->startOfQuarter()->toDateString(), $now->copy()->endOfQuarter()->toDateString()],
            'year' => [$now->copy()->startOfYear()->toDateString(), $now->copy()->endOfYear()->toDateString()],
            default => [now()->toDateString(), now()->toDateString()],
        };
    }
}
