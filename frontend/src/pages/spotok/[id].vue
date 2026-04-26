<script setup>
import BaseLayout from '@layouts/BaseLayout.vue';
import { useRoute } from 'vue-router';
import { useSpotStore } from '@stores/SpotStore';
import { useSavedSpotStore } from '@stores/SavedSpotStore';
import { useAuthStore } from '@stores/AuthStore';
import { useToastStore } from '@/stores/ToastStore';
import { ref, onMounted, computed } from 'vue';
import { getTagStyle } from '@utils/tagColors';

const route = useRoute();
const spotStore = useSpotStore();
const savedSpotStore = useSavedSpotStore();
const authStore = useAuthStore();
const toast = useToastStore();

const spot = ref({});

const bookmarked = ref(false);

const galleryOpen = ref(false);
const activeImage = ref(0);

const images = computed(() =>
  spot.value?.images?.map(img => img.url) || []
);

onMounted(async () => {
  spot.value = await spotStore.getSpot(route.params.id);

  if (authStore.isAuthenticated) {
    await savedSpotStore.getSavedSpots();
    bookmarked.value = savedSpotStore.isSaved(spot.value.id);
  }

  if (spot.value?.title) {
    document.title = `${spot.value.title} | ${import.meta.env.VITE_APP_NAME}`;
  }
});

async function toggleBookmark() {
  if (!authStore.isAuthenticated) {
    toast.trigger('Spot mentéséhez be kell jelentkezned!', 'error');
    return;
  }

  try {
    const spotId = spot.value.id;

    if (!bookmarked.value) {
      await savedSpotStore.saveSpot(spotId);
      bookmarked.value = true;
      toast.trigger('Spot sikeresen elmentve!');
    } else {
      const id = savedSpotStore.findSavedSpotId(spotId);
      if (id) {
        await savedSpotStore.deleteSavedSpot(id);
      }
      bookmarked.value = false;
      toast.trigger('Mentés eltávolítva!');
    }
  } catch (e) {
    toast.trigger('Hiba történt!', 'error');
  }
}

function openGallery(index) {
  activeImage.value = index;
  galleryOpen.value = true;
}

function closeGallery() {
  galleryOpen.value = false;
}

function nextImage() {
  activeImage.value = (activeImage.value + 1) % images.value.length;
}

function prevImage() {
  activeImage.value = (activeImage.value - 1 + images.value.length) % images.value.length;
}
</script>

<template>
  <BaseLayout>
    <div class="w-full flex flex-col gap-5 mt-5 overflow-x-hidden">

      <div class="flex items-start justify-between gap-4 min-w-0">

        <div class="flex items-center gap-5 flex-wrap min-w-0">

          <h1 class="text-5xl font-bold text-text mr-5 min-w-0 [overflow-wrap:anywhere] hyphens-auto">
            {{ spot.title }}
          </h1>

          <div v-if="spot.sports_and_tags?.length" class="hidden lg:flex flex-wrap gap-3 mt-1">
            <span v-for="tag in spot.sports_and_tags" :key="tag.id" :style="getTagStyle(tag.id)"
              class="px-3 py-1 rounded-xl text-md font-semibold">
              {{ tag.name }}
            </span>
          </div>

        </div>

        <button @click="toggleBookmark" class="relative link-hover cursor-pointer mt-2 pb-1 shrink-0">
          <div class="relative w-10 h-10">
            <img src="@assets/img/bookmark.svg" class="absolute inset-0 w-10 h-10 transition-all duration-400 ease-out"
              :class="bookmarked ? 'opacity-0 rotate-5 translate-y-1' : 'opacity-100'" />
            <img src="@assets/img/bookmark-filled.svg"
              class="absolute inset-0 w-10 h-10 transition-all duration-400 ease-out"
              :class="bookmarked ? 'opacity-100' : 'opacity-0 -rotate-5 translate-y-1'" />
          </div>
        </button>

      </div>

      <p v-if="spot.created_by?.username"
        class="text-text-muted text-md -mt-4 min-w-0 [overflow-wrap:anywhere] hyphens-auto">
        Felfedezte: <span class="text-text">{{ spot.created_by.username }}</span>
      </p>

      <div v-if="spot.sports_and_tags?.length" class="flex lg:hidden flex-wrap gap-3 -mt-1">
        <span v-for="tag in spot.sports_and_tags" :key="tag.id" :style="getTagStyle(tag.id)"
          class="px-3 py-1 rounded-xl text-md font-semibold">
          {{ tag.name }}
        </span>
      </div>

      <p v-if="spot.description" class="text-text-muted text-xl min-w-0 [overflow-wrap:anywhere] hyphens-auto">
        {{ spot.description }}
      </p>

      <div v-if="images.length" class="w-full">

        <div v-if="images.length === 1" class="rounded-xl overflow-hidden cursor-pointer shadow-lg"
          @click="openGallery(0)">
          <img :src="images[0]" class="w-full h-[400px] md:h-[500px] lg:h-[700px] object-cover" />
        </div>

        <div v-else-if="images.length === 2" class="grid grid-cols-2 gap-5">
          <div v-for="(img, i) in images" :key="i" class="rounded-xl overflow-hidden cursor-pointer shadow-lg"
            @click="openGallery(i)">
            <img :src="img" class="w-full h-[400px] md:h-[500px] lg:h-[700px] object-cover" />
          </div>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-5">

          <div class="md:col-span-2 rounded-xl overflow-hidden cursor-pointer shadow-lg" @click="openGallery(0)">
            <img :src="images[0]" class="w-full h-[400px] md:h-[500px] lg:h-[700px] object-cover" />
          </div>

          <div class="grid grid-cols-2 md:flex md:flex-col gap-5">

            <div class="rounded-xl overflow-hidden cursor-pointer shadow-lg" @click="openGallery(1)">
              <img :src="images[1]" class="w-full h-[200px] md:h-[240px] lg:h-[340px] object-cover" />
            </div>

            <div class="rounded-xl overflow-hidden relative cursor-pointer shadow-lg" @click="openGallery(2)">
              <img :src="images[2]" class="w-full h-[200px] md:h-[240px] lg:h-[340px] object-cover" />

              <div v-if="images.length > 3"
                class="absolute inset-0 bg-black/60 flex items-center justify-center text-white text-5xl font-bold">
                +{{ images.length - 2 }}
              </div>
            </div>

          </div>

        </div>

      </div>

      <div v-else class="text-text-muted">
        Nincs még kép ehhez a spothoz.
      </div>

      <div v-if="spot.latitude && spot.longitude" class="w-full rounded-xl overflow-hidden mb-10 shadow-lg">
        <iframe class="w-full h-[400px] md:h-[500px] lg:h-[700px]"
          :src="`https://www.google.com/maps?q=${spot.latitude},${spot.longitude}&z=17&output=embed`"
          loading="lazy"></iframe>
      </div>

    </div>
  </BaseLayout>

  <transition enter-active-class="transition duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100"
    leave-active-class="transition duration-200" leave-from-class="opacity-100" leave-to-class="opacity-0">

    <div v-if="galleryOpen" class="fixed inset-0 bg-black/85 flex items-center justify-center z-50"
      @click="closeGallery">

      <button
        class="absolute cursor-pointer top-5 right-5 lg:top-8 lg:right-8 bg-white/25 backdrop-blur-lg p-3 rounded-2xl transition hover:bg-white/40 hover:scale-105 active:scale-95"
        @click.stop="closeGallery">
        <img src="@assets/img/x-white.svg" class="h-6 w-6 lg:h-8 lg:w-8" />
      </button>

      <button
        class="absolute cursor-pointer left-1/2 -translate-x-[150%] bottom-10 lg:bottom-auto lg:left-8 lg:translate-x-0 bg-white/25 backdrop-blur-lg p-3 rounded-2xl transition hover:bg-white/40 hover:scale-105 active:scale-95"
        @click.stop="prevImage">
        <img src="@assets/img/left-arrow-white.svg" class="h-6 w-6 lg:h-8 lg:w-8" />
      </button>

      <img :src="images[activeImage]" class="max-h-[90vh] max-w-[90vw] object-contain" @click.stop />

      <button
        class="absolute cursor-pointer left-1/2 translate-x-[50%] bottom-10 lg:bottom-auto lg:left-auto lg:right-8 lg:translate-x-0 bg-white/25 backdrop-blur-lg p-3 rounded-2xl transition hover:bg-white/40 hover:scale-105 active:scale-95"
        @click.stop="nextImage">
        <img src="@assets/img/right-arrow-white.svg" class="h-6 w-6 lg:h-8 lg:w-8" />
      </button>

    </div>

  </transition>
</template>

<route lang="yaml">
name: spot-megjelenites
meta:
  title: Spot
</route>