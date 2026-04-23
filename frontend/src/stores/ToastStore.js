import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useToastStore = defineStore('toast', () => {
    const show = ref(false)
    const message = ref('')
    const type = ref('success')

    function trigger(msg, t = 'success') {
        message.value = msg
        type.value = t
        show.value = true

        setTimeout(() => {
            show.value = false
        }, 3000)
    }

    return {
        show,
        message,
        type,
        trigger
    }
})