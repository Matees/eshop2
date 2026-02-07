<?php

declare(strict_types=1);

namespace App\Address\Clients;

use App\Address\Contracts\AddressClientInterface;
use App\Address\DTO\AddressResult;
use App\Address\DTO\CityResult;
use App\Address\DTO\StreetResult;
use App\Address\HttpClientFactory;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;

final class SwiftyperAddressClient implements AddressClientInterface
{
    private const string API_URL = 'https://api.swiftyper.sk/v1/places/query';

    private const string DEFAULT_COUNTRY = 'SK';

    private ?Client $httpClient = null;

    public function __construct(
        private readonly string $apiKey,
    ) {}

    private function getHttpClient(): Client
    {
        if ($this->httpClient === null) {
            $this->httpClient = HttpClientFactory::create();
        }

        return $this->httpClient;
    }

    /**
     * @return array<CityResult>
     */
    public function searchCities(string $query): array
    {
        $data = $this->fetchData([
            'query' => $query,
            'country' => [self::DEFAULT_COUNTRY],
        ]);

        return collect($data)
            ->filter(fn (array $item): bool => isset($item['municipality']))
            ->map(fn (array $item): CityResult => CityResult::fromApiResponse($item))
            ->unique('name')
            ->values()
            ->all();
    }

    /**
     * @return array<StreetResult>
     */
    public function searchStreets(string $query, string $municipality): array
    {
        $data = $this->fetchData([
            'query' => $query,
            'municipality' => $municipality,
            'country' => [self::DEFAULT_COUNTRY],
        ]);

        return collect($data)
            ->filter(fn (array $item): bool => isset($item['street']))
            ->map(fn (array $item): StreetResult => StreetResult::fromApiResponse($item))
            ->unique('name')
            ->values()
            ->all();
    }

    /**
     * @return array<AddressResult>
     */
    public function searchAddresses(string $query, string $street, string $municipality): array
    {
        $searchQuery = $query !== '' ? "$street $query" : $street;

        $data = $this->fetchData([
            'query' => $searchQuery,
            'municipality' => $municipality,
            'country' => [self::DEFAULT_COUNTRY],
            'limit' => 15,
        ]);

        return collect($data)
            ->filter(fn (array $item): bool => AddressResult::hasValidStreetNumber($item))
            ->map(fn (array $item): AddressResult => AddressResult::fromApiResponse($item))
            ->unique('streetNumber')
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<int, array<string, mixed>>
     */
    private function fetchData(array $payload): array
    {
        try {
            $response = $this->getHttpClient()->post(self::API_URL, [
                'headers' => [
                    'X-Swiftyper-API-Key' => $this->apiKey,
                ],
                'json' => $payload,
            ]);

            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                return json_decode((string) $response->getBody(), true, flags: JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
            }
        } catch (GuzzleException $e) {
            // TODO doplnit logovanie popripade nejaku logiku
            return [];
        } catch (JsonException $e) {
            // TODO doplnit logovanie popripade nejaku logiku
            return [];
        }

        return [];
    }
}
