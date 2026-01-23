<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { ref, provide } from 'vue';

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
    <div class="app-layout">
        <header class="header">
            <nav class="nav">
                <Link href="/products" class="nav-brand">
                    {{ page.props.name }}
                </Link>

                <Link href="/cart" class="cart-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="21" r="1"/>
                        <circle cx="20" cy="21" r="1"/>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                    </svg>
                    <span v-if="page.props.cart.itemCount > 0" class="cart-badge">
                        {{ page.props.cart.itemCount }}
                    </span>
                </Link>
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
