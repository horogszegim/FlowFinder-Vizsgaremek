import { defineStore } from 'pinia';
import { api } from '@utils/http.mjs';
import { ref } from 'vue';

export const useSavedSpotStore = defineStore('savedSpot', () => {
    const savedSpots = ref([]);

    async function getSavedSpots() {
        const response = await api.get('saved-spots');
        savedSpots.value = response.data.data;
    }

    function isSaved(spotId) {
        return savedSpots.value.some(s => s.spot_id === spotId);
    }

    async function saveSpot(spotId) {
        const response = await api.post('saved-spots', {
            spot_id: spotId
        });

        const saved = response.data.data;
        const exists = savedSpots.value.some(s => s.id === saved.id);

        if (!exists) {
            savedSpots.value.push(saved);
        }

        return saved;
    }

    async function deleteSavedSpot(id) {
        await api.delete(`saved-spots/${id}`);
        const index = savedSpots.value.findIndex(s => s.id === id);
        if (index !== -1) {
            savedSpots.value.splice(index, 1);
        }
    }

    function findSavedSpotId(spotId) {
        const item = savedSpots.value.find(s => s.spot_id === spotId);
        return item ? item.id : null;
    }

    function removeBySpotId(spotId) {
        savedSpots.value = savedSpots.value.filter(s => s.spot_id !== spotId);
    }

    return {
        savedSpots,
        getSavedSpots,
        isSaved,
        saveSpot,
        deleteSavedSpot,
        findSavedSpotId,
        removeBySpotId
    };
});