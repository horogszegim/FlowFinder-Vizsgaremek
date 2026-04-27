<script setup>
import { onMounted } from 'vue';
import BaseLayout from '@layouts/BaseLayout.vue';

onMounted(() => {
    document.title = `404 | ${import.meta.env.VITE_APP_NAME}`;
});
</script>

<template>
    <BaseLayout>
        <div class="w-full min-h-[60vh] flex items-center justify-center px-3 py-20">
            <div class="w-full max-w-4xl text-center">
                <h1 class="text-8xl sm:text-9xl md:text-[200px] font-black text-primary-dark">
                    404.
                </h1>

                <p class="text-2xl sm:text-3xl md:text-4xl font-semibold text-text">
                    A kért oldal nem található.
                </p>

                <div
                    class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-5 sm:gap-10 text-2xl text-text-muted font-medium">
                    <RouterLink to="/" class="relative inline-block link-hover">
                        Kezdőlap
                    </RouterLink>

                    <RouterLink to="/spotkereso" class="relative inline-block link-hover">
                        Spotkereső
                    </RouterLink>

                    <RouterLink to="/feltoltes" class="relative inline-block link-hover">
                        Feltöltés
                    </RouterLink>
                </div>
            </div>
        </div>
    </BaseLayout>
</template>

<route lang="yaml">
name: not-found
meta:
  title: 404
</route>