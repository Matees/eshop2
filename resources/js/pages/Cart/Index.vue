<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';

import { remove } from '@/actions/App/Http/Controllers/CartController';
import { home } from '@/routes';
import { log } from 'eslint-import-resolver-typescript/lib/logger';

const page = usePage();
</script>

<template>
    <div class="cart-page">
        <h1>Košík</h1>

        <div v-if="page.props.cart.items.length === 0" class="cart-empty">
            <p>Váš košík je prázdny</p>
            <Link :href="home()" class="continue-shopping"> Pokračovať v nákupe </Link>
        </div>

        <div v-else>
            <div class="cart-items">
                <div v-for="item in page.props.cart.items" :key="item.cartId" class="cart-item">
                    <div class="cart-item-info">
                        <p class="cart-item-name">{{ item.name }}</p>
                    </div>
                    <div class="cart-item-quantity">Množstvo: {{ item.quantity }}</div>
                    <div class="cart-item-total">
                        <p class="subtotal-price">
                            <span>Cena bez DPH: </span>
                            <strong>{{ item.subTotal }} €</strong>
                        </p>
                        <p class="total-price">
                            <span>Cena s DPH: </span>
                            <strong>{{ item.total }} €</strong>
                        </p>
                    </div>
                    <Link :href="remove(111)"> Odstranit </Link>
                </div>
            </div>

            <div class="cart-summary">
                <div class="cart-total">
                    <p class="subtotal-price">
                        <span>Cena bez DPH: </span>
                        <strong>{{ page.props.cart.subTotal }} €</strong>
                    </p>
                    <p class="total-price">
                        <span>Cena s DPH: </span>
                        <strong>{{ page.props.cart.total }} €</strong>
                    </p>
                </div>

                <div class="cart-actions">
                    <Link :href="home()" class="continue-shopping"> Pokračovať v nákupe </Link>
                    <button class="checkout-btn">Objednať</button>
                </div>
            </div>
        </div>
    </div>
</template>
