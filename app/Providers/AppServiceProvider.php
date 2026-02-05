<?php

namespace App\Providers;

use App\Address\Clients\SwiftyperAddressClient;
use App\Address\Contracts\AddressClientInterface;
use App\Cart\CartRiesenieService;
use App\Cart\Contracts\CartInterface;
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

        $this->app->singleton(AddressClientInterface::class, function () {
            /** @var string $apiKey */
            $apiKey = config('services.swiftyper.api_key');

            return new SwiftyperAddressClient(
                apiKey: $apiKey,
            );
        });
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
