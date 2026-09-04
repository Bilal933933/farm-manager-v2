<?php

use App\Models\Land;
use Illuminate\Support\Str;

test('returns only lands of the requesting company', function () {
    Land::create(['company_id' => 'comp-a', 'slug' => 'land-a-1', 'name' => 'Land A 1', 'area' => 10, 'ownership_type' => 'owned', 'owner_party_id' => (string) Str::uuid()]);
    Land::create(['company_id' => 'comp-a', 'slug' => 'land-a-2', 'name' => 'Land A 2', 'area' => 20, 'ownership_type' => 'owned', 'owner_party_id' => (string) Str::uuid()]);
    Land::create(['company_id' => 'comp-b', 'slug' => 'land-b-1', 'name' => 'Land B 1', 'area' => 30, 'ownership_type' => 'owned', 'owner_party_id' => (string) Str::uuid()]);

    $response = $this->getJson('/api/lands', $this->serviceHeaders('comp-a'));

    $response->assertStatus(200);
    $response->assertJsonCount(2, 'data');
    $response->assertJsonPath('data.0.company_id', 'comp-a');
    $response->assertJsonPath('data.1.company_id', 'comp-a');
});
