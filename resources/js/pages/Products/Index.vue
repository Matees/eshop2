<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';

import { addItem } from "@/actions/App/Http/Controllers/CartController";
import { show } from "@/actions/App/Http/Controllers/ProductController";
import type { Product } from "@/types/product";

const page = usePage()
defineProps<{products: Product[]}>()
</script>

<template>
    <div v-for="product in products" :key="product.id">
        <Link :href="show(product.id)">
            {{ product.name }}
        </Link>
        <p>{{ product.description }}</p>

        <Link :href="addItem(product.id)">
            Pridat do kosika
        </Link>
    </div>

<!--    <pre>{{ JSON.stringify(page.props.cart, null, 2) }}</pre>-->


    <!-- Počet položiek -->
    <span>Košík: {{ page.props.cart.itemsCount }}</span>

    <!-- Zoznam položiek -->
    <div v-for="item in page.props.cart.items" :key="item.cartId">
        <p>{{ item.name }}</p>
        <p>Množstvo: {{ item.quantity }}</p>
        <p>Cena: {{ item.unitPrice }} €</p>
    </div>

    <!-- Celková suma -->
    <p>Celkom: {{ page.props.cart.total }} €</p>
</template>
