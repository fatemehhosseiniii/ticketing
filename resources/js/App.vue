<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

onMounted(() => {
    const token = localStorage.getItem('token')

    if (token) {
        router.replace('/dashboard')
    } else {
        router.replace('/')
    }
})
</script>

<template>
    <router-view />
</template>

