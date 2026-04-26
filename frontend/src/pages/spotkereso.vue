<script setup>
import { ref, onMounted, computed, onBeforeUnmount, watch } from 'vue';
import BaseLayout from '@layouts/BaseLayout.vue';
import BaseSpotBlock from '@components/BaseSpotBlock.vue';
import { useSpotStore } from '@stores/SpotStore.js';
import { useSportsAndTagStore } from '@stores/SportsAndTagStore';
import { getTagStyle } from '@utils/tagColors';

const spotStore = useSpotStore();
const sportsAndTagStore = useSportsAndTagStore();

const search = ref('');
const selectedTags = ref([]);
const tagScroller = ref(null);

const perPage = 100;
const currentPage = ref(1);

let wheelHandler = null;

const normalize = (value) =>
    (value || '').toString().trim().toLowerCase();

const tags = computed(() => sportsAndTagStore.sportsAndTags);
const spots = computed(() => spotStore.spots);
const isLoading = computed(() => spotStore.loading);

const toggleTag = (id) => {
    const selected = selectedTags.value;

    selectedTags.value = selected.includes(id)
        ? selected.filter(tagId => tagId !== id)
        : [...selected, id];
};

const scrollTagsLeft = () => {
    tagScroller.value?.scrollBy({
        left: -250,
        behavior: 'smooth'
    });
};

const scrollTagsRight = () => {
    tagScroller.value?.scrollBy({
        left: 250,
        behavior: 'smooth'
    });
};

onMounted(async () => {
    spotStore.getSpots();
    sportsAndTagStore.getSportsAndTags();

    const el = tagScroller.value;

    if (!el) return;

    wheelHandler = (e) => {
        e.preventDefault();
        el.scrollLeft += e.deltaY + e.deltaX;
    };

    el.addEventListener('wheel', wheelHandler, { passive: false });
});

onBeforeUnmount(() => {
    if (tagScroller.value && wheelHandler) {
        tagScroller.value.removeEventListener('wheel', wheelHandler);
    }
});

const filteredSpots = computed(() => {
    const query = normalize(search.value);

    return spots.value.filter((spot) => {
        const tagList = spot.sports_and_tags || [];

        const textMatch =
            !query ||
            normalize(spot.title).includes(query) ||
            normalize(spot.description).includes(query) ||
            normalize(spot.created_by?.username).includes(query) ||
            tagList.some(tag => normalize(tag.name).includes(query));

        const selectedTagsMatch =
            selectedTags.value.length === 0 ||
            selectedTags.value.some(selectedId =>
                tagList.some(tag => tag.id === selectedId)
            );

        return textMatch && selectedTagsMatch;
    });
});

const totalPages = computed(() =>
    Math.max(1, Math.ceil(filteredSpots.value.length / perPage))
);

const paginatedSpots = computed(() => {
    const start = (currentPage.value - 1) * perPage;
    const end = start + perPage;

    return filteredSpots.value.slice(start, end);
});

const goToPage = (page) => {
    if (page < 1 || page > totalPages.value) return;

    currentPage.value = page;

    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
};

watch([search, selectedTags], () => {
    currentPage.value = 1;
}, { deep: true });

watch(totalPages, (value) => {
    if (currentPage.value > value) {
        currentPage.value = value;
    }
});

const visiblePages = computed(() => {
    const pages = [];

    const start = Math.max(1, currentPage.value - 2);
    const end = Math.min(totalPages.value, currentPage.value + 2);

    for (let i = start; i <= end; i++) {
        pages.push(i);
    }

    return pages;
});

const hasActiveFilter = computed(() =>
    search.value.trim().length > 0 || selectedTags.value.length > 0
);

const remainingResults = computed(() => {
    const shown = currentPage.value * perPage;
    return Math.max(0, filteredSpots.value.length - shown);
});
</script>

<template>
    <BaseLayout>
        <div class="w-full flex justify-center items-start my-5">
            <div class="w-full max-w-7xl">
                <h1 class="text-5xl font-bold text-text mb-1">
                    Spotkereső
                </h1>

                <p class="text-xl text-text-muted mb-10">
                    Fedezd fel a legjobb spotokat!
                </p>

                <input v-model="search" type="text" placeholder="Keresés a spotok között ..."
                    class="h-12 w-full rounded-xl border border-text-muted bg-background px-5 py-3 text-base text-text shadow-md mb-4 outline-none focus:ring-2 focus:ring-primary-dark focus:border-primary-dark" />

                <div class="flex items-center gap-3 mb-10">
                    <button type="button" @click="scrollTagsLeft"
                        class="shrink-0 w-12 h-12 rounded-xl border border-text-muted bg-background shadow-md hover:brightness-95 transition cursor-pointer flex items-center justify-center">
                        <img src="@assets/img/left-arrow-white.svg" class="w-5 h-5 invert" />
                    </button>

                    <div ref="tagScroller" class="flex-1 overflow-x-auto scrollbar-hide">
                        <div class="flex gap-3 min-w-max py-1">
                            <span v-for="tag in tags" :key="tag.id" @click="toggleTag(tag.id)"
                                :style="getTagStyle(tag.id)"
                                class="px-3 py-1 rounded-xl text-md font-semibold cursor-pointer whitespace-nowrap transition"
                                :class="selectedTags.includes(tag.id)
                                    ? 'scale-105 ring-2 ring-offset-2 ring-primary-dark'
                                    : 'opacity-70'">
                                {{ tag.name }}
                            </span>
                        </div>
                    </div>

                    <button type="button" @click="scrollTagsRight"
                        class="shrink-0 w-12 h-12 rounded-xl border border-text-muted bg-background shadow-md hover:brightness-95 transition cursor-pointer flex items-center justify-center">
                        <img src="@assets/img/right-arrow-white.svg" class="w-5 h-5 invert" />
                    </button>
                </div>

                <div v-if="isLoading" class="flex flex-col items-center justify-center py-15 gap-5">
                    <div class="w-12 h-12 border-5 border-text-muted border-t-primary-dark rounded-full animate-spin">
                    </div>

                    <p class="text-lg text-text-muted font-semibold">
                        Spotok betöltése ...
                    </p>
                </div>

                <p v-else-if="filteredSpots.length === 0" class="text-red-700 mt-10 text-center text-3xl font-bold">
                    Nincs találat a keresésre!
                </p>

                <div v-else class="flex flex-col gap-5">
                    <BaseSpotBlock v-for="spot in paginatedSpots" :key="spot.id" :spot="spot" />
                </div>

                <div v-if="!isLoading" class="my-10">
                    <p v-if="hasActiveFilter && remainingResults > 0" class="text-center text-text-muted mb-5">
                        <span class="text-text font-semibold">{{ remainingResults }}</span> további találat ...
                    </p>

                    <div v-if="filteredSpots.length > perPage" class="flex flex-wrap justify-center items-center gap-3">
                        <button type="button" @click="goToPage(currentPage - 1)" :disabled="currentPage === 1"
                            class="shrink-0 w-12 h-12 rounded-xl border border-text-muted bg-background shadow-md hover:brightness-95 transition cursor-pointer flex items-center justify-center disabled:opacity-40 disabled:cursor-default">
                            <img src="@assets/img/left-arrow-white.svg" class="w-5 h-5 invert" />
                        </button>

                        <button v-for="page in visiblePages" :key="page" @click="goToPage(page)"
                            class="min-w-12 h-12 px-4 rounded-xl border bg-background shadow-md hover:brightness-95 transition cursor-pointer font-semibold"
                            :class="page === currentPage
                                ? 'bg-primary-dark text-white border-primary-dark'
                                : 'border-text-muted'">
                            {{ page }}
                        </button>

                        <button type="button" @click="goToPage(currentPage + 1)" :disabled="currentPage === totalPages"
                            class="shrink-0 w-12 h-12 rounded-xl border border-text-muted bg-background shadow-md hover:brightness-95 transition cursor-pointer flex items-center justify-center disabled:opacity-40 disabled:cursor-default">
                            <img src="@assets/img/right-arrow-white.svg" class="w-5 h-5 invert" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </BaseLayout>
</template>

<route lang="yaml">
name: spotkereso
meta:
  title: Spotkereső
</route>