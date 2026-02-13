<?php

declare(strict_types=1);

namespace App\Address\Clients;

use App\Address\Contracts\AddressClientInterface;
use App\Address\DTO\AddressResult;
use App\Address\DTO\CityResult;
use App\Address\DTO\StreetResult;
use GuzzleHttp\Psr7\Request;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;

final class SwiftyperAddressClient implements AddressClientInterface
{
    private const string DEFAULT_COUNTRY = 'SK';

    public function __construct(
        private readonly string $apiKey,
        private readonly SwiftyperResponseMapper $mapper,
        private readonly ClientInterface $httpClient,
        private readonly string $baseUrl,
    ) {}

    /**
     * @return array<CityResult>
     */
    public function searchCities(string $query): array
    {
        $data = $this->fetchData([
            'query' => $query,
            'country' => [self::DEFAULT_COUNTRY],
        ]);

        return $this->mapper->mapCities($data);
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

        return $this->mapper->mapStreets($data);
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
        ]);

        return $this->mapper->mapAddresses($data);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<int, array<string, mixed>>
     */
    private function fetchData(array $payload): array
    {
        try {
            $request = new Request('POST', $this->baseUrl, [
                'Content-Type' => 'application/json',
                'X-Swiftyper-API-Key' => $this->apiKey,
            ], json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));

            $response = $this->httpClient->sendRequest($request);

            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                return json_decode((string) $response->getBody(), true, flags: JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
            }
        } catch (ClientExceptionInterface $e) {
            // TODO doplnit logovanie popripade nejaku logiku
            return [];
        } catch (JsonException $e) {
            // TODO doplnit logovanie popripade nejaku logiku
            return [];
        }

        return [];
    }
}
