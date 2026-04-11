import { defineStore } from 'pinia';
import { api } from '@utils/http.mjs';
import { ref } from 'vue';

export const useImageStore = defineStore('image', () => {
    const images = ref([]);

    async function getImages() {
        const response = await api.get('images');
        images.value = response.data.data;
    }

    async function getImage(id) {
        const response = await api.get(`images/${id}`);
        return response.data.data;
    }

    async function uploadImage(file, spotId) {
        const formData = new FormData();
        formData.append('image', file);
        formData.append('spot_id', spotId);

        const response = await api.post('images', formData);

        images.value.push(response.data.data);

        return response.data.data;
    }

    async function deleteImage(id) {
        await api.delete(`images/${id}`);

        const index = images.value.findIndex(i => i.id === id);
        if (index !== -1) {
            images.value.splice(index, 1);
        }
    }

    return {
        images,
        getImages,
        getImage,
        uploadImage,
        deleteImage
    }
});