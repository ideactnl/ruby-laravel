<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CommonHelper
{
    /**
     * Apply date filtering based on preset or custom range.
     */
    public static function applyDateFilters(Builder $query, Request $request): Builder
    {
        $from = $request->input('from') ?? $request->input('from_date');
        $to = $request->input('to') ?? $request->input('to_date');
        $preset = $request->input('preset');

        if ($preset) {
            [$from, $to] = self::getDateRangeFromPreset($preset, $request);
        }

        if ($from) {
            $query->where('reported_date', '>=', $from);
        }

        if ($to) {
            $query->where('reported_date', '<=', $to);
        }

        return $query;
    }

    /**
     * Resolve start and end date from preset filter.
     */
    public static function getDateRangeFromPreset(string $preset, ?Request $request = null): array
    {
        $today = Carbon::today();

        return match ($preset) {
            'week' => [$today->copy()->startOfWeek()->toDateString(), $today->copy()->endOfWeek()->toDateString()],
            'month' => [$today->copy()->startOfMonth()->toDateString(), $today->copy()->endOfMonth()->toDateString()],
            'year' => [$today->copy()->startOfYear()->toDateString(), $today->copy()->endOfYear()->toDateString()],
            'custom' => [
                $request?->input('start_date') ?? $request?->input('from'),
                $request?->input('end_date') ?? $request?->input('to'),
            ],
            default => [null, null],
        };
    }
}
