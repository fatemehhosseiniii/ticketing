<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const email = ref('')
const password = ref('')
const errors = ref({})

async function login() {
    errors.value = {}

    const res = await fetch('/api/auth/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
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
        <h2>Sign-in</h2>

        <input v-model="email" placeholder="Email" @input="errors.email = null"/>
        <small v-if="errors.email" class="error-text">{{ errors.email[0] }}</small>
        <input v-model="password" type="password" placeholder="Password" @input="errors.password = null"/>
        <small v-if="errors.password" class="error-text">{{ errors.password[0] }}</small>

        <button @click="login">Login</button>

        <p @click="$router.push('/register')" class="link">
           don't Have Account?
        </p>
    </div>

</template>
