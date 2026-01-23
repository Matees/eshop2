<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { useForm } from 'laravel-precognition-vue';
import { inject } from 'vue';

import { add } from "@/actions/App/Http/Controllers/CartController";
import type { Product } from "@/types/product";

const props = defineProps<{product: Product}>()

const showDialog = inject<(message: string) => void>('showDialog')

const form = useForm(add(props.product.id).method, add(props.product.id).url, {
    quantity: 1,
})

const addToCart = () => {
    router.post(add(props.product.id).url, {
        quantity: form.quantity
    }, {
        preserveScroll: true,
        onSuccess: () => showDialog?.('Pridané do košíka!'),
        onError: () => showDialog?.('Chyba pri pridávaní do košíka')
    })
}
</script>

<template>
    <div class="product-detail">
        <img :src="product.image" :alt="product.name" class="product-image" />
        <h1>{{ product.name }}</h1>
        <p class="product-description">{{ product.description }}</p>
        <p class="product-price">{{ product.price }} €</p>

        <div class="product-actions">
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

        <span v-if="form.invalid('quantity')" class="error">
            {{ form.errors.quantity }}
        </span>
    </div>
</template>
