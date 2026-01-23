<script setup lang="ts">
import { onMounted } from 'vue';

defineProps<{
    message: string
    type?: 'success' | 'error' | 'warning'
}>()

const emit = defineEmits<{
    close: []
}>()

let timeout: ReturnType<typeof setTimeout>
let closed = false

const typeClasses = {
    success: 'bg-green-500',
    error: 'bg-red-500',
    warning: 'bg-yellow-500',
}

const icons = {
    success: '✓',
    error: '✕',
    warning: '!',
}

const close = () => {
    if (closed) return
    closed = true
    clearTimeout(timeout)
    emit('close')
}

onMounted(() => {
    timeout = setTimeout(close, 3000)
})
</script>

<template>
    <div
        class="fixed top-4 left-1/2 -translate-x-1/2 z-[100] flex items-center gap-3 min-w-80 px-5 py-4 rounded-lg shadow-lg text-white cursor-pointer hover:opacity-90 transition-opacity"
        :class="typeClasses[type ?? 'success']"
        @click="close"
    >
        <span class="flex items-center justify-center w-7 h-7 rounded-full bg-white/20 text-sm font-bold">
            {{ icons[type ?? 'success'] }}
        </span>
        <span class="flex-1 font-medium">{{ message }}</span>
        <span class="ml-2 text-white/70 hover:text-white text-xl">&times;</span>
    </div>
</template>
