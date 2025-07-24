<?php

namespace App\Exports;

use App\Models\Pbac;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PbacExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected string $startDate;

    protected string $endDate;

    public function __construct(string $startDate, string $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * Use a chunked query instead of loading all data into memory.
     */
    public function query(): Builder
    {
        return Pbac::query()
            ->whereBetween('reported_date', [$this->startDate, $this->endDate])
            ->orderBy('reported_date');
    }

    /**
     * Map each row to an array of values.
     */
    public function map($pbac): array
    {
        return collect($pbac->attributesToArray())->values()->all();
    }

    /**
     * Return column headings based on model fillables.
     */
    public function headings(): array
    {
        return (new Pbac)->getFillable();
    }
}
