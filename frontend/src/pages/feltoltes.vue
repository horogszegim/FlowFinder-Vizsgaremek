<script setup>
import BaseLayout from '@layouts/BaseLayout.vue';

import { useSpotStore } from '@stores/SpotStore';
import { useImageStore } from '@stores/ImageStore';
import { useAuthStore } from '@stores/AuthStore';
import { useSportsAndTagStore } from '@stores/SportsAndTagStore';

import { useRouter } from 'vue-router';
import { ref, onMounted } from 'vue';
import { getTagStyle } from '@utils/tagColors';

const spotStore = useSpotStore();
const imageStore = useImageStore();
const authStore = useAuthStore();
const sportsAndTagStore = useSportsAndTagStore();

const router = useRouter();

const tags = ref([]);
const selectedTags = ref([]);
const uploadedImages = ref([]);

const imageError = ref('');
const tagError = ref('');
const errorMessage = ref('');
const successMessage = ref('');

const uploading = ref(false);
const dragActive = ref(false);

onMounted(async () => {
    await sportsAndTagStore.getSportsAndTags();
    tags.value = sportsAndTagStore.sportsAndTags;
});

function showTempError(refVar, message) {
    refVar.value = message;
    setTimeout(() => {
        refVar.value = '';
    }, 2500);
}

function toggleTag(id) {
    if (selectedTags.value.includes(id)) {
        selectedTags.value = selectedTags.value.filter(t => t !== id);
        return;
    }

    if (selectedTags.value.length >= 5) {
        showTempError(tagError, 'Maximum 5 taget választhatsz!');
        return;
    }

    selectedTags.value.push(id);
}

function handleFiles(fileList) {
    imageError.value = '';

    const newFiles = Array.from(fileList);

    if (uploadedImages.value.length + newFiles.length > 10) {
        showTempError(imageError, 'Maximum 10 képet tölthetsz fel!');
        return;
    }

    for (const file of newFiles) {
        if (!['image/jpeg', 'image/png'].includes(file.type)) {
            showTempError(imageError, 'Csak JPG és PNG képeket tölthetsz fel!');
            return;
        }

        if (file.size > 3 * 1024 * 1024) {
            showTempError(imageError, 'Egy kép maximum 3MB lehet!');
            return;
        }
    }

    newFiles.forEach(file => {
        uploadedImages.value.push({
            file,
            preview: URL.createObjectURL(file)
        });
    });
}

function onDrop(e) {
    e.preventDefault();
    dragActive.value = false;
    handleFiles(e.dataTransfer.files);
}

function onDragOver(e) {
    e.preventDefault();
    dragActive.value = true;
}

function onDragLeave() {
    dragActive.value = false;
}

function moveImage(index, direction) {
    const newIndex = index + direction;
    if (newIndex < 0 || newIndex >= uploadedImages.value.length) return;

    const temp = uploadedImages.value[index];
    uploadedImages.value[index] = uploadedImages.value[newIndex];
    uploadedImages.value[newIndex] = temp;
}

function removeImage(index) {
    URL.revokeObjectURL(uploadedImages.value[index].preview);
    uploadedImages.value.splice(index, 1);
}

function validateImages() {
    if (uploadedImages.value.length < 1) {
        imageError.value = 'Legalább 1 képet fel kell töltened!';
        return false;
    }

    return true;
}

function validateTags() {
    if (selectedTags.value.length > 5) {
        tagError.value = 'Maximum 5 taget választhatsz!';
        return false;
    }

    return true;
}

function onlyNumberAndDot(node) {
    return /^[0-9.]+$/.test(node.value || '');
}

async function submitForm(data) {
    errorMessage.value = '';
    successMessage.value = '';

    if (!validateTags() || !validateImages()) return;

    uploading.value = true;

    try {
        const spotPayload = {
            ...data,
            sports_and_tags: selectedTags.value
        };

        const createdSpot = await spotStore.createSpot(spotPayload);
        const spotId = createdSpot.id;

        for (const img of uploadedImages.value) {
            await imageStore.uploadImage(img.file, spotId);
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

                <FormKit type="form" :actions="false" @submit="submitForm">
                    <FormKit type="text" name="title" label="Cím" placeholder="Spot neve ..."
                        validation="required|length:1,60" validation-visibility="dirty" :validation-messages="{
                            required: 'A cím megadása kötelező!',
                            length: 'A cím maximum 60 karakter lehet!'
                        }" />

                    <div class="my-2" v-if="uploadedImages.length < 10">
                        <label class="text-lg font-semibold text-text block mb-2">
                            Képek (1-10)
                        </label>

                        <div class="mt-1 shadow-lg w-full bg-background border-2 border-dashed rounded-xl px-5 py-10 text-lg text-text-muted transition"
                            :class="dragActive ? 'border-primary-dark ring-2 ring-primary-dark' : 'border-text-muted'"
                            @drop="onDrop" @dragover="onDragOver" @dragleave="onDragLeave">
                            <div class="flex flex-col items-center justify-center text-center">
                                <p class="text-text text-lg font-semibold mb-2">
                                    Húzd ide a képeket
                                </p>
                                <p class="text-text-muted mb-4">
                                    vagy válaszd ki őket az eszközödről
                                </p>

                                <input id="spot-images" type="file" multiple accept="image/png, image/jpeg"
                                    class="hidden" @change="e => handleFiles(e.target.files)">

                                <label for="spot-images"
                                    class="px-5 py-3 bg-primary-dark text-white rounded-xl shadow-lg cursor-pointer transition hover:brightness-120 hover:-translate-y-1">
                                    Képek kiválasztása
                                </label>
                            </div>
                        </div>
                    </div>

                    <p v-else class="text-sm font-medium text-text-muted text-center mt-2">
                        Elérted a maximum képszámot. Törölj egy képet, hogy újat tölthess fel!
                    </p>

                    <p v-if="imageError" class="text-sm font-medium text-red-600 text-center mt-2">
                        {{ imageError }}
                    </p>

                    <div v-if="uploadedImages.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mt-5">
                        <div v-for="(img, index) in uploadedImages" :key="`${img.preview}-${index}`"
                            class="relative rounded-xl overflow-hidden shadow-lg">
                            <img :src="img.preview" class="w-full h-50 object-cover" />

                            <button type="button"
                                class="absolute bottom-4 left-4 bg-white/25 backdrop-blur-lg p-2 rounded-xl transition hover:bg-white/40 hover:scale-105 active:scale-95 cursor-pointer"
                                @click="moveImage(index, -1)">
                                <img src="@assets/img/left-arrow-white.svg" class="h-5 w-5" />
                            </button>

                            <button type="button"
                                class="absolute bottom-4 right-4 bg-white/25 backdrop-blur-lg p-2 rounded-xl transition hover:bg-white/40 hover:scale-105 active:scale-95 cursor-pointer"
                                @click="moveImage(index, 1)">
                                <img src="@assets/img/right-arrow-white.svg" class="h-5 w-5" />
                            </button>

                            <button type="button"
                                class="absolute left-1/2 -translate-x-1/2 bottom-4 bg-red-500/50 backdrop-blur-lg p-2 rounded-xl transition hover:bg-red-500/80 hover:scale-105 active:scale-95 cursor-pointer"
                                @click="removeImage(index)">
                                <img src="@assets/img/trash-white.svg" class="h-5 w-5" />
                            </button>
                        </div>
                    </div>

                    <FormKit type="textarea" name="description" label="Leírás" placeholder="Írj leírást a spotról ..."
                        validation="required" validation-visibility="dirty" :validation-messages="{
                            required: 'A leírás megadása kötelező!'
                        }" />

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <FormKit type="text" name="latitude" label="Szélesség (latitude)" placeholder="pl. 47.5051"
                            validation="required|length:1,25|onlyNumberAndDot" :validation-rules="{ onlyNumberAndDot }"
                            validation-visibility="dirty" :validation-messages="{
                                required: 'A szélesség megadása kötelező!',
                                length: 'A szélesség maximum 25 karakter lehet!',
                                onlyNumberAndDot: 'A szélesség megadásához csak szám és pont használható!'
                            }" />

                        <FormKit type="text" name="longitude" label="Hosszúság (longitude)" placeholder="pl. 19.1463"
                            validation="required|length:1,25|onlyNumberAndDot" :validation-rules="{ onlyNumberAndDot }"
                            validation-visibility="dirty" :validation-messages="{
                                required: 'A hosszúság megadása kötelező!',
                                length: 'A hosszúság maximum 25 karakter lehet!',
                                onlyNumberAndDot: 'A hosszúság megadásához csak szám és pont használható!'
                            }" />
                    </div>

                    <div class="flex flex-col gap-3 mt-3">
                        <label class="text-lg font-semibold text-text">
                            Tagek (max 5)
                        </label>

                        <div class="flex flex-wrap gap-3">
                            <span v-for="tag in tags" :key="tag.id" @click="toggleTag(tag.id)"
                                :style="getTagStyle(tag.id)"
                                class="px-3 py-1 rounded-xl text-md font-semibold cursor-pointer transition" :class="selectedTags.includes(tag.id)
                                    ? 'scale-105 ring-2 ring-offset-2 ring-primary-dark'
                                    : 'opacity-70 hover:opacity-100'">
                                {{ tag.name }}
                            </span>
                        </div>

                        <p v-if="tagError" class="text-sm font-medium text-red-600 text-center">
                            {{ tagError }}
                        </p>
                    </div>

                    <p v-if="errorMessage" class="text-md font-medium mt-3 text-red-600 text-center">
                        {{ errorMessage }}
                    </p>

                    <p v-if="successMessage" class="text-md font-medium mt-3 text-green-600 text-center">
                        {{ successMessage }}
                    </p>

                    <button type="submit" :disabled="uploading"
                        class="mt-5 mb-10 w-full text-xl font-semibold py-3 bg-primary-dark text-white rounded-xl shadow-lg cursor-pointer transition hover:brightness-120 hover:-translate-y-1 disabled:opacity-50">
                        {{ uploading ? 'Feltöltés ...' : 'Spot feltöltése' }}
                    </button>
                </FormKit>
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