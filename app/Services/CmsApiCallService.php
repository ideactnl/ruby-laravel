<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CmsApiCallService
{
    protected $cmsApiUrl;

    protected $cmsApiKey;

    public function __construct()
    {
        $this->cmsApiUrl = config('cms.api_url');
        $this->cmsApiKey = config('cms.api_key');
    }

    public function call($requestOrFilters, ?string $location = null)
    {
        $systemLang = session('locale') ?? app()->getLocale();

        $filters = [];
        if ($requestOrFilters instanceof \Illuminate\Http\Request) {
            $filters = $requestOrFilters->all();
            if (! $location) {
                $location = $requestOrFilters->input('location');
            }
        } elseif (is_array($requestOrFilters)) {
            $filters = $requestOrFilters;
        }

        if (! $location && isset($filters['location'])) {
            $location = $filters['location'];
        }
        unset($filters['location']);

        $apiUrl = "$this->cmsApiUrl/api/v1/content?lang=$systemLang";
        if ($location) {
            $apiUrl .= '&location='.urlencode($location);
        }

        unset($filters['page']);

        foreach ($filters as $key => $value) {
            if ($value !== null && $value !== '' && $value !== false) {
                if (is_array($value)) {
                    foreach ($value as $v) {
                        $apiUrl .= "&$key=".urlencode((string) $v);
                    }
                } else {
                    if (is_bool($value)) {
                        $value = 'true';
                    }
                    $apiUrl .= "&$key=".urlencode((string) $value);
                }
            }
        }

        if (! $this->cmsApiUrl || ! $this->cmsApiKey) {
            return ['error' => true, 'message' => 'CMS API URL or API key is not configured'];
        }

        \Log::info('CMS API URL: '.$apiUrl);

        try {
            $response = Http::withoutVerifying()->withHeaders([
                'accept' => 'application/json',
                'x-api-key' => $this->cmsApiKey,
            ])->get($apiUrl);

            if (! $response->successful()) {
                return ['error' => true, 'message' => 'API request failed: '.$response->status()];
            }

            // \Log::info('CMS API Response: '.json_encode($response->json()));

            return ['data' => $response->json()];
        } catch (\Exception $e) {
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    public function buildFiltersFromParticipantData($participantData)
    {
        $filters = [];

        if (! isset($participantData['pillars'])) {
            return $filters;
        }

        $pillars = $participantData['pillars'];

        $this->mapPainLevel($pillars, $filters);
        $this->mapEnergyLevel($pillars, $filters);
        $this->mapSleepHours($participantData, $filters);
        $this->mapSportenTime($pillars, $filters);
        $this->mapBloodLoss($pillars, $filters);
        $this->mapStoolType($pillars, $filters);
        $this->mapMood($pillars, $filters);
        $this->mapGeneralHealth($pillars, $filters);

        return $filters;
    }

    private function mapPainLevel(array $pillars, array &$filters): void
    {
        if (isset($pillars['pain']['value'])) {
            $filters['pain-level'] = $pillars['pain']['value'];
        }
    }

    private function mapEnergyLevel(array $pillars, array &$filters): void
    {
        if (isset($pillars['general_health']['energyLevel'])) {
            $filters['energy-level'] = $pillars['general_health']['energyLevel'];
        }
    }

    private function mapSleepHours(array $participantData, array &$filters): void
    {
        if (isset($participantData['sleep_hours'])) {
            $filters['sleep-hours'] = $participantData['sleep_hours'];
        }
    }

    private function mapSportenTime(array $pillars, array &$filters): void
    {
        if (isset($pillars['exercise']['minutes'])) {
            $filters['sporten-time'] = $pillars['exercise']['minutes'];
        }
    }

    private function mapBloodLoss(array $pillars, array &$filters): void
    {
        $bloodLossAmount = $pillars['blood_loss']['amount'] ?? 0;
        if ($bloodLossAmount > 0) {
            $filters['menstruation-bloodloss'] = true;
        }

        if (! empty($pillars['stool_urine']['bloodInUrine'])) {
            $filters['blood-in-urine'] = true;
        }
    }

    private function mapStoolType(array $pillars, array &$filters): void
    {
        if (isset($pillars['stool_urine']['stoolType'])) {
            $stoolType = strtolower($pillars['stool_urine']['stoolType']);
            if ($stoolType === 'watery') {
                $stoolType = 'diarrhea';
            }
            if (in_array($stoolType, ['hard', 'soft', 'diarrhea', 'no stool'])) {
                $filters['stool'] = $stoolType;
            }
        }
    }

    private function mapMood(array $pillars, array &$filters): void
    {
        if (isset($pillars['mood']['negatives']) && is_array($pillars['mood']['negatives'])) {
            $mappedMoods = [];
            foreach ($pillars['mood']['negatives'] as $neg) {
                $mapped = match ($neg) {
                    'anxious_stressed' => 'anxious/stressed',
                    'ashamed' => 'ashamed',
                    'angry_irritable' => 'angry/irratable',
                    'sensitive' => 'sensitive',
                    'mood_swings' => 'mood swings',
                    'worthless_guilty' => 'worthless/guilty',
                    'overwhelmed' => 'overwhelmed',
                    'hopeless' => 'hopeless',
                    'depressed_sad_down' => 'depressed/sad/down',
                    default => null
                };
                if ($mapped) {
                    $mappedMoods[] = $mapped;
                }
            }
            if (! empty($mappedMoods)) {
                $filters['mood'] = $mappedMoods;
            }
        }
    }

    private function mapGeneralHealth(array $pillars, array &$filters): void
    {
        if (isset($pillars['general_health']['symptoms']) && is_array($pillars['general_health']['symptoms']) && count($pillars['general_health']['symptoms']) > 0) {
            $filters['boolean-domain-general-health'] = true;
        }
    }
}
