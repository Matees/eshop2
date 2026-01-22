<script setup lang="ts">
import { Link, usePage, router } from '@inertiajs/vue3';
import { useForm } from 'laravel-precognition-vue';
import { ref } from 'vue';

import { addItem } from "@/actions/App/Http/Controllers/CartController";
import { show } from "@/actions/App/Http/Controllers/ProductController";
import type { Product, CursorPaginated } from "@/types/product";

const page = usePage()
const props = defineProps<{products: CursorPaginated<Product>}>()

const dialog = ref<HTMLDialogElement>()
const dialogMessage = ref('')

const showDialog = (message: string) => {
    dialogMessage.value = message
    dialog.value?.showModal()
}

// Form pre každý produkt
type CartForm = ReturnType<typeof useForm<{ quantity: number }>>
const forms: Record<number, CartForm> = {}
props.products.data.forEach(p => {
    forms[p.id] = useForm(addItem(p.id).method, addItem(p.id).url, {
        quantity: 1,
    })
})

const addToCart = (productId: number) => {
    router.post(addItem(productId).url, {
        quantity: forms[productId].quantity
    }, {
        preserveScroll: true,
        onSuccess: () => showDialog('Pridané do košíka!'),
        onError: () => showDialog('Chyba pri pridávaní do košíka')
    })
}
</script>

<template>
    <div v-for="product in products.data" :key="product.id">
        <img :src="product.image" />
        <Link :href="show(product.id)">
            {{ product.name }}
        </Link>
        <p>{{ product.description }}</p>

        <input
            type="number"
            v-model.number="forms[product.id].quantity"
            min="1"
            @change="forms[product.id].validate('quantity')"
        />
        <button @click="addToCart(product.id)" :disabled="forms[product.id].processing">
            Pridať do košíka
        </button>
        <span v-if="forms[product.id].invalid('quantity')">
            {{ forms[product.id].errors.quantity }}
        </span>
    </div>

    <div>
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

    <!-- Počet položiek -->
    <span>Košík: {{ page.props.cart.itemsCount }}</span>

    <!-- Zoznam položiek -->
    <div v-for="item in page.props.cart.items" :key="item.cartId">
        <p>{{ item.name }}</p>
        <p>Množstvo: {{ item.quantity }}</p>
        <p>Cena: {{ item.unitPrice }} €</p>
        --------------------------------------
    </div>

    <!-- Celková suma -->
    <p>Celkom: {{ page.props.cart.total }} €</p>

    <!-- Dialog -->
    <dialog ref="dialog">
        <p>{{ dialogMessage }}</p>
        <button @click="dialog?.close()">OK</button>
    </dialog>
</template>
