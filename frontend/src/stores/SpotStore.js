import { defineStore } from 'pinia';
import { api } from '@utils/http.mjs';
import { ref } from 'vue';

export const useSpotStore = defineStore('spot', () => {
    const spots = ref([]);
    const loading = ref(false);

    async function getSpots() {
        loading.value = true;

        try {
            const response = await api.get('spots');
            spots.value = response.data.data;
        } finally {
            loading.value = false;
        }
    }

    async function getSpot(id) {
        const response = await api.get(`spots/${id}`);
        return response.data.data;
    }

    async function createSpot(spot) {
        const response = await api.post('spots', spot);
        const created = response.data.data;
        spots.value.push(created);
        return created;
    }

    async function deleteSpot(id) {
        await api.delete(`spots/${id}`);
        const index = spots.value.findIndex(s => s.id === id);

        if (index !== -1) {
            spots.value.splice(index, 1);
        }
    }

    return {
        spots,
        loading,
        getSpots,
        getSpot,
        createSpot,
        deleteSpot
    };
});