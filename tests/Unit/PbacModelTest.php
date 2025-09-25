<?php

describe('Pbac@getBloodLossAttribute', function () {
    it('calculates blood loss amount as sum of bl_pad/tampon counts', function () {
        $pbac = new \App\Models\Pbac([
            'is_blood_loss_answered' => true,
            'bl_pad_large' => 1,
            'bl_pad_medium' => 2,
            'bl_pad_small' => 3,
            'bl_tampon_large' => 4,
            'bl_tampon_medium' => 5,
            'bl_tampon_small' => 6,
        ]);
        expect($pbac->blood_loss['amount'])->toBe(21);
    });
});

describe('Pbac@getBloodLossAttribute', function () {
    it('returns structured blood loss data with spotting flag', function () {
        $pbac1 = new \App\Models\Pbac(['spotting' => true, 'is_blood_loss_answered' => true]);
        $pbac0 = new \App\Models\Pbac(['spotting' => false, 'is_blood_loss_answered' => true]);
        expect($pbac1->blood_loss['flags']['spotting'])->toBe(true);
        expect($pbac0->blood_loss['flags']['spotting'])->toBe(false);
    });
});
