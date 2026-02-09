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
                    //                    Log::debug('logujem pred requestom');

                    $start = microtime(true);

                    $promise = $handler($request, $options);

                    return $promise->then(
                        static function (ResponseInterface $response) use ($start) {
                            //                            Log::debug('logujem po requeste');
                            Log::debug('cas requestu '.(microtime(true) - $start) * 1000 .' ms');

                            return $response;
                        }
                    );
                };
            }
        );

        return new Client(['handler' => $handlerStack]);
    }
}
