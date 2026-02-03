<?php

namespace App\Http\Middleware;

use App\Cart\Contracts\CartInterface;
use App\Cart\DTO\CartData;
use App\Enums\FlashType;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'cart' => fn () => CartData::fromCart(app(CartInterface::class)),
            'flash' => [
                FlashType::Success->value => fn () => $request->session()->get(FlashType::Success->value),
                FlashType::Error->value => fn () => $request->session()->get(FlashType::Error->value),
                FlashType::Warning->value => fn () => $request->session()->get(FlashType::Warning->value),
            ],
            'name' => config('app.name'),
        ];
    }
}
