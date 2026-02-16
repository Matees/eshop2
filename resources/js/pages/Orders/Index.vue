<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { index as productsIndex } from '@/routes/products'

interface Order {
    id: number
    status: string
    email: string
    total: number
    created_at: string
}

defineProps<{
    orders: Order[]
}>()

const statusLabels: Record<string, string> = {
    pending: 'Caka na spracovanie',
    processing: 'Spracovava sa',
    shipped: 'Odoslana',
    delivered: 'Dorucena',
    cancelled: 'Zrusena',
}

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('sk-SK', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    })
}
</script>

<template>
    <div class="orders-page">
        <h1>Moje objednavky</h1>

        <div v-if="orders.length === 0" class="orders-empty">
            <p>Zatial nemáte žiadne objednavky</p>
            <Link :href="productsIndex()" class="back-link">Pokracovat v nakupe</Link>
        </div>

        <div v-else class="orders-list">
            <div v-for="order in orders" :key="order.id" class="order-card">
                <div class="order-header">
                    <span class="order-id">#{{ order.id }}</span>
                    <span class="order-date">{{ formatDate(order.created_at) }}</span>
                </div>
                <div class="order-body">
                    <span class="status-badge" :class="'status-' + order.status">
                        {{ statusLabels[order.status] ?? order.status }}
                    </span>
                    <span class="order-total">{{ order.total }} &euro;</span>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.orders-page {
    max-width: 600px;
    margin: 0 auto;
    padding: 2rem;
}

.orders-empty {
    text-align: center;
    padding: 2rem;
    color: #666;
}

.orders-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.order-card {
    border: 1px solid #eee;
    border-radius: 4px;
    padding: 1rem;
}

.order-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
}

.order-id {
    font-weight: 600;
}

.order-date {
    color: #666;
    font-size: 0.875rem;
}

.order-body {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.status-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-weight: 600;
    font-size: 0.8rem;
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

.order-total {
    font-weight: 600;
    font-size: 1.1rem;
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
