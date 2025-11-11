<?php

namespace App\Services;

use App\Models\VideoContent;
use Illuminate\Support\Collection;

class VideoService
{
    /**
     * Get videos for a specific location (education or self-management)
     */
    public function getVideosForLocation(string $location): Collection
    {
        return VideoContent::active()
            ->forLocation($location)
            ->ordered()
            ->get()
            ->map(function ($video) {
                return $this->formatVideoData($video);
            });
    }

    /**
     * Get videos for daily view based on participant data and conditions
     */
    public function getVideosForDailyView(array $participantData): array
    {
        $videos = VideoContent::forDailyView()
            ->active()
            ->orderBy('order')
            ->get();

        $matchingVideos = [];

        foreach ($videos as $video) {
            if ($this->evaluateCondition($video->condition, $participantData)) {
                $matchingVideos[] = $this->formatVideoData($video);
            }
        }

        return $matchingVideos;
    }

    /**
     * Evaluate if a video's condition is met based on participant data
     */
    protected function evaluateCondition(?string $condition, array $data): bool
    {
        if (empty($condition)) {
            return false;
        }

        try {
            if (preg_match('/painscore\s*>\s*(\d+)/', $condition, $matches)) {
                $threshold = (int) $matches[1];
                $painScore = $this->getPainScore($data);
                return $painScore !== null && $painScore > $threshold;
            }

            if (str_contains($condition, 'menstruation_bloodloss')) {
                return $this->hasMenstruationBloodLoss($data);
            }

            if (str_contains($condition, 'general_health.any')) {
                return $this->hasAnyGeneralHealthSymptom($data);
            }

            if (preg_match('/energy_level\s*<\s*(-?\d+)/', $condition, $matches)) {
                $threshold = (int) $matches[1];
                $energyLevel = $this->getEnergyLevel($data);
                return $energyLevel !== null && $energyLevel < $threshold;
            }

            if (str_contains($condition, 'mood.')) {
                return $this->hasMoodSymptoms($data);
            }

            if (str_contains($condition, 'stool') || str_contains($condition, 'blood_in')) {
                return $this->hasStoolUrineIssues($data);
            }

            if (str_contains($condition, 'sleep')) {
                return $this->hasSleepIssues($data);
            }

            if (str_contains($condition, 'exercise_minutes')) {
                return $this->hasLowExercise($data);
            }

            return false;
        } catch (\Exception $e) {
            \Log::error('Error evaluating video condition', [
                'condition' => $condition,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get pain score from participant data
     */
    protected function getPainScore(array $data): ?int
    {
        return $data['pillars']['pain']['value'] ?? 
               $data['pillars']['pain']['painScore'] ?? 
               null;
    }

    /**
     * Check if participant has menstruation blood loss
     */
    protected function hasMenstruationBloodLoss(array $data): bool
    {
        $bloodLoss = $data['pillars']['blood_loss'] ?? [];
        
        return ($bloodLoss['amount'] ?? 0) > 0;
    }

    /**
     * Check if participant has any general health symptoms
     */
    protected function hasAnyGeneralHealthSymptom(array $data): bool
    {
        $generalHealth = $data['pillars']['general_health'] ?? [];
        
        $symptoms = $generalHealth['symptoms'] ?? [];
        
        if (!is_array($symptoms)) {
            return false;
        }
        
        return count($symptoms) > 0;
    }

    /**
     * Get energy level from participant data
     */
    protected function getEnergyLevel(array $data): ?int
    {
        return $data['pillars']['general_health']['energyLevel'] ?? 
               $data['pillars']['general_health']['energy_level'] ?? 
               null;
    }

    /**
     * Check if participant has mood symptoms
     */
    protected function hasMoodSymptoms(array $data): bool
    {
        $mood = $data['pillars']['mood'] ?? [];
        
        $negatives = $mood['negatives'] ?? [];
        
        if (!is_array($negatives)) {
            return false;
        }
        
        $targetSymptoms = [
            'anxious_stressed',
            'ashamed',
            'angry_irritable',
            'sad',
            'mood_swings',
            'worthless_guilty',
            'overwhelmed',
            'hopeless',
            'depressed_sad_down'
        ];
        
        foreach ($targetSymptoms as $symptom) {
            if (in_array($symptom, $negatives, true)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if participant has stool/urine issues
     */
    protected function hasStoolUrineIssues(array $data): bool
    {
        $stoolUrine = $data['pillars']['stool_urine'] ?? [];
        
        $stoolConsistency = $stoolUrine['stool']['consistency'] ?? null;
        if (in_array($stoolConsistency, ['hard', 'soft', 'watery', 'no_stool'])) {
            return true;
        }
        
        if (($stoolUrine['urine']['blood'] ?? false) === true ||
            ($stoolUrine['stool']['blood'] ?? false) === true) {
            return true;
        }
        
        return false;
    }

    /**
     * Check if participant has sleep issues
     */
    protected function hasSleepIssues(array $data): bool
    {
        $sleep = $data['pillars']['sleep'] ?? [];
        
        $sleepHours = $sleep['calculatedHours'] ?? $sleep['sleep_hours'] ?? null;
        if ($sleepHours !== null && $sleepHours < 7) {
            return true;
        }
        
        $sleepIssues = [
            'troubleAsleep',
            'wakeUpDuringNight',
            'tiredRested'
        ];
        
        $issueCount = 0;
        foreach ($sleepIssues as $issue) {
            if (($sleep[$issue] ?? false) === true) {
                $issueCount++;
            }
        }
        
        return $issueCount >= 2;
    }

    /**
     * Check if participant has low exercise
     */
    protected function hasLowExercise(array $data): bool
    {
        $exercise = $data['pillars']['exercise'] ?? [];
        
        $levels = $exercise['levels'] ?? [];
        
        if (!is_array($levels)) {
            return false;
        }
        
        return in_array('less_thirty', $levels, true);
    }

    /**
     * Format video data for API response
     */
    protected function formatVideoData(VideoContent $video): array
    {
        return [
            'id' => $video->id,
            'title' => $video->title,
            'location' => $video->location,
            'order' => $video->order,
            'video_url' => $video->video_url,
            'video_type' => $video->video_type,
            'video_id' => $video->video_id,
            'thumbnail_url' => $video->thumbnail_url,
            'embed_url' => $video->embed_url,
            'watch_url' => $video->watch_url,
        ];
    }
}
