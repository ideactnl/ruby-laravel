<?php

namespace App\Services;

use App\Exports\PbacExport;
use App\Models\Pbac;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PbacExportService
{
    /**
     * Get PBAC data based on date range.
     */
    public function getDataByDateRange(string $startDate, string $endDate): Collection
    {
        return Pbac::with('participant')
            ->whereBetween('reported_date', [$startDate, $endDate])
            ->get();
    }

    /**
     * Export data to JSON.
     */
    public function exportToJson(string $startDate, string $endDate): string
    {
        return $this->getDataByDateRange($startDate, $endDate)->toJson(JSON_PRETTY_PRINT);
    }

    /**
     * Export data to CSV or Excel.
     */
    public function exportToExcel(string $startDate, string $endDate, string $format = 'xlsx'): BinaryFileResponse
    {
        $export = new PbacExport($startDate, $endDate);
        $filename = "pbac_export_{$startDate}_to_{$endDate}.".$format;

        return Excel::download($export, $filename);
    }
}
