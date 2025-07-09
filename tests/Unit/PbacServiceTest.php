<?php

use App\Services\PbacService;
use App\Models\User;
use App\Models\Pbac;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

describe('PbacService@getUserPbacs', function () {
    it('returns null if user not found and id is given', function () {
        $service = app(PbacService::class);
        $result = $service->getUserPbacs('nonexistent', 1);
        expect($result)->toBeNull();
    });

    it('returns empty collection if user not found and id is not given', function () {
        $service = app(PbacService::class);
        $result = $service->getUserPbacs('nonexistent');
        expect($result)->toHaveCount(0);
    });

    it('returns PBAC collection for existing user', function () {
        $user = User::factory()->create(['registration_number' => 'abc123']);
        Pbac::factory()->count(2)->create(['user_id' => $user->id]);
        $service = app(PbacService::class);
        $result = $service->getUserPbacs('abc123');
        expect($result)->toHaveCount(2);
    });
});
