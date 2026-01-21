<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';

import { addItem } from "@/actions/App/Http/Controllers/CartController";
import { show } from "@/actions/App/Http/Controllers/ProductController";
import type { Product } from "@/types/product";

const page = usePage()
const cart = page.props.cart
console.log(cart);
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

    <pre>{{ JSON.stringify(cart, null, 2) }}</pre>


<!--    &lt;!&ndash; Počet položiek &ndash;&gt;-->
<!--    <span>Košík: {{ cart.items.length }}</span>-->

<!--    &lt;!&ndash; Zoznam položiek &ndash;&gt;-->
<!--    <div v-for="item in cart.items" :key="item.cartId">-->
<!--        <p>{{ item.cartName }}</p>-->
<!--        <p>Množstvo: {{ item.cartQuantity }}</p>-->
<!--        <p>Cena: {{ item.unitPrice }} €</p>-->
<!--    </div>-->

<!--    &lt;!&ndash; Celková suma &ndash;&gt;-->
<!--    <p>Celkom: {{ cart.total }} €</p>-->
</template>
