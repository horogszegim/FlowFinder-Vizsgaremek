<script setup>
import BaseLayout from '@layouts/BaseLayout.vue';
import { useRoute, useRouter, RouterView } from 'vue-router';
import { useSpotStore } from '@stores/SpotStore';
import { useSavedSpotStore } from '@stores/SavedSpotStore';
import { useAuthStore } from '@stores/AuthStore';
import { useToastStore } from '@/stores/ToastStore';
import { ref, onMounted, computed, nextTick } from 'vue';
import { getTagStyle } from '@utils/tagColors';

const route = useRoute();
const router = useRouter();
const spotStore = useSpotStore();
const savedSpotStore = useSavedSpotStore();
const authStore = useAuthStore();
const toast = useToastStore();

const spot = ref({});

const bookmarked = ref(false);

const galleryOpen = ref(false);
const activeImage = ref(0);
const thumbnailRefs = ref([]);

const images = computed(() =>
  spot.value?.images?.map(img => img.url) || []
);

const isOwnSpot = computed(() => {
  return !!authStore.user?.id && spot.value?.created_by?.id === authStore.user.id;
});

onMounted(async () => {
  if (route.name === 'spot-szerkesztes') {
    return;
  }

  try {
    spot.value = await spotStore.getSpot(route.params.id);
  } catch {
    router.replace({ name: 'spotkereso' });
    return;
  }

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
  scrollActiveThumbnailIntoView();
}

function closeGallery() {
  galleryOpen.value = false;
}

function setActiveImage(index) {
  activeImage.value = index;
  scrollActiveThumbnailIntoView();
}

function nextImage() {
  activeImage.value = (activeImage.value + 1) % images.value.length;
  scrollActiveThumbnailIntoView();
}

function prevImage() {
  activeImage.value = (activeImage.value - 1 + images.value.length) % images.value.length;
  scrollActiveThumbnailIntoView();
}

function scrollActiveThumbnailIntoView() {
  nextTick(() => {
    const element = thumbnailRefs.value[activeImage.value];

    if (element) {
      element.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
    }
  });
}
</script>

<template>
  <RouterView v-if="$route.name === 'spot-szerkesztes'" />

  <BaseLayout v-else>
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
        Felfedezte: <span class="text-text">{{ spot.created_by.username }}{{ isOwnSpot ? ' (Te)' : '' }}</span>
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

    <div v-if="galleryOpen"
      class="fixed inset-0 bg-black/85 flex flex-col items-center justify-center gap-5 p-5 lg:p-8 z-50"
      @click="closeGallery">

      <button
        class="absolute cursor-pointer top-5 right-5 lg:top-8 lg:right-8 h-12 w-12 rounded-xl bg-background shadow-lg transition hover:brightness-95 active:scale-95 flex items-center justify-center"
        @click.stop="closeGallery">
        <img src="@assets/img/x-white.svg" class="h-5 w-5 invert" />
      </button>

      <div class="w-full flex-1 min-h-0 flex items-center justify-center">
        <img :src="images[activeImage]" class="max-h-full max-w-[90vw] object-contain" @click.stop />
      </div>

      <div v-if="images.length > 1" class="w-full flex items-center justify-center gap-3" @click.stop>
        <button
          class="cursor-pointer h-12 w-12 shrink-0 rounded-xl bg-background shadow-lg transition hover:brightness-95 active:scale-95 flex items-center justify-center"
          @click="prevImage">
          <img src="@assets/img/left-arrow-white.svg" class="h-5 w-5 invert" />
        </button>

        <div class="max-w-[calc(100vw-8rem)] overflow-x-auto">
          <div class="flex gap-3 min-w-max px-1 py-1">
            <button v-for="(img, i) in images" :key="i" :ref="el => thumbnailRefs[i] = el"
              class="cursor-pointer w-15 h-15 lg:w-20 lg:h-20 shrink-0 rounded-xl overflow-hidden border shadow-lg transition hover:brightness-95 active:scale-95"
              :class="i === activeImage ? 'border-primary-dark ring-2 ring-primary-dark' : 'border-text-muted opacity-70'"
              @click="setActiveImage(i)">
              <img :src="img" class="w-full h-full object-cover" />
            </button>
          </div>
        </div>

        <button
          class="cursor-pointer h-12 w-12 shrink-0 rounded-xl bg-background shadow-lg transition hover:brightness-95 active:scale-95 flex items-center justify-center"
          @click="nextImage">
          <img src="@assets/img/right-arrow-white.svg" class="h-5 w-5 invert" />
        </button>
      </div>

    </div>

  </transition>
</template>

<route lang="yaml">
name: spot-megjelenites
meta:
  title: Spot
</route>