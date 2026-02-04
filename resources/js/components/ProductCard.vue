<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { useForm } from 'laravel-precognition-vue';

import { add } from "@/actions/App/Http/Controllers/CartController";
import { show } from "@/actions/App/Http/Controllers/ProductController";
import CartIcon from '@/components/CartIcon.vue';
import type { Product } from "@/types/product";

const props = withDefaults(defineProps<{
    product: Product
    withInput?: boolean
}>(), {
    withInput: true
})

const form = useForm(add(props.product.id).method, add(props.product.id).url, {
    quantity: 1,
})

const addToCart = () => {
    router.post(add(props.product.id).url, {
        quantity: form.quantity
    }, {
        preserveScroll: true,
    })
}
</script>

<template>
    <div class="product-card">
        <img :src="product.image" :alt="product.name" class="product-image" />

        <Link :href="show(product.id)" class="product-name">
            {{ product.name }}
        </Link>

        <p class="product-description">{{ product.description }}</p>
        <p class="subtotal-price">
            <span>Cena bez DPH: </span>
            <strong>{{ product.price }} €</strong>
        </p>
        <p class="total-price">
            <span>Cena s DPH: </span>
            <strong>{{ product.price_with_vat }} €</strong>
        </p>

        <cart-icon :product-id="product.id"></cart-icon>

        <div v-if="withInput" class="product-actions">
            <input
                type="number"
                v-model.number="form.quantity"
                min="1"
                class="quantity-input"
                @change="form.validate('quantity')"
            />
            <button
                @click="addToCart"
                :disabled="form.processing"
                class="add-to-cart-btn"
            >
                Pridať do košíka
            </button>
        </div>

        <span v-if="withInput && form.invalid('quantity')" class="error">
            {{ form.errors.quantity }}
        </span>
    </div>
</template>
