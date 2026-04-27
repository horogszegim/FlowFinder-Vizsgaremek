<script setup>
import BaseLayout from '@layouts/BaseLayout.vue';
import SpotForm from '@components/SpotForm.vue';
import { useSpotStore } from '@stores/SpotStore.js';
import { useImageStore } from '@stores/ImageStore.js';
import { useAuthStore } from '@stores/AuthStore.js';
import { useToastStore } from '@stores/ToastStore.js';
import { useRoute, useRouter } from 'vue-router';
import { computed, onMounted, ref } from 'vue';

const route = useRoute();
const router = useRouter();
const spotStore = useSpotStore();
const imageStore = useImageStore();
const authStore = useAuthStore();
const toast = useToastStore();

const spot = ref(null);
const loading = ref(true);
const saving = ref(false);

const canEdit = computed(() => {
    return authStore.isAdmin || spot.value?.created_by?.id === authStore.user?.id;
});

onMounted(async () => {
    try {
        await authStore.fetchUser();
        spot.value = await spotStore.getSpot(route.params.id);

        if (!canEdit.value) {
            toast.trigger('Nincs jogosultságod a spot szerkesztéséhez!', 'error');
            router.push('/profil');
            return;
        }

    } catch {
        toast.trigger('A spot betöltése sikertelen!', 'error');
        router.push('/profil');
    } finally {
        loading.value = false;
    }
});

async function updateSpot(payload) {
    saving.value = true;

    try {
        await spotStore.updateSpot(spot.value.id, payload.spot);

        for (const imageId of payload.removedImageIds) {
            await imageStore.deleteImage(imageId);
        }

        const imageOrder = [];

        for (const image of payload.orderedImages) {
            if (image.existing) {
                imageOrder.push(image.id);
                continue;
            }

            const createdImage = await imageStore.uploadImage(image.file, spot.value.id);
            imageOrder.push(createdImage.id);
        }

        const updatedSpot = await spotStore.updateImageOrder(spot.value.id, imageOrder);
        spotStore.setSpot(updatedSpot);

        toast.trigger('Spot sikeresen módosítva!');
        router.push('/profil');
    } catch (error) {
        if (error.response?.status === 403) {
            toast.trigger('Nincs jogosultságod a spot szerkesztéséhez!', 'error');
            return;
        }

        if (error.response?.status === 422) {
            toast.trigger('Ellenőrizd a megadott adatokat!', 'error');
            return;
        }

        toast.trigger('Hiba történt a spot módosítása közben!', 'error');
    } finally {
        saving.value = false;
    }
}
</script>

<template>
    <BaseLayout>
        <div class="w-full flex justify-center items-center my-10">
            <div class="w-full max-w-5xl">
                <div v-if="loading" class="flex flex-col items-center justify-center py-15 gap-5">
                    <div class="w-12 h-12 border-5 border-text-muted border-t-primary-dark rounded-full animate-spin">
                    </div>

                    <p class="text-lg text-text-muted font-semibold">
                        Spot betöltése ...
                    </p>
                </div>

                <template v-else-if="spot && canEdit">
                    <h1 class="text-4xl font-bold text-text mb-2">
                        Spot szerkesztése
                    </h1>

                    <p class="text-xl text-text-muted mb-10">
                        Módosítsd a spotod adatait!
                    </p>

                    <SpotForm mode="edit" :initial-spot="spot" :loading="saving" submit-text="Módosítás mentése"
                        loading-text="Mentés ..." @submit="updateSpot" />
                </template>
            </div>
        </div>
    </BaseLayout>
</template>

<route lang="yaml">
name: spot-szerkesztes
meta:
  title: Spot szerkesztés
  requiresAuth: true
</route>