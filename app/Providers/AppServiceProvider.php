<?php

namespace App\Providers;

use App\Address\Clients\SwiftyperAddressClient;
use App\Address\Contracts\AddressClientInterface;
use App\Address\HttpClientFactory;
use App\Cart\CartRiesenieService;
use App\Cart\Contracts\CartInterface;
use GuzzleHttp\ClientInterface;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->scoped(CartInterface::class, CartRiesenieService::class);

        $this->app->singleton(AddressClientInterface::class, SwiftyperAddressClient::class);

        $this->app->when(SwiftyperAddressClient::class)
            ->needs('$apiKey')
            ->giveConfig('services.swiftyper.api_key');

        $this->app->when(SwiftyperAddressClient::class)
            ->needs(ClientInterface::class)
            ->give(fn () => HttpClientFactory::create());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
    }

    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null
        );
    }
}
