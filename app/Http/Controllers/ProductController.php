<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexProductRequest $request): Response
    {
        $query = Product::query()
            ->when($request->validated('min_price'), fn ($q, $minPrice) => $q->where('price_with_vat', '>=', $minPrice))
            ->when($request->validated('max_price'), fn ($q, $maxPrice) => $q->where('price_with_vat', '<=', $maxPrice));

        $priceRange = Product::query()->selectRaw('
            MIN(price_with_vat) as min_price,
            MAX(price_with_vat) as max_price
        ')->first();

        return Inertia::render('Products/Index', [
            'products' => $query->cursorPaginate(5),
            'priceRange' => $priceRange,
            'filters' => $request->only(['min_price', 'max_price']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): void
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): void
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): Response
    {
        return Inertia::render('Products/Show', [
            'product' => Product::query()->findOrFail($id),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): void
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): void
    {
        //
    }
}
