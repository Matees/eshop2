<script setup lang="ts">
import { Link } from '@inertiajs/vue3'

import { home } from '@/routes'

interface OrderProduct {
    id: number
    name: string
    pivot: {
        unit_price: number
        quantity: number
        tax_rate: number
        total: number
    }
}

interface Order {
    id: number
    status: string
    email: string
    phone: string | null
    address_line_one: string
    address_line_two: string
    address_line_three: string
    total: number
    subtotal: number
    created_at: string
    products: OrderProduct[]
}

const props = defineProps<{
    order: Order
}>()

const statusLabels: Record<string, string> = {
    pending: 'Caka na spracovanie',
    processing: 'Spracovava sa',
    shipped: 'Odoslana',
    delivered: 'Dorucena',
    cancelled: 'Zrusena',
}

const statusLabel = statusLabels[props.order.status] ?? props.order.status

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('sk-SK', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    })
}
</script>

<template>
    <div class="order-show">
        <h1>Objednavka #{{ order.id }}</h1>

        <div class="order-status">
            <span class="status-badge" :class="'status-' + order.status">
                {{ statusLabel }}
            </span>
            <span class="order-date">{{ formatDate(order.created_at) }}</span>
        </div>

        <div class="order-section">
            <h2>Produkty</h2>
            <div class="order-items">
                <div v-for="product in order.products" :key="product.id" class="order-item">
                    <div class="order-item-info">
                        <p class="order-item-name">{{ product.name }}</p>
                        <p class="order-item-quantity">Mnozstvo: {{ product.pivot.quantity }}</p>
                    </div>
                    <div class="order-item-price">
                        <p>{{ product.pivot.unit_price }} &euro;</p>
                        <p class="order-item-total"><strong>{{ product.pivot.total }} &euro;</strong></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="order-section">
            <h2>Sumar</h2>
            <div class="order-summary">
                <p class="subtotal-price">
                    <span>Cena bez DPH:</span>
                    <strong>{{ order.subtotal }} &euro;</strong>
                </p>
                <p class="total-price">
                    <span>Cena s DPH:</span>
                    <strong>{{ order.total }} &euro;</strong>
                </p>
            </div>
        </div>

        <div class="order-section">
            <h2>Dorucovacia adresa</h2>
            <p>{{ order.address_line_one }}</p>
            <p>{{ order.address_line_two }}</p>
            <p>{{ order.address_line_three }}</p>
        </div>

        <div class="order-section">
            <h2>Kontakt</h2>
            <p>{{ order.email }}</p>
            <p v-if="order.phone">{{ order.phone }}</p>
        </div>

        <Link :href="home()" class="back-link">Spat na hlavnu stranku</Link>
    </div>
</template>

<style scoped>
.order-show {
    max-width: 600px;
    margin: 0 auto;
    padding: 2rem;
}

.order-status {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2rem;
}

.status-badge {
    padding: 0.4rem 0.8rem;
    border-radius: 4px;
    font-weight: 600;
    font-size: 0.875rem;
    color: white;
    background-color: #6c757d;
}

.status-pending {
    background-color: #ffc107;
    color: #333;
}

.status-processing {
    background-color: #007bff;
}

.status-shipped {
    background-color: #17a2b8;
}

.status-delivered {
    background-color: #28a745;
}

.status-cancelled {
    background-color: #dc3545;
}

.order-date {
    color: #666;
    font-size: 0.875rem;
}

.order-section {
    margin-bottom: 1.5rem;
}

.order-section h2 {
    font-size: 1.25rem;
    margin-bottom: 0.5rem;
    border-bottom: 1px solid #eee;
    padding-bottom: 0.5rem;
}

.order-items {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.order-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem;
    border: 1px solid #eee;
    border-radius: 4px;
}

.order-item-name {
    font-weight: 500;
}

.order-item-quantity {
    font-size: 0.875rem;
    color: #666;
}

.order-item-price {
    text-align: right;
}

.order-item-total {
    font-size: 1.1rem;
}

.order-summary {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.subtotal-price {
    display: flex;
    justify-content: space-between;
    color: #666;
}

.total-price {
    display: flex;
    justify-content: space-between;
    font-size: 1.1rem;
    padding-top: 0.5rem;
    border-top: 1px solid #eee;
}

.back-link {
    display: inline-block;
    margin-top: 1rem;
    color: #007bff;
    text-decoration: none;
}

.back-link:hover {
    text-decoration: underline;
}
</style>
