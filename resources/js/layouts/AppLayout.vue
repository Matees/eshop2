<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

import CartIcon from '@/components/CartIcon.vue';
import Toast from '@/components/Toast.vue';
import { home } from '@/routes'

const page = usePage()

const showFlash = ref(true)

const flash = computed(() => {
    if (page.props.flash?.error) {
        return { message: page.props.flash.error, type: 'error' as const }
    }
    if (page.props.flash?.success) {
        return { message: page.props.flash.success, type: 'success' as const }
    }
    if (page.props.flash?.warning) {
        return { message: page.props.flash.warning, type: 'warning' as const }
    }
    return null
})

watch(flash, () => {
    showFlash.value = true
})
</script>

<template>
    <Toast
        v-if="flash && showFlash"
        :message="flash.message"
        :type="flash.type"
        @close="showFlash = false"
    />

    <div class="app-layout min-h-screen">
        <header class="sticky top-0 z-50 bg-amber-200gi shadow-sm">
            <nav class="flex items-center justify-between px-6 py-4">
                <Link :href="home()" class="text-xl font-bold text-gray-800 hover:text-gray-600">
                    {{ page.props.name }}
                </Link>

                <CartIcon class="mr-2 mt-1" />
            </nav>
        </header>

        <main class="main-content">
            <slot />
        </main>
    </div>
</template>
