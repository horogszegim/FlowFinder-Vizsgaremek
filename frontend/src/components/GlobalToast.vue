<script setup>
import { useToastStore } from '@/stores/ToastStore'

const toast = useToastStore()
</script>

<template>
    <transition enter-active-class="transition duration-300 ease-out"
        enter-from-class="opacity-0 -translate-y-5 scale-90" enter-to-class="opacity-100 translate-y-0 scale-100"
        leave-active-class="transition duration-200 ease-in" leave-from-class="opacity-100"
        leave-to-class="opacity-0 -translate-y-4">
        <div v-if="toast.show" class="fixed top-15 left-1/2 -translate-x-1/2 z-[999]">
            <div :class="toast.type === 'error' ? 'bg-red-700' : 'bg-primary-dark'"
                class="text-white px-6 py-3 rounded-xl shadow-lg flex items-center gap-2">
                {{ toast.message }}
            </div>
        </div>
    </transition>
</template>