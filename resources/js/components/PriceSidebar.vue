<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';

import type { PriceRange, PriceFilters } from '@/types/product';

const props = defineProps<{
    priceRange: PriceRange;
    currentFilters: PriceFilters;
}>();

const isOpen = ref(false);
const minValue = ref(Number(props.currentFilters.min_price) || props.priceRange.min_price);
const maxValue = ref(Number(props.currentFilters.max_price) || props.priceRange.max_price);

watch(() => props.priceRange, (newRange) => {
    if (!props.currentFilters.min_price) {
        minValue.value = newRange.min_price;
    }
    if (!props.currentFilters.max_price) {
        maxValue.value = newRange.max_price;
    }
});

watch(minValue, (newVal) => {
    if (newVal > maxValue.value) {
        minValue.value = maxValue.value;
    }
});

watch(maxValue, (newVal) => {
    if (newVal < minValue.value) {
        maxValue.value = minValue.value;
    }
});

const minPercent = computed(() => {
    const range = props.priceRange.max_price - props.priceRange.min_price;
    return ((minValue.value - props.priceRange.min_price) / range) * 100;
});

const maxPercent = computed(() => {
    const range = props.priceRange.max_price - props.priceRange.min_price;
    return ((maxValue.value - props.priceRange.min_price) / range) * 100;
});

function toggle() {
    isOpen.value = !isOpen.value;
}

function applyFilter() {
    router.get('/', {
        min_price: minValue.value,
        max_price: maxValue.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
    isOpen.value = false;
}

function resetFilter() {
    minValue.value = props.priceRange.min_price;
    maxValue.value = props.priceRange.max_price;
    router.get('/', {}, {
        preserveState: true,
        preserveScroll: true,
    });
    isOpen.value = false;
}
</script>

<template>
    <div>
        <!-- Toggle button -->
        <button
            @click="toggle"
            class="fixed left-0 top-1/2 -translate-y-1/2 z-40 bg-blue-600 text-white px-3 py-4 rounded-r-lg shadow-lg hover:bg-blue-700 transition-colors"
        >
            <span class="writing-mode-vertical">Filter</span>
        </button>

        <!-- Overlay -->
        <div
            v-if="isOpen"
            @click="toggle"
            class="fixed inset-0 bg-black/50 z-40"
        />

        <!-- Sidebar -->
        <div
            :class="[
                'fixed left-0 top-0 h-full w-80 bg-white shadow-xl z-50 p-6 transition-transform duration-300',
                isOpen ? 'translate-x-0' : '-translate-x-full'
            ]"
        >
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold">Filter ceny</h2>
                <button @click="toggle" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
            </div>

            <div class="space-y-6">
                <div>
                    <div class="flex justify-between text-sm font-medium text-gray-700 mb-4">
                        <span>{{ minValue }} €</span>
                        <span>{{ maxValue }} €</span>
                    </div>

                    <div class="dual-range">
                        <div class="range-track">
                            <div
                                class="range-selected"
                                :style="{ left: minPercent + '%', right: (100 - maxPercent) + '%' }"
                            />
                        </div>
                        <input
                            type="range"
                            v-model.number="minValue"
                            :min="priceRange.min_price"
                            :max="priceRange.max_price"
                            :step="0.01"
                            class="range-input range-min"
                        />
                        <input
                            type="range"
                            v-model.number="maxValue"
                            :min="priceRange.min_price"
                            :max="priceRange.max_price"
                            :step="0.01"
                            class="range-input range-max"
                        />
                    </div>

                    <div class="flex justify-between text-xs text-gray-500 mt-2">
                        <span>{{ priceRange.min_price }} €</span>
                        <span>{{ priceRange.max_price }} €</span>
                    </div>
                </div>

                <div class="pt-4 space-y-3">
                    <button
                        @click="applyFilter"
                        class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors"
                    >
                        Filtrovať
                    </button>
                    <button
                        @click="resetFilter"
                        class="w-full bg-gray-200 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-300 transition-colors"
                    >
                        Resetovať
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.writing-mode-vertical {
    writing-mode: vertical-rl;
    text-orientation: mixed;
}

.dual-range {
    position: relative;
    height: 8px;
}

.range-track {
    position: absolute;
    width: 100%;
    height: 8px;
    background: #e5e7eb;
    border-radius: 4px;
}

.range-selected {
    position: absolute;
    height: 100%;
    background: #2563eb;
    border-radius: 4px;
}

.range-input {
    position: absolute;
    width: 100%;
    height: 8px;
    background: none;
    pointer-events: none;
    -webkit-appearance: none;
    appearance: none;
}

.range-min {
    z-index: 2;
}

.range-max {
    z-index: 1;
}

.range-input::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 20px;
    height: 20px;
    background: #2563eb;
    border-radius: 50%;
    cursor: pointer;
    pointer-events: auto;
    border: 2px solid white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.range-input::-moz-range-thumb {
    width: 20px;
    height: 20px;
    background: #2563eb;
    border-radius: 50%;
    cursor: pointer;
    pointer-events: auto;
    border: 2px solid white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}
</style>
