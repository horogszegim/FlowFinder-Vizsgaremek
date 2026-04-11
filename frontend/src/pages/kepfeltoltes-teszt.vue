<script setup>
import { ref, onMounted } from 'vue';
import { useImageStore } from '@/stores/ImageStore';
import { useSpotStore } from '@/stores/SpotStore';

const imageStore = useImageStore();
const spotStore = useSpotStore();

const file = ref(null);
const spotId = ref(1);
const deleteImageId = ref('');
const deleteSpotId = ref('');
const response = ref(null);
const error = ref(null);

const handleFileChange = (e) => {
    file.value = e.target.files[0];
}

const fetchImages = async () => {
    try {
        await imageStore.getImages();
    } catch (err) {
        console.error(err);
    }
}

const upload = async () => {
    error.value = null;
    response.value = null;

    if (!file.value) {
        error.value = 'Nincs fájl kiválasztva';
        return;
    }

    try {
        const res = await imageStore.uploadImage(file.value, spotId.value);
        response.value = res;
    } catch (err) {
        error.value = err.response?.data || err.message;
    }
}

const deleteImage = async () => {
    if (!deleteImageId.value) return;

    try {
        await imageStore.deleteImage(deleteImageId.value);
        deleteImageId.value = '';
    } catch (err) {
        error.value = err.response?.data || err.message;
    }
}

const deleteSpot = async () => {
    if (!deleteSpotId.value) return;

    try {
        await spotStore.deleteSpot(deleteSpotId.value);
        deleteSpotId.value = '';

        await imageStore.getImages();
    } catch (err) {
        error.value = err.response?.data || err.message;
    }
}

onMounted(fetchImages);
</script>

<template>
    <div class="p-5 max-w-6xl mx-auto space-y-10">

        <h1 class="text-3xl font-bold text-center">Képfeltöltés teszt oldal</h1>

        <h2 class="text-xl font-semibold -mb-0 mt-10">Feltöltés</h2>

        <div class="bg-background p-5 rounded-xl shadow-lg space-y-5 mb-10">
            <div>
                <label class="block text-md font-medium">Spot ID</label>
                <input v-model="spotId" type="number" class="bg-background-light shadow-lg p-3 rounded-xl w-full" />
            </div>

            <input type="file" @change="handleFileChange"
                class="block cursor-pointer bg-background-light shadow-lg rounded-xl font-semibold p-3" />

            <button @click="upload" class="bg-blue-700 text-white px-5 py-3 rounded-xl cursor-pointer">
                Feltöltés
            </button>
        </div>

        <h2 class="text-xl font-semibold -mb-0 mt-10">Törlések</h2>

        <div class="bg-background p-5 rounded-xl shadow-lg space-y-5">
            <h2 class="font-semibold">Kép törlés (ID alapján)</h2>

            <input v-model="deleteImageId" type="number" placeholder="Kép ID"
                class="bg-background-light shadow-lg p-3 rounded-xl w-full" />

            <button @click="deleteImage" class="bg-red-700 text-white px-5 py-3 rounded-xl cursor-pointer">
                Kép törlése
            </button>
        </div>

        <div class="bg-background p-5 rounded-xl shadow-lg space-y-5">
            <h2 class="font-semibold">Spot törlés (képekkel együtt)</h2>

            <input v-model="deleteSpotId" type="number" placeholder="Spot ID"
                class="bg-background-light shadow-lg p-3 rounded-xl w-full" />

            <button @click="deleteSpot" class="bg-red-700 text-white px-5 py-3 rounded-xl cursor-pointer">
                Spot törlése
            </button>
        </div>

        <div v-if="response" class="bg-green-100 p-3 rounded">
            <h3 class="font-semibold">Siker</h3>
            <pre>{{ response }}</pre>
        </div>

        <div v-if="error" class="bg-red-100 p-3 rounded">
            <h3 class="font-semibold">Hiba</h3>
            <pre>{{ error }}</pre>
        </div>

        <div>

            <h2 class="text-xl font-semibold mt-10 mb-1 text-center">Összes kép</h2>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-5">
                <div v-for="img in imageStore.images" :key="img.id" class="rounded-xl p-3 bg-background shadow-lg">
                    <img v-if="img.url" :src="img.url" class="w-full h-50 object-cover rounded-xl mb-3" />

                    <div class="text-md">
                        <p><b>ID:</b> {{ img.id }}</p>
                        <p><b>Spot:</b> {{ img.spot_id }}</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<route lang="yaml">
name: kepfeltoltes-teszt
meta:
  title: Képfeltöltés teszt
</route>