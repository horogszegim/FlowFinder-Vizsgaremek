<script setup>
import BaseLayout from '@layouts/BaseLayout.vue';
import { useAuthStore } from '@/stores/AuthStore.js';

const auth = useAuthStore();

function logout() {
    auth.logout();
};
</script>

<template>
    <BaseLayout>
        <div class="flex flex-col gap-5">
            <h1>Profil oldal</h1>
            <button @click="logout()">Kijelentkezés</button>
        </div>
    </BaseLayout>
</template>

<route lang="yaml">
name: profil
meta:
  title: Profil
  requiresAuth: true
</route>