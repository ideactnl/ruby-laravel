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

    public function call($request)
    {
        $systemLang = session('locale') ?? app()->getLocale();
        $page = $request->input('page', 'education');
        $apiUrl = "$this->cmsApiUrl/api/v1/content?lang=$systemLang&page=$page";

        // Get all request data except page and convert to CMS API format
        $allFilters = $request->all();

        // Remove page from filters as it's already in URL
        unset($allFilters['page']);

        // Add non-null filters to URL with proper formatting
        foreach ($allFilters as $key => $value) {
            if ($value !== null && $value !== '' && $value !== false) {
                // Convert boolean values to string
                if (is_bool($value)) {
                    // @phpstan-ignore-next-line
                    $value = $value ? true : false;
                } elseif (is_numeric($value)) {
                    $value = $value;
                }
                $apiUrl .= "&$key=".urlencode($value);
            }
        }

        if (! $this->cmsApiUrl || ! $this->cmsApiKey) {
            return ['error' => true, 'message' => 'CMS API URL or API key is not configured'];
        }

        try {
            $response = Http::withoutVerifying()->withHeaders([
                'accept' => 'application/json',
                'x-api-key' => $this->cmsApiKey,
            ])->get($apiUrl);

            if (! $response->successful()) {
                return ['error' => true, 'message' => 'API request failed: '.$response->status()];
            }

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

        // Pain level (0-10)
        if (isset($pillars['pain']['value'])) {
            $filters['pain-level'] = $pillars['pain']['value'];
        }

        // Energy level (-10 to 10)
        if (isset($pillars['general_health']['energyLevel'])) {
            $filters['energy-level'] = $pillars['general_health']['energyLevel'];
        }

        // Sleep hours
        if (isset($participantData['sleep_hours'])) {
            $filters['sleep-hours'] = $participantData['sleep_hours'];
        }

        // Sport minutes
        if (isset($pillars['exercise']['minutes'])) {
            $filters['sport-minutes'] = $pillars['exercise']['minutes'];
        }

        // Boolean filters
        $booleanFilters = [
            'menstruation-bloodloss' => $pillars['blood_loss']['amount'] ?? null,
            'blood-in-urine' => $pillars['stool_urine']['bloodInUrine'] ?? null,
            'blood-in-stool' => $pillars['stool_urine']['bloodInStool'] ?? null,
            'pain-during-urination' => $pillars['stool_urine']['painDuringUrination'] ?? null,
            'pain-during-defecation' => $pillars['stool_urine']['painDuringDefecation'] ?? null,
        ];

        foreach ($booleanFilters as $key => $value) {
            if ($value !== null && $value !== false) {
                $filters[$key] = true;
            }
        }

        // Stool type
        if (isset($pillars['stool_urine']['stoolType'])) {
            $filters['stool-type'] = $pillars['stool_urine']['stoolType'];
        }

        // Mood domain variables
        $moodFilters = [
            'anxious-stressed' => $pillars['mood']['anxiousStressed'] ?? null,
            'ashamed' => $pillars['mood']['ashamed'] ?? null,
            'angry-irritable' => $pillars['mood']['angryIrritable'] ?? null,
            'sensitive' => $pillars['mood']['sensitive'] ?? null,
            'mood-swings' => $pillars['mood']['moodSwings'] ?? null,
            'worthless-guilty' => $pillars['mood']['worthlessGuilty'] ?? null,
            'overwhelmed' => $pillars['mood']['overwhelmed'] ?? null,
            'hopeless' => $pillars['mood']['hopeless'] ?? null,
            'depressed-sad-down' => $pillars['mood']['depressedSadDown'] ?? null,
        ];

        foreach ($moodFilters as $key => $value) {
            if ($value === true) {
                $filters[$key] = true;
            }
        }

        return $filters;
    }
}
