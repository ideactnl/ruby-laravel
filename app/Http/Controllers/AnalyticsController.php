<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnalyticsController extends Controller
{
    /**
     * Display participant dashboard visit analytics.
     */
    public function index(Request $request)
    {
        $query = Participant::select('id', 'registration_number')
            ->withCount(['activities as dashboard_visits_count' => function ($query) {
                $query->where('log_name', 'participant-visits');
            }])
            ->withSum('sessions as total_duration_seconds', 'duration_seconds')
            ->withAvg('sessions as avg_duration_seconds', 'duration_seconds')
            ->with(['sessions']);

        if ($q = $request->input('q')) {
            $query->where('registration_number', 'like', "%{$q}%");
        }

        $sort = in_array($request->input('sort'), ['registration_number', 'dashboard_visits_count', 'total_duration_seconds', 'avg_duration_seconds']) ? $request->input('sort') : 'dashboard_visits_count';
        $dir = $request->input('dir') === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sort, $dir);

        $perPage = (int) $request->input('per_page', 10);
        /** @var \Illuminate\Pagination\LengthAwarePaginator<\App\Models\Participant> $participants */
        $participants = $query->paginate($perPage)->appends($request->query());

        if ($request->wantsJson() || $request->ajax() || $request->has('ajax')) {
            /** @var \Illuminate\Support\Collection<int, \App\Models\Participant> $collection */
            $collection = $participants->getCollection();

            return response()->json([
                /** @phpstan-ignore-next-line */
                'data' => $collection->map(function (Participant $p) {
                    $sectionBreakdown = [];
                    $formattedVisits = [];

                    foreach ($p->sessions as $session) {
                        foreach ($session->section_breakdown ?? [] as $section => $seconds) {
                            $label = $this->formatLabel($section);
                            $sectionBreakdown[$label] = ($sectionBreakdown[$label] ?? 0) + $seconds;
                        }

                        foreach ($session->interactions_breakdown ?? [] as $section => $count) {
                            $label = $this->formatLabel($section);
                            $formattedVisits[$label] = ($formattedVisits[$label] ?? 0) + $count;
                        }
                    }

                    $formattedSections = [];
                    foreach ($sectionBreakdown as $label => $seconds) {
                        if ($seconds > 0) {
                            $formattedSections[] = [
                                'name' => $label,
                                'duration' => $this->formatDuration((int) $seconds),
                                'seconds' => (int) $seconds,
                            ];
                        }
                    }
                    usort($formattedSections, fn ($a, $b) => $b['seconds'] <=> $a['seconds']);

                    $visitList = [];
                    foreach ($formattedVisits as $label => $count) {
                        $visitList[] = ['name' => $label, 'count' => $count];
                    }
                    usort($visitList, fn ($a, $b) => $b['count'] <=> $a['count']);

                    return [
                        'id' => $p->id,
                        'registration_number' => $p->registration_number,
                        'dashboard_visits_count' => $p->dashboard_visits_count,
                        'interaction_count' => array_sum($formattedVisits),
                        'interaction_list' => $visitList,
                        'total_duration' => $this->formatDuration((int) $p->total_duration_seconds),
                        'avg_duration' => $this->formatDuration((int) $p->avg_duration_seconds),
                        'section_breakdown' => $formattedSections,
                    ];
                }),
                'meta' => [
                    'current_page' => $participants->currentPage(),
                    'per_page' => $participants->perPage(),
                    'total' => $participants->total(),
                    'last_page' => $participants->lastPage(),
                ],
            ]);
        }

        return view('analytics.index', compact('participants'));
    }

    /**
     * Export participant analytics as a CSV download.
     */
    public function export(Request $request): StreamedResponse
    {
        $query = Participant::select('id', 'registration_number')
            ->withCount(['activities as dashboard_visits_count' => function ($query) {
                $query->where('log_name', 'participant-visits');
            }])
            ->withSum('sessions as total_duration_seconds', 'duration_seconds')
            ->withAvg('sessions as avg_duration_seconds', 'duration_seconds')
            ->with(['sessions']);

        if ($q = $request->input('q')) {
            $query->where('registration_number', 'like', "%{$q}%");
        }

        $sort = in_array($request->input('sort'), ['registration_number', 'dashboard_visits_count', 'total_duration_seconds', 'avg_duration_seconds']) ? $request->input('sort') : 'dashboard_visits_count';
        $dir = $request->input('dir') === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sort, $dir);

        $filename = 'participant_analytics_'.now()->format('Y-m-d_His').'.csv';

        return new StreamedResponse(function () use ($query) {
            $handle = fopen('php://output', 'w');

            fwrite($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'Registration ID',
                'Dashboard Visits',
                'Total Interactions',
                'Avg Session Duration',
                'Total Time',
                'Section Breakdown (Time)',
                'Section Breakdown (Visits)',
            ]);

            foreach ($query->lazy(500) as $p) {
                $sectionBreakdown = [];
                $formattedVisits = [];

                foreach ($p->sessions as $session) {
                    foreach ($session->section_breakdown ?? [] as $section => $seconds) {
                        $label = str_replace('|', '-', $this->formatLabel($section));
                        $sectionBreakdown[$label] = ($sectionBreakdown[$label] ?? 0) + $seconds;
                    }
                    foreach ($session->interactions_breakdown ?? [] as $section => $count) {
                        $label = str_replace('|', '-', $this->formatLabel($section));
                        $formattedVisits[$label] = ($formattedVisits[$label] ?? 0) + $count;
                    }
                }

                $timeBreakdown = collect($sectionBreakdown)
                    ->filter(fn ($s) => $s > 0)
                    ->sortDesc()
                    ->map(fn ($s, $name) => $name.': '.$this->formatDuration((int) $s))
                    ->implode(' | ');

                $visitBreakdown = collect($formattedVisits)
                    ->sortDesc()
                    ->map(fn ($count, $name) => $name.': '.$count)
                    ->implode(' | ');

                fputcsv($handle, [
                    $p->registration_number ?? 'N/A',
                    $p->dashboard_visits_count ?? 0,
                    array_sum($formattedVisits),
                    $this->formatDuration((int) $p->avg_duration_seconds),
                    $this->formatDuration((int) $p->total_duration_seconds),
                    $timeBreakdown ?: 'N/A',
                    $visitBreakdown ?: 'N/A',
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Format technical keys into user-friendly labels.
     */
    private function formatLabel($key)
    {
        $map = [
            'dashboard' => 'Home / Dashboard',
            'daily-view' => 'Daily View',
            'education' => 'Education',
            'self-management' => 'Self Management',
            'external-links' => 'External Links',
            'general-information' => 'General Information',
            'settings' => 'Settings',
            'export' => 'Data Export',
            'nav:sidebar:calendar' => 'Sidebar: Calendar',
            'nav:sidebar:daily_view' => 'Sidebar: Daily View',
            'nav:sidebar:education' => 'Sidebar: Education',
            'nav:sidebar:selfmanagement' => 'Sidebar: Self Management',
            'nav:sidebar:links_external_websites' => 'Sidebar: External Links',
            'nav:sidebar:export' => 'Sidebar: Export',
            'nav:sidebar:general_information' => 'Sidebar: Info',
            'nav:bottom:home' => 'Mobile: Home',
            'nav:bottom:daily_view' => 'Mobile: Daily View',
            'nav:bottom:education' => 'Mobile: Education',
            'nav:bottom:more' => 'Mobile: More',
        ];

        if (strpos($key, 'video:play:') === 0) {
            return 'Video: '.substr($key, 11);
        }

        return $map[$key] ?? ucwords(str_replace([':', '_', '-'], ' ', $key));
    }

    /**
     * Format seconds into human readable duration
     */
    private function formatDuration($seconds)
    {
        if ($seconds < 60) {
            return $seconds.'s';
        }
        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;
        if ($minutes < 60) {
            return $minutes.'m '.$remainingSeconds.'s';
        }
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        return $hours.'h '.$remainingMinutes.'m';
    }
}
