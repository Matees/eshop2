<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

import { store } from '@/actions/App/Http/Controllers/OrderController'

interface City {
    name: string
    placeId: string
    postcode: string
    display_name: string
}

interface Street {
    name: string
    postcode: string
}

interface Address {
    streetNumber: string
    postcode: string
}

const form = useForm({
    email: '',
    phone: '',
    city: '',
    street: '',
    houseNumber: '',
    zip: '',
}).withPrecognition(store())

const cityQuery = ref('')
const streetQuery = ref('')
const houseNumberQuery = ref('')
const cities = ref<City[]>([])
const streets = ref<Street[]>([])
const addresses = ref<Address[]>([])
const showCityDropdown = ref(false)
const showStreetDropdown = ref(false)
const showAddressDropdown = ref(false)
const loadingCities = ref(false)
const loadingStreets = ref(false)
const loadingAddresses = ref(false)

let cityDebounce: ReturnType<typeof setTimeout> | null = null
let streetDebounce: ReturnType<typeof setTimeout> | null = null
let addressDebounce: ReturnType<typeof setTimeout> | null = null

const searchCities = async (query: string) => {
    loadingCities.value = true

    try {
        const response = await fetch(`/api/address/cities?q=${encodeURIComponent(query)}`)
        cities.value = await response.json()
        showCityDropdown.value = cities.value.length > 0
    } catch (error) {
        console.error('Error fetching cities:', error)
        cities.value = []
    } finally {
        loadingCities.value = false
    }
}

const searchStreets = async (query: string) => {
    if (!form.city) {
        streets.value = []
        return
    }

    if (!query) {
        return
    }

    loadingStreets.value = true

    try {
        const response = await fetch(
            `/api/address/streets?q=${encodeURIComponent(query)}&municipality=${encodeURIComponent(form.city)}`
        )
        streets.value = await response.json()
        showStreetDropdown.value = streets.value.length > 0
    } catch (error) {
        console.error('Error fetching streets:', error)
        streets.value = []
    } finally {
        loadingStreets.value = false
    }
}

const searchAddresses = async (query: string) => {
    if (!form.city || !form.street) {
        addresses.value = []
        return
    }

    loadingAddresses.value = true

    try {
        const response = await fetch(
            `/api/address/addresses?q=${encodeURIComponent(query)}&street=${encodeURIComponent(form.street)}&municipality=${encodeURIComponent(form.city)}`
        )
        addresses.value = await response.json()
        showAddressDropdown.value = addresses.value.length > 0
    } catch (error) {
        console.error('Error fetching addresses:', error)
        addresses.value = []
    } finally {
        loadingAddresses.value = false
    }
}

const selectCity = (city: City) => {
    form.city = city.name
    cityQuery.value = city.name
    showCityDropdown.value = false

    form.zip = city.postcode

    form.street = ''
    streetQuery.value = ''
    streets.value = []

    form.houseNumber = ''
    houseNumberQuery.value = ''
    addresses.value = []
}

const selectStreet = (street: Street) => {
    form.street = street.name
    streetQuery.value = street.name
    showStreetDropdown.value = false

    form.houseNumber = ''
    houseNumberQuery.value = ''
    addresses.value = []

        form.zip = street.postcode
}

const selectAddress = (address: Address) => {
    form.houseNumber = address.streetNumber
    houseNumberQuery.value = address.streetNumber
    showAddressDropdown.value = false
}

watch(cityQuery, (newValue) => {
    if (cityDebounce) clearTimeout(cityDebounce)

    if (newValue === form.city) {
        return
    }

    form.city = ''
    form.zip = ''

    cityDebounce = setTimeout(() => {
        searchCities(newValue)
    }, 300)
})

watch(streetQuery, (newValue) => {
    if (streetDebounce) clearTimeout(streetDebounce)

    if (newValue === form.street) {
        return
    }

    form.street = newValue

    streetDebounce = setTimeout(() => {
        searchStreets(newValue)
    }, 300)
})

watch(houseNumberQuery, (newValue) => {
    if (addressDebounce) clearTimeout(addressDebounce)

    if (newValue === form.houseNumber) {
        return
    }

    form.houseNumber = newValue

    addressDebounce = setTimeout(() => {
        searchAddresses(newValue)
    }, 300)
})

const submit = () => {
    form.submit()
}

</script>

<template>
    <div class="order-create">
        <h1>Dokoncenie objednavky</h1>

        <form @submit.prevent="submit" class="order-form">
            <div class="form-section">
                <h2>Kontaktne udaje</h2>

                <p v-if="form.validating">Validating...</p>

                <div class="form-group">
                    <label for="email">Email *</label>
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        required
                        placeholder="vas@email.sk"
                        @change="form.validate('email')"
                    />
                    <span v-if="form.errors.email || form.invalid('email')" class="error">{{ form.errors.email }}</span>
                </div>

                <div class="form-group">
                    <label for="phone">Telefon</label>
                    <input
                        id="phone"
                        v-model="form.phone"
                        type="tel"
                        placeholder="+421 XXX XXX XXX alebo bez predvolby"
                        @change="form.validate('phone')"
                    />
                    <span v-if="form.errors.phone" class="error">{{ form.errors.phone }}</span>
                </div>
            </div>

            <div class="form-section">
                <h2>Adresa dorucenia</h2>

                <div class="form-group autocomplete">
                    <label for="city">Mesto *</label>
                    <input
                        id="city"
                        v-model="cityQuery"
                        type="text"
                        required
                        placeholder="Zadajte mesto"
                        autocomplete="off"
                        @focus="showCityDropdown = cities.length > 0"
                        @blur="showCityDropdown = false"
                    />
                    <div v-if="loadingCities" class="loading">Hladam...</div>
                    <ul v-if="showCityDropdown && cities.length > 0" class="dropdown">
                        <li
                            v-for="city in cities"
                            :key="city.name"
                            @mousedown="selectCity(city)"
                        >
                            {{ city.name }}
                            <span v-if="city.postcode" class="postcode">({{ city.postcode }})</span>
                        </li>
                    </ul>
                    <span v-if="form.errors.city" class="error">{{ form.errors.city }}</span>
                </div>

                <div class="form-group autocomplete">
                    <label for="street">Ulica *</label>
                    <input
                        id="street"
                        v-model="streetQuery"
                        type="text"
                        required
                        placeholder="Zadajte ulicu"
                        autocomplete="off"
                        :disabled="!form.city"
                        @focus="showStreetDropdown = streets.length > 0"
                        @blur="showStreetDropdown = false"
                    />
                    <div v-if="loadingStreets" class="loading">Hladam...</div>
                    <ul v-if="showStreetDropdown && streets.length > 0" class="dropdown">
                        <li
                            v-for="street in streets"
                            :key="street.name"
                            @mousedown="selectStreet(street)"
                        >
                            {{ street.name }}
                            <span v-if="street.postcode" class="postcode">({{ street.postcode }})</span>
                        </li>
                    </ul>
                    <span v-if="form.errors.street" class="error">{{ form.errors.street }}</span>
                </div>

                <div class="form-group autocomplete">
                    <label for="houseNumber">Číslo domu *</label>
                    <input
                        id="houseNumber"
                        v-model="houseNumberQuery"
                        type="text"
                        required
                        placeholder="Zadajte číslo domu"
                        autocomplete="off"
                        :disabled="!form.street"
                        @focus="showAddressDropdown = addresses.length > 0"
                        @blur="showAddressDropdown = false"
                    />
                    <div v-if="loadingAddresses" class="loading">Hladam...</div>
                    <ul v-if="showAddressDropdown && addresses.length > 0" class="dropdown">
                        <li
                            v-for="address in addresses"
                            :key="address.streetNumber"
                            @mousedown="selectAddress(address)"
                        >
                            {{ address.streetNumber }}
                            <span v-if="address.postcode" class="postcode">({{ address.postcode }})</span>
                        </li>
                    </ul>
                    <span v-if="form.errors.houseNumber" class="error">{{ form.errors.houseNumber }}</span>
                </div>

                <div class="form-group">
                    <label for="zip">PSC *</label>
                    <input
                        id="zip"
                        v-model="form.zip"
                        type="text"
                        required
                        placeholder="XXX XX"
                        :class="{ 'auto-filled': form.zip }"
                    />
                    <span v-if="form.errors.zip" class="error">{{ form.errors.zip }}</span>
                </div>
            </div>

            <button type="submit" :disabled="form.processing" class="submit-btn">
                {{ form.processing ? 'Odosielam...' : 'Odoslat objednavku' }}
            </button>
        </form>
    </div>
</template>

<style scoped>
.order-create {
    max-width: 600px;
    margin: 0 auto;
    padding: 2rem;
}

.order-form {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.form-section {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.form-section h2 {
    font-size: 1.25rem;
    margin-bottom: 0.5rem;
    border-bottom: 1px solid #eee;
    padding-bottom: 0.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    position: relative;
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

.form-group input:disabled {
    background-color: #f5f5f5;
    cursor: not-allowed;
}

.form-group input.auto-filled {
    background-color: #e8f5e9;
    color: #333;
}

.autocomplete {
    position: relative;
}

.dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ddd;
    border-top: none;
    border-radius: 0 0 4px 4px;
    max-height: 200px;
    overflow-y: auto;
    z-index: 100;
    list-style: none;
    margin: 0;
    padding: 0;
}

.dropdown li {
    padding: 0.75rem;
    cursor: pointer;
}

.dropdown li:hover {
    background-color: #f5f5f5;
}

.dropdown .postcode {
    color: #666;
    font-size: 0.875rem;
}

.loading {
    position: absolute;
    right: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    color: #666;
    font-size: 0.875rem;
}

.error {
    color: #dc3545;
    font-size: 0.875rem;
}

.submit-btn {
    padding: 1rem 2rem;
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
