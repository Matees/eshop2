<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'

const form = useForm({
    email: '',
    password: '',
})

const submit = () => {
    form.post('/login', {
        onFinish: () => {
            form.reset()
        },
    })
}
</script>

<template>
    <div class="login-page">
        <h1>Prihlasenie</h1>

        <form @submit.prevent="submit" class="login-form">
            <span v-if="form.hasErrors" class="error">Zle prihlasovacie udaje</span>

            <div class="form-group">
                <label for="email">Email</label>
                <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    requiredpage
                    placeholder="vas@email.sk"
                />
            </div>

            <div class="form-group">
                <label for="password">Heslo</label>
                <input
                    id="password"
                    v-model="form.password"
                    type="password"
                    required
                />
                <span v-if="form.errors.password" class="error">{{ form.errors.password }}</span>
            </div>

            <button type="submit" :disabled="form.processing" class="submit-btn">
                {{ form.processing ? 'Prihlasujem...' : 'Prihlasit sa' }}
            </button>
        </form>
    </div>
</template>

<style scoped>
.login-page {
    max-width: 400px;
    margin: 0 auto;
    padding: 2rem;
}

.login-form {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.form-group label {
    font-weight: 500;
    font-size: 0.875rem;
}

.form-group input {
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
}

.form-group input:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.1);
}

.error {
    color: #dc3545;
    font-size: 0.875rem;
}

.submit-btn {
    padding: 0.75rem 1.5rem;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    cursor: pointer;
    transition: background-color 0.2s;
}

.submit-btn:hover:not(:disabled) {
    background-color: #0056b3;
}

.submit-btn:disabled {
    background-color: #ccc;
    cursor: not-allowed;
}
</style>
