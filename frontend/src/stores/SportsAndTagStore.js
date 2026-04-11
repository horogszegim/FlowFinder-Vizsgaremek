import { defineStore } from 'pinia';
import { api } from '@utils/http.mjs';
import { ref } from 'vue';

export const useSpotStore = defineStore('sportsAndTag', () => {
    const sportsAndTags = ref([]);

    async function getSportsAndTags() {
        const response = await api.get('sports-and-tags');
        sportsAndTags.value = response.data.data;
    }

    async function getSportsAndTag(id) {
        const response = await api.get(`sports-and-tags/${id}`);
        return response.data.data;
    }

    return {
        sportsAndTags,
        getSportsAndTags,
        getSportsAndTag,
    }
});