import { defineStore } from 'pinia';
import { api } from '@utils/http.mjs';
import { ref } from 'vue';

export const useImageStore = defineStore('image', () => {
    const images = ref([]);

    async function getImages() {
        const response = await api.get('images');
        images.value = response.data.data;
    }

    async function uploadImage(file, spotId) {
        const formData = new FormData();
        formData.append('image', file);
        formData.append('spot_id', spotId);

        const response = await api.post('images', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });

        const created = response.data.data;
        images.value.push(created);

        return created;
    }

    async function deleteImage(id) {
        await api.delete(`images/${id}`);

        const index = images.value.findIndex(image => image.id === id);

        if (index !== -1) {
            images.value.splice(index, 1);
        }
    }

    return {
        images,
        getImages,
        uploadImage,
        deleteImage
    };
});