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

    function setSpot(spot) {
        const index = spots.value.findIndex(s => s.id === spot.id);

        if (index !== -1) {
            spots.value.splice(index, 1, spot);
        } else {
            spots.value.push(spot);
        }
    }

    async function updateSpot(id, spot) {
        const response = await api.put(`spots/${id}`, spot);
        const updated = response.data.data;
        setSpot(updated);
        return updated;
    }

    async function deleteSpot(id) {
        await api.delete(`spots/${id}`);
        const index = spots.value.findIndex(s => s.id === id);

        if (index !== -1) {
            spots.value.splice(index, 1);
        }
    }

    async function updateImageOrder(id, imageOrder) {
        const response = await api.patch(`spots/${id}/images/order`, {
            image_order: imageOrder
        });

        const updated = response.data.data;
        setSpot(updated);
        return updated;
    }

    return {
        spots,
        loading,
        getSpots,
        getSpot,
        createSpot,
        updateSpot,
        setSpot,
        deleteSpot,
        updateImageOrder
    };
});