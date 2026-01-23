<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';

const page = usePage()
</script>

<template>
    <div class="max-h-80 overflow-y-auto">
        <div v-if="page.props.cart.items.length === 0" class="text-gray-500 text-center py-4">
            Košík je prázdny
        </div>

        <div v-else>
            <div
                v-for="item in page.props.cart.items"
                :key="item.cartId"
                class="flex justify-between items-center py-2 border-b border-gray-100 last:border-0"
            >
                <div class="flex-1">
                    <p class="font-medium text-gray-800 text-sm">{{ item.name }}</p>
                    <p class="text-gray-500 text-xs">{{ item.quantity }}x {{ item.unitPrice }} €</p>
                </div>
                <span class="font-semibold text-gray-800">
                    {{ (item.quantity * item.unitPrice).toFixed(2) }} €
                </span>
            </div>

            <div class="mt-4 pt-3 border-t border-gray-200 flex justify-between items-center">
                <span class="font-semibold text-gray-800">Celkom:</span>
                <span class="text-lg font-bold text-blue-600">{{ page.props.cart.total }} €</span>
            </div>
        </div>
    </div>
</template>
