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
            'Blood Loss Amount',
            'Blood Loss Severity',
            'Pain Value',
            'Impact Grade Your Day',
            'General Health Energy Level',
            'Mood Positives Count',
            'Mood Negatives Count',
            'Exercise Any',
            'Notes Has Note',
        ];
    }

    public function map($pbac): array
    {
        return [
            $pbac->reported_date,
            $pbac->blood_loss ? $pbac->blood_loss['amount'] : null,
            $pbac->blood_loss ? $pbac->blood_loss['severity'] : null,
            $pbac->pain ? $pbac->pain['value'] : null,
            $pbac->impact ? $pbac->impact['gradeYourDay'] : null,
            $pbac->general_health ? $pbac->general_health['energyLevel'] : null,
            $pbac->mood ? count($pbac->mood['positives']) : null,
            $pbac->mood ? count($pbac->mood['negatives']) : null,
            $pbac->exercise ? ($pbac->exercise['any'] ? 1 : 0) : null,
            $pbac->notes ? ($pbac->notes['hasNote'] ? 1 : 0) : null,
        ];
    }
}
