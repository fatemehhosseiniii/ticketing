import { ref } from 'vue'

export const toast = ref({
    show: false,
    message: '',
    type: 'success'
})

/** show toast **/
export const showToast = (message, type = 'success') => {
    toast.value = {
        show: true,
        message,
        type
    }
    setTimeout(() => {

        toast.value.show = false

    }, 3000)
}
