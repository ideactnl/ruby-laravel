<?php

use App\Models\Participant;
use App\Models\Pbac;
use App\Services\PbacService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

describe('PbacService@getParticipantPbacs', function () {
    it('returns null if participant not found and id is given', function () {
        $service = app(PbacService::class);
        $result = $service->getParticipantPbacs('nonexistent', 1);
        expect($result)->toBeNull();
    });

    it('returns empty collection if participant not found and id is not given', function () {
        $service = app(PbacService::class);
        $result = $service->getParticipantPbacs('nonexistent');
        expect($result)->toHaveCount(0);
    });

    it('returns PBAC collection for existing participant', function () {
        $participant = Participant::factory()->create(['registration_number' => 'abc123']);
        Pbac::factory()->count(2)->create(['participant_id' => $participant->id]);
        $service = app(PbacService::class);
        $result = $service->getParticipantPbacs('abc123');
        expect($result)->toHaveCount(2);
    });
});
