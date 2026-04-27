<script setup>
import BaseLayout from '@layouts/BaseLayout.vue';
import SpotForm from '@components/SpotForm.vue';

import { useSpotStore } from '@stores/SpotStore';
import { useImageStore } from '@stores/ImageStore';

import { useRouter } from 'vue-router';
import { ref } from 'vue';

const spotStore = useSpotStore();
const imageStore = useImageStore();

const router = useRouter();

const errorMessage = ref('');
const successMessage = ref('');

const uploading = ref(false);

async function submitForm(payload) {
    errorMessage.value = '';
    successMessage.value = '';

    uploading.value = true;

    try {
        const createdSpot = await spotStore.createSpot(payload.spot);
        const spotId = createdSpot.id;

        for (const file of payload.newImages) {
            await imageStore.uploadImage(file, spotId);
        }

        successMessage.value = 'Spot sikeresen feltöltve!';

        setTimeout(() => {
            router.push(`/spotok/${spotId}`);
        }, 1000);
    } catch (err) {
        errorMessage.value = 'Hiba történt a feltöltés során!';
    } finally {
        uploading.value = false;
    }
}
</script>

<template>
    <BaseLayout>
        <div class="w-full flex justify-center items-center my-10">
            <div class="w-full max-w-5xl">
                <h1 class="text-4xl font-bold text-text mb-2">
                    Új spot feltöltése
                </h1>

                <p class="text-xl text-text-muted mb-10">
                    Ossz meg egy új helyet másokkal!
                </p>

                <SpotForm :loading="uploading" :error-message="errorMessage" :success-message="successMessage"
                    submit-text="Spot feltöltése" loading-text="Feltöltés ..." @submit="submitForm" />
            </div>
        </div>
    </BaseLayout>
</template>

<route lang="yaml">
name: feltoltes
meta:
  title: Spot feltöltés
  requiresAuth: true
</route>