<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { toast } from './toast'

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


    <!-- Toast -->
    <transition name="toast">

        <div
            v-if="toast.show"
            class="toast"
            :class="toast.type"
        >
            {{ toast.message }}
        </div>

    </transition>
</template>

