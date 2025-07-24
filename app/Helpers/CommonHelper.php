<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CommonHelper
{
    public static function applyDateFilters(Builder $query, Request $request): Builder
    {
        if ($request->filled('from')) {
            $query->where('reported_date', '>=', $request->input('from'));
        }

        if ($request->filled('to')) {
            $query->where('reported_date', '<=', $request->input('to'));
        }

        return $query;
    }
}
