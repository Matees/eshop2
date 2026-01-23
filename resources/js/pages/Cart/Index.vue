<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage()
</script>

<template>
    <div class="cart-page">
        <h1>Košík</h1>

        <div v-if="page.props.cart.items.length === 0" class="cart-empty">
            <p>Váš košík je prázdny</p>
            <Link href="/products" class="continue-shopping"> Pokračovať v nákupe </Link>
        </div>

        <div v-else>
            <div class="cart-items">
                <div v-for="item in page.props.cart.items" :key="item.cartId" class="cart-item">
                    <div class="cart-item-info">
                        <p class="cart-item-name">{{ item.name }}</p>

                        <p class="subtotal-price">
                            <span>Cena bez DPH: </span>
                            <strong>{{ item.unitPrice }} €</strong>
                        </p>
                        <p class="total-price">
                            <span>Cena s DPH: </span>
                            <strong>{{ item.unitPrice * (1 + item.taxRate / 100) }} €</strong>
                        </p>
                    </div>
                    <div class="cart-item-quantity">Množstvo: {{ item.quantity }}</div>
                    <div class="cart-item-total">
                        <p class="subtotal-price">
                            <span>Cena bez DPH: </span>
                            <strong>{{ item.unitPrice * item.quantity }} €</strong>
                        </p>
                        <p class="total-price">
                            <span>Cena s DPH: </span>
                            <strong>{{ (item.unitPrice * (1 + item.taxRate / 100)) * item.quantity }} €</strong>
                        </p>
                    </div>
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
                    <Link href="/products" class="continue-shopping"> Pokračovať v nákupe </Link>
                    <button class="checkout-btn">Objednať</button>
                </div>
            </div>
        </div>
    </div>
</template>
