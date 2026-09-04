<?php

use App\Models\Contract;
use App\Models\Cost;
use App\Models\Crop;
use App\Models\Harvest;
use App\Models\Land;
use App\Models\Sale;
use App\Models\Season;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}

function createLand(string $company = 'comp-a', array $overrides = []): Land
{
    return Land::create(array_merge([
        'company_id' => $company,
        'slug' => 'land-'.Str::random(8),
        'name' => 'Land '.Str::random(6),
        'area' => 10,
        'ownership_type' => 'owned',
        'owner_party_id' => (string) Str::uuid(),
    ], $overrides));
}

function createCrop(array $overrides = []): Crop
{
    return Crop::create(array_merge([
        'name' => 'Crop '.Str::random(8),
        'unit' => 'kg',
    ], $overrides));
}

function createSeason(Land $land, array $overrides = []): Season
{
    return Season::create(array_merge([
        'land_id' => $land->id,
        'product_id' => (string) Str::uuid(),
        'start_date' => '2026-01-01',
    ], $overrides));
}

function createHarvest(Season $season, array $overrides = []): Harvest
{
    return Harvest::create(array_merge([
        'season_id' => $season->id,
        'date' => '2026-03-01',
        'total_quantity' => 100,
        'unit' => 'kg',
    ], $overrides));
}

function createCost(Season $season, array $overrides = []): Cost
{
    return Cost::create(array_merge([
        'season_id' => $season->id,
        'cost_type' => 'labor',
        'amount' => 500,
        'date' => '2026-02-01',
    ], $overrides));
}

function createSale(Season $season, array $overrides = []): Sale
{
    return Sale::create(array_merge([
        'season_id' => $season->id,
        'buyer_party_id' => (string) Str::uuid(),
        'quantity' => 10,
        'unit' => 'kg',
        'unit_price' => 5,
        'total_price' => 50,
        'date' => '2026-04-01',
    ], $overrides));
}

function createContract(Land $land, array $overrides = []): Contract
{
    return Contract::create(array_merge([
        'land_id' => $land->id,
        'contract_type' => 'rent_in',
        'counterparty_party_id' => (string) Str::uuid(),
        'financial_value' => 1000,
        'start_date' => '2026-01-01',
    ], $overrides));
}
