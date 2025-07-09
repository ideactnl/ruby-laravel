<?php

describe('Pbac@getPbacScorePerDayAttribute', function () {
    it('calculates PBAC score per day as sum of q4a to q4f', function () {
        $pbac = new \App\Models\Pbac([
            'q4a' => 1,
            'q4b' => 2,
            'q4c' => 3,
            'q4d' => 4,
            'q4e' => 5,
            'q4f' => 6,
        ]);
        expect($pbac->pbac_score_per_day)->toBe(21);
    });
});

describe('Pbac@getSpottingYesNoAttribute', function () {
    it('returns 1 if q3b is 1, else 0', function () {
        $pbac1 = new \App\Models\Pbac(['q3b' => 1]);
        $pbac0 = new \App\Models\Pbac(['q3b' => 0]);
        expect($pbac1->spotting_yes_no)->toBe(1);
        expect($pbac0->spotting_yes_no)->toBe(0);
    });
});
