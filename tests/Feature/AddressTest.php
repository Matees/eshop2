<?php

use App\Address\Contracts\AddressClientInterface;
use App\Address\DTO\AddressResult;
use App\Address\DTO\CityResult;
use App\Address\DTO\StreetResult;
use Mockery\MockInterface;

test('cities endpoint returns cities', function () {
    $this->getJson(route('address.cities', ['q' => 'Ni']))
        ->assertOk()
        ->assertJsonStructure([
            '*' => ['name', 'placeId', 'postalCode', 'lat', 'lon'],
        ]);
});

test('streets endpoint returns streets', function () {
    $this->getJson(route('address.streets', ['q' => 'Šte', 'municipality' => 'Nitra']))
        ->assertOk()
        ->assertJsonStructure([
            '*' => ['name'],
        ]);
});

test('streets endpoint returns empty array without municipality', function () {
    $this->getJson(route('address.streets', ['q' => 'Šte']))
        ->assertOk()
        ->assertJsonCount(0);
});

test('addresses endpoint returns addresses', function () {
    $this->getJson(route('address.addresses', ['q' => '1', 'street' => 'Štefánikova', 'municipality' => 'Nitra']))
        ->assertOk()
        ->assertJsonStructure([
            '*' => ['streetNumber', 'postalCode', 'placeId'],
        ]);
});

test('addresses endpoint returns empty for missing parameters', function () {
    $this->getJson(route('address.addresses', ['q' => '1']))
        ->assertOk()
        ->assertJsonCount(0);
});

test('cities endpoint caches results', function () {
    $this->mock(AddressClientInterface::class, function (MockInterface $mock) {
        $mock->shouldReceive('searchCities')
            ->with('Br')
            ->once()
            ->andReturn([
                new CityResult(name: 'Bratislava', placeId: 'place_1', postalCode: '81101', lat: 48.14, lon: 17.10),
            ]);
    });

    $this->getJson(route('address.cities', ['q' => 'Br']))->assertOk();
    $this->getJson(route('address.cities', ['q' => 'Br']))->assertOk()
        ->assertJsonCount(1)
        ->assertJsonFragment(['name' => 'Bratislava']);
});

test('streets endpoint caches results', function () {
    $this->mock(AddressClientInterface::class, function (MockInterface $mock) {
        $mock->shouldReceive('searchStreets')
            ->with('Šte', 'Nitra')
            ->once()
            ->andReturn([
                new StreetResult(name: 'Štefánikova'),
            ]);
    });

    $this->getJson(route('address.streets', ['q' => 'Šte', 'municipality' => 'Nitra']))->assertOk();
    $this->getJson(route('address.streets', ['q' => 'Šte', 'municipality' => 'Nitra']))->assertOk()
        ->assertJsonCount(1)
        ->assertJsonFragment(['name' => 'Štefánikova']);
});

test('addresses endpoint caches results', function () {
    $this->mock(AddressClientInterface::class, function (MockInterface $mock) {
        $mock->shouldReceive('searchAddresses')
            ->with('1', 'Štefánikova', 'Nitra')
            ->once()
            ->andReturn([
                new AddressResult(streetNumber: '1/A', postalCode: '94901', placeId: 'place_1'),
            ]);
    });

    $this->getJson(route('address.addresses', ['q' => '1', 'street' => 'Štefánikova', 'municipality' => 'Nitra']))->assertOk();
    $this->getJson(route('address.addresses', ['q' => '1', 'street' => 'Štefánikova', 'municipality' => 'Nitra']))->assertOk()
        ->assertJsonCount(1)
        ->assertJsonFragment(['streetNumber' => '1/A']);
});
