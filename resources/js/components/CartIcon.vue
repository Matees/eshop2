<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

import { index } from '@/actions/App/Http/Controllers/CartController'

const props = defineProps<{
    productId?: number
}>()

const page = usePage()

const quantity = computed(() => {
    if (!props.productId) {
        return page.props.cart.itemCount
    }
    const item = page.props.cart.items[props.productId]
    return item?.quantity ?? 0
})
</script>

<template>
    <Link v-if="quantity > 0" :href="index()" class="relative inline-flex items-center p-2 text-gray-700 hover:text-gray-900 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="9" cy="21" r="1"/>
            <circle cx="20" cy="21" r="1"/>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
        </svg>
        <span class="absolute -top-1 -right-1 flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full">
            {{ quantity }}
        </span>
    </Link>
</template>
