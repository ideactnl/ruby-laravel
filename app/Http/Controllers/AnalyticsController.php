<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use Illuminate\Http\Request;

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
            ->with(['sessions']); // Load sessions for manual aggregation of JSON parts

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
                'data' => $collection->map(function (\App\Models\Participant $p) {
                    // Aggregate sections and interactions manually across all sessions
                    $sectionBreakdown = [];
                    $formattedVisits = [];

                    foreach ($p->sessions as $session) {
                        // Section breakdown (Time on Page)
                        foreach ($session->section_breakdown ?? [] as $section => $seconds) {
                            $label = $this->formatLabel($section);
                            $sectionBreakdown[$label] = ($sectionBreakdown[$label] ?? 0) + $seconds;
                        }

                        // Aggregate Page Visits from interactions_breakdown
                        foreach ($session->interactions_breakdown ?? [] as $section => $count) {
                            $label = $this->formatLabel($section);
                            $formattedVisits[$label] = ($formattedVisits[$label] ?? 0) + $count;
                        }
                    }

                    // Format section names and durations for UI
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
