<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const name = ref('')
const email = ref('')
const password = ref('')
const errors = ref({})

async function register() {
    errors.value = {}
    const res = await fetch('/api/auth/register', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            name: name.value,
            email: email.value,
            password: password.value
        })
    })
    const data = await res.json()
    if (!res.ok || !data.data) {
        console.log('Error:', data)

        if (data.errors && typeof data.errors === 'object') {
            Object.entries(data.errors).forEach(([field, messages]) => {
                errors.value = data.errors
            })
        }

        return
    }

    localStorage.setItem('token', data.data.token)
    router.push('/dashboard')

}
</script>

<template>
    <div class="auth-container">
        <h2>Register</h2>

        <input v-model="name" placeholder="Name" @input="errors.name = null"/>
        <small v-if="errors.name" class="error-text">{{ errors.name[0] }}</small>
        <input v-model="email" placeholder="Email" @input="errors.email = null"/>
        <small v-if="errors.email" class="error-text">{{ errors.email[0] }}</small>
        <input v-model="password" type="password" placeholder="Password" @input="errors.password = null"/>
        <small v-if="errors.password" class="error-text">{{ errors.password[0] }}</small>

        <button @click="register">Register</button>

        <p @click="$router.push('/')" class="link">
            Already have account?
        </p>
    </div>

</template>
