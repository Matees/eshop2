<script setup lang="ts">

import type { Product } from "@/types/product";

defineProps<{product: Product}>()
</script>

<template>
    <p>{{ product.name }}</p>
    <p>{{ product.description }}</p>
</template>
