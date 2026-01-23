<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { ref, provide } from 'vue';

import CartIcon from '@/components/CartIcon.vue';
import { home } from '@/routes'

const page = usePage()

const dialog = ref<HTMLDialogElement>()
const dialogMessage = ref('')

const showDialog = (message: string) => {
    dialogMessage.value = message
    dialog.value?.showModal()
}

provide('showDialog', showDialog)
</script>

<template>
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

        <dialog ref="dialog">
            <p>{{ dialogMessage }}</p>
            <button @click="dialog?.close()">OK</button>
        </dialog>
    </div>
</template>
