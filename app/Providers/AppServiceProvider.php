<?php

namespace App\Providers;

use App\Address\Clients\SwiftyperAddressClient;
use App\Address\Contracts\AddressClientInterface;
use App\Address\HttpClientFactory;
use App\Cart\Contracts\CartInterface;
use App\Cart\Riesenia\CartRiesenieService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Psr\Http\Client\ClientInterface;

class AppServiceProvider extends ServiceProvider
{
    /** @var class-string[] */
    public array $singletons = [
        CartInterface::class => CartRiesenieService::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ClientInterface::class, fn () => HttpClientFactory::create());
        $this->app->singleton(AddressClientInterface::class, SwiftyperAddressClient::class);

        $this->app->when(SwiftyperAddressClient::class)
            ->needs('$apiKey')
            ->giveConfig('services.swiftyper.api_key')
            ->needs('$baseUrl')
            ->giveConfig('services.swiftyper.base_url');
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
