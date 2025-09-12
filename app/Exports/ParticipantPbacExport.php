<?php

namespace App\Exports;

use App\Models\Pbac;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ParticipantPbacExport implements FromQuery, ShouldQueue, WithHeadings, WithMapping
{
    use Exportable;

    protected int $participantId;

    protected string $startDate;

    protected string $endDate;

    public function __construct(int $participantId, string $startDate, string $endDate)
    {
        $this->participantId = $participantId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function query(): Builder
    {
        return Pbac::query()
            ->where('participant_id', $this->participantId)
            ->whereBetween('reported_date', [$this->startDate, $this->endDate])
            ->orderBy('reported_date');
    }

    public function headings(): array
    {
        return [
            'Reported Date',
            'PBAC Score',
            'Pain Score',
            'Quality of Life',
            'Energy Level',
            'Spotting',
            'Influence Factor',
            'Pain Medication',
            'Defecation Complaints',
            'Urinating Complaints',
            'Quality of Sleep',
            'Exercise',
        ];
    }

    public function map($pbac): array
    {
        return [
            $pbac->reported_date,
            $pbac->pbac_score_per_day,
            $pbac->pain_score_per_day,
            $pbac->quality_of_life,
            $pbac->energy_level,
            $pbac->spotting_yes_no,
            $pbac->influence_factor,
            $pbac->pain_medication,
            $pbac->complaints_with_defecation,
            $pbac->complaints_with_urinating,
            $pbac->quality_of_sleep,
            $pbac->exercise,
        ];
    }
}
