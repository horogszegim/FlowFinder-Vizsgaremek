<script setup>
import BaseHeader from '@components/layout/BaseHeader.vue';
import BaseFooter from '@components/layout/BaseFooter.vue';
import GlobalToast from '@/components/GlobalToast.vue';
</script>

<template>
  <div class="min-h-screen flex flex-col">
    <BaseHeader />

    <main class="container mx-auto mt-25 flex-1 flex px-3">
      <slot />
    </main>

    <BaseFooter />

    <GlobalToast />
  </div>
</template>
