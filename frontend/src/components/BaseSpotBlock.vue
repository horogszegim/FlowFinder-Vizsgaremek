<script setup>
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import { getTagStyle } from '@utils/tagColors';

const props = defineProps({
    spot: Object
});

const router = useRouter();

const goToSpot = () => {
    router.push(`/spotok/${props.spot.id}`);
};

const firstImage = computed(() => {
    return props.spot?.images?.[0]?.url || new URL('@assets/img/spot-placeholder.png', import.meta.url).href;
});
</script>

<template>
    <div @click="goToSpot"
        class="flex flex-col md:flex-row items-start gap-5 p-5 bg-background border border-text-muted rounded-xl shadow-lg cursor-pointer transition hover:-translate-y-1 md:h-64 overflow-hidden">

        <div class="w-full md:w-72 aspect-[3/1.5] md:aspect-[3/2.5] md:h-full shrink-0">
            <div class="w-full h-full overflow-hidden rounded-xl">
                <img :src="firstImage" class="w-full h-full object-cover object-center" />
            </div>
        </div>

        <div class="flex-1 min-w-0 w-full h-full flex flex-col justify-between gap-3 overflow-hidden">

            <div class="shrink-0 min-w-0 w-full overflow-hidden">
                <h2
                    class="block w-full max-w-full min-w-0 overflow-hidden whitespace-nowrap text-ellipsis text-2xl md:text-3xl font-bold text-text leading-tight">
                    {{ spot.title }}
                </h2>

                <p class="text-sm text-text-muted break-all">
                    Felfedezte: {{ spot?.created_by?.username || 'Ismeretlen' }}
                </p>
            </div>

            <div class="flex-1 flex items-center min-w-0 w-full">
                <div v-if="spot.sports_and_tags?.length" class="flex flex-wrap gap-1.5 lg:gap-3">
                    <span v-for="tag in spot.sports_and_tags" :key="tag.id" :style="getTagStyle(tag.id)"
                        class="px-2 lg:px-3 py-1 rounded-xl text-md font-semibold">
                        {{ tag.name }}
                    </span>
                </div>
            </div>

            <div class="shrink-0 min-w-0 w-full overflow-hidden">
                <p class="text-text-muted text-sm md:text-base line-clamp-3 break-all overflow-hidden max-w-full">
                    {{ spot.description }}
                </p>
            </div>

        </div>
    </div>
</template>