<script setup>
import BaseLayout from '@layouts/BaseLayout.vue';
import BaseSpotBlock from '@components/BaseSpotBlock.vue';
import { useAuthStore } from '@stores/AuthStore.js';
import { useSpotStore } from '@stores/SpotStore.js';
import { useToastStore } from '@stores/ToastStore.js';
import { useRouter } from 'vue-router';
import { computed, onMounted, ref, watch } from 'vue';

const authStore = useAuthStore();
const spotStore = useSpotStore();
const toast = useToastStore();
const router = useRouter();

const currentPage = ref(1);
const pageSize = 100;
const deleteLoadingId = ref(null);
const profileLoading = ref(true);

const displayName = computed(() => {
    return authStore.user?.username || 'Felhasználó';
});

const isAdmin = computed(() => {
    return authStore.isAdmin;
});

const spots = computed(() => {
    return spotStore.spots;
});

const totalPages = computed(() => {
    return Math.max(1, Math.ceil(spots.value.length / pageSize));
});

const paginatedSpots = computed(() => {
    const start = (currentPage.value - 1) * pageSize;
    return spots.value.slice(start, start + pageSize);
});

const visiblePages = computed(() => {
    const pages = [];
    const start = Math.max(1, currentPage.value - 2);
    const end = Math.min(totalPages.value, currentPage.value + 2);

    for (let page = start; page <= end; page++) {
        pages.push(page);
    }

    return pages;
});

onMounted(async () => {
    try {
        await authStore.fetchUser();

        if (isAdmin.value) {
            await spotStore.getSpots();
        }
    } catch {
        await authStore.logout();
        toast.trigger('A munkamenet lejárt, jelentkezz be újra!', 'error');
        router.push('/bejelentkezes');
    } finally {
        profileLoading.value = false;
    }
});

watch(totalPages, (value) => {
    if (currentPage.value > value) {
        currentPage.value = value;
    }
});

function goToPage(page) {
    if (page < 1 || page > totalPages.value) return;

    currentPage.value = page;

    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

async function logout() {
    await authStore.logout();
    toast.trigger('Sikeresen kijelentkeztél!');
    router.push('/bejelentkezes');
}

async function deleteSpot(spot) {
    const confirmed = window.confirm(`Biztos törölni szeretnéd ezt a spotot: "${spot.title}"?`);

    if (!confirmed) return;

    deleteLoadingId.value = spot.id;

    try {
        await spotStore.deleteSpot(spot.id);

        if (currentPage.value > totalPages.value) {
            currentPage.value = totalPages.value;
        }

        toast.trigger('Spot sikeresen törölve!');
    } catch (error) {
        if (error.response?.status === 403) {
            toast.trigger('Nincs jogosultságod a spot törléséhez!', 'error');
            return;
        }

        toast.trigger('Hiba történt a spot törlése közben!', 'error');
    } finally {
        deleteLoadingId.value = null;
    }
}
</script>

<template>
    <BaseLayout>
        <div class="w-full flex justify-center my-10">
            <div class="w-full max-w-5xl flex flex-col gap-8">
                <section class="bg-background border border-text-muted rounded-xl shadow-lg p-6 md:p-8">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                        <div class="min-w-0">
                            <p class="text-primary-dark font-semibold mb-2">
                                Profil
                            </p>

                            <div class="flex flex-wrap items-center gap-3">
                                <h1 class="text-4xl font-bold text-text break-all">
                                    {{ displayName }}
                                </h1>

                                <span v-if="isAdmin"
                                    class="inline-flex px-3 py-1 rounded-xl bg-primary-dark text-white text-sm font-semibold">
                                    Admin
                                </span>
                            </div>

                            <p v-if="authStore.user?.email" class="text-text-muted text-lg mt-2 break-all">
                                {{ authStore.user.email }}
                            </p>
                        </div>

                        <button type="button"
                            class="px-7 py-3 flex items-center justify-center transition font-semibold rounded-xl shadow-lg bg-primary-dark text-white hover:brightness-120 hover:-translate-y-1 cursor-pointer"
                            @click="logout">
                            Kijelentkezés
                        </button>
                    </div>
                </section>

                <section v-if="isAdmin" class="flex flex-col gap-5">
                    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-3">
                        <div>
                            <h2 class="text-3xl font-bold text-text">
                                Összes spot kezelése
                            </h2>

                            <p class="text-text-muted text-lg">
                                Admin jogosultsággal az összes feltöltött spotot látod, és törölheted őket.
                            </p>
                        </div>
                    </div>

                    <div v-if="spotStore.loading" class="flex flex-col items-center justify-center py-15 gap-5">
                        <div
                            class="w-12 h-12 border-5 border-text-muted border-t-primary-dark rounded-full animate-spin">
                        </div>

                        <p class="text-lg text-text-muted font-semibold">
                            Spotok betöltése ...
                        </p>
                    </div>

                    <div v-else-if="!spots.length"
                        class="bg-background border border-text-muted rounded-xl shadow-lg p-6 text-center text-text-muted">
                        Nincs még feltöltött spot.
                    </div>

                    <div v-else class="flex flex-col gap-5">
                        <div v-for="spot in paginatedSpots" :key="spot.id" class="flex items-center gap-5">
                            <BaseSpotBlock :spot="spot" class="flex-1 min-w-0" />

                            <button type="button" :disabled="deleteLoadingId === spot.id"
                                class="shrink-0 bg-red-700/50 backdrop-blur-lg p-2 md:p-3 rounded-xl transition hover:bg-red-700/80 hover:scale-105 active:scale-95 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                                @click.stop="deleteSpot(spot)">
                                <img src="@assets/img/trash-white.svg" alt="Törlés" class="h-5 w-5 md:h-8 md:w-8" />
                            </button>
                        </div>

                        <div v-if="spots.length > pageSize"
                            class="flex flex-wrap justify-center items-center gap-3 mt-3 mb-5">
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

                            <button type="button" @click="goToPage(currentPage + 1)"
                                :disabled="currentPage === totalPages"
                                class="shrink-0 w-12 h-12 rounded-xl border border-text-muted bg-background shadow-md hover:brightness-95 transition cursor-pointer flex items-center justify-center disabled:opacity-40 disabled:cursor-default">
                                <img src="@assets/img/right-arrow-white.svg" class="w-5 h-5 invert" />
                            </button>
                        </div>
                    </div>
                </section>

                <section v-else-if="!profileLoading"
                    class="bg-background border border-text-muted rounded-xl shadow-lg p-5 text-text-muted text-lg">
                    Ez egy sima felhasználói profil (nem admin).
                </section>
            </div>
        </div>
    </BaseLayout>
</template>

<route lang="yaml">
name: profil
meta:
  title: Profil
  requiresAuth: true
</route>