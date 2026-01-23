<script setup lang="ts">

import ProductCard from '@/components/ProductCard.vue';
import type { Product } from "@/types/product";

defineProps<{product: Product}>()

</script>

<template>
    <div class="product-detail">
        <ProductCard :product="product" />
    </div>
</template>
