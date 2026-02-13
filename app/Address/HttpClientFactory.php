<?php

declare(strict_types=1);

namespace App\Address;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Facades\Log;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class HttpClientFactory
{
    public static function create(): ClientInterface
    {
        $handlerStack = HandlerStack::create();

        $handlerStack->push(
            static function (callable $handler) {
                return static function (RequestInterface $request, array $options) use ($handler) {
                    $start = microtime(true);

                    $promise = $handler($request, $options);

                    return $promise->then(
                        static function (ResponseInterface $response) use ($start, $request) {
                            $durationMs = round((microtime(true) - $start) * 1000, 2);

                            Log::debug('HTTP request', [
                                'method' => $request->getMethod(),
                                'url' => (string) $request->getUri(),
                                'request_body' => (string) $request->getBody(),
                                'status' => $response->getStatusCode(),
                                'response_body' => (string) $response->getBody(),
                                'duration_ms' => $durationMs,
                            ]);

                            $response->getBody()->rewind();

                            return $response;
                        }
                    );
                };
            }
        );

        return new Client(['handler' => $handlerStack]);
    }
}
