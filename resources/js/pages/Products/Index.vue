<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

import PriceSidebar from '@/components/PriceSidebar.vue';
import ProductCard from '@/components/ProductCard.vue';
import type { Product, CursorPaginated, PriceRange, PriceFilters } from "@/types/product";

defineProps<{
    products: CursorPaginated<Product>;
    priceRange: PriceRange;
    filters: PriceFilters;
}>()
</script>

<template>
    <div>
        <PriceSidebar
            :price-range="priceRange"
            :current-filters="filters"
        />

        <div class="products-list">
            <ProductCard
                v-for="product in products.data"
                :key="product.id"
                :product="product"
                :with-input="false"
            />
        </div>

        <div class="pagination">
            <Link
                v-if="products.prev_page_url"
                :href="products.prev_page_url"
                preserve-scroll
            >
                ← Predchádzajúca
            </Link>
            <Link
                v-if="products.next_page_url"
                :href="products.next_page_url"
                preserve-scroll
            >
                Ďalšia →
            </Link>
        </div>
    </div>
</template>
