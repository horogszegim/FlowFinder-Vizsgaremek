<script setup>
import BaseLayout from '@layouts/BaseLayout.vue';
import BaseSpotBlock from '@components/BaseSpotBlock.vue';
import { useAuthStore } from '@stores/AuthStore.js';
import { useSpotStore } from '@stores/SpotStore.js';
import { useSavedSpotStore } from '@stores/SavedSpotStore.js';
import { useToastStore } from '@stores/ToastStore.js';
import { useRouter } from 'vue-router';
import { computed, onMounted, ref, watch } from 'vue';

const authStore = useAuthStore();
const spotStore = useSpotStore();
const savedSpotStore = useSavedSpotStore();
const toast = useToastStore();
const router = useRouter();

const pageSize = 100;
const savedPage = ref(1);
const uploadedPage = ref(1);
const otherPage = ref(1);
const deleteLoadingId = ref(null);
const saveLoadingId = ref(null);
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

const savedSpotItems = computed(() => {
    return savedSpotStore.savedSpots
        .map(savedSpot => savedSpot.spot)
        .filter(Boolean);
});

const uploadedSpots = computed(() => {
    return spots.value.filter(spot => spot.created_by?.id === authStore.user?.id);
});

const otherSpots = computed(() => {
    return spots.value.filter(spot => spot.created_by?.id !== authStore.user?.id);
});

const savedTotalPages = computed(() => {
    return getTotalPages(savedSpotItems.value.length);
});

const uploadedTotalPages = computed(() => {
    return getTotalPages(uploadedSpots.value.length);
});

const otherTotalPages = computed(() => {
    return getTotalPages(otherSpots.value.length);
});

const paginatedSavedSpots = computed(() => {
    return paginate(savedSpotItems.value, savedPage.value);
});

const paginatedUploadedSpots = computed(() => {
    return paginate(uploadedSpots.value, uploadedPage.value);
});

const paginatedOtherSpots = computed(() => {
    return paginate(otherSpots.value, otherPage.value);
});

const savedVisiblePages = computed(() => {
    return getVisiblePages(savedPage.value, savedTotalPages.value);
});

const uploadedVisiblePages = computed(() => {
    return getVisiblePages(uploadedPage.value, uploadedTotalPages.value);
});

const otherVisiblePages = computed(() => {
    return getVisiblePages(otherPage.value, otherTotalPages.value);
});

onMounted(async () => {
    try {
        await authStore.fetchUser();

        await Promise.all([
            spotStore.getSpots(),
            savedSpotStore.getSavedSpots()
        ]);
    } catch {
        await authStore.logout();
        toast.trigger('A munkamenet lejárt, jelentkezz be újra!', 'error');
        router.push('/bejelentkezes');
    } finally {
        profileLoading.value = false;
    }
});

watch(savedTotalPages, (value) => {
    if (savedPage.value > value) {
        savedPage.value = value;
    }
});

watch(uploadedTotalPages, (value) => {
    if (uploadedPage.value > value) {
        uploadedPage.value = value;
    }
});

watch(otherTotalPages, (value) => {
    if (otherPage.value > value) {
        otherPage.value = value;
    }
});

function getTotalPages(length) {
    return Math.max(1, Math.ceil(length / pageSize));
}

function paginate(items, page) {
    const start = (page - 1) * pageSize;
    return items.slice(start, start + pageSize);
}

function getVisiblePages(current, total) {
    const pages = [];
    const start = Math.max(1, current - 2);
    const end = Math.min(total, current + 2);

    for (let page = start; page <= end; page++) {
        pages.push(page);
    }

    return pages;
}

function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

function goToSavedPage(page) {
    if (page < 1 || page > savedTotalPages.value) return;

    savedPage.value = page;
    scrollToTop();
}

function goToUploadedPage(page) {
    if (page < 1 || page > uploadedTotalPages.value) return;

    uploadedPage.value = page;
    scrollToTop();
}

function goToOtherPage(page) {
    if (page < 1 || page > otherTotalPages.value) return;

    otherPage.value = page;
    scrollToTop();
}

function editSpot(spot) {
    router.push({
        name: 'spot-szerkesztes',
        params: {
            id: spot.id
        }
    });
}

async function logout() {
    await authStore.logout();
    toast.trigger('Sikeresen kijelentkeztél!');
    router.push('/bejelentkezes');
}

async function toggleSavedSpot(spot) {
    if (saveLoadingId.value === spot.id) return;

    const savedSpotId = savedSpotStore.findSavedSpotId(spot.id);

    if (!savedSpotId) {
        return;
    }

    saveLoadingId.value = spot.id;

    try {
        await savedSpotStore.deleteSavedSpot(savedSpotId);
        toast.trigger('Spot eltávolítva a mentettek közül!');
    } catch {
        toast.trigger('Hiba történt a mentés módosítása közben!', 'error');
    } finally {
        saveLoadingId.value = null;
    }
}

async function deleteSpot(spot) {
    const confirmed = window.confirm(`Biztos törölni szeretnéd ezt a spotot: "${spot.title}"?`);

    if (!confirmed) return;

    deleteLoadingId.value = spot.id;

    try {
        await spotStore.deleteSpot(spot.id);
        savedSpotStore.removeBySpotId(spot.id);
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
        <div class="w-full flex justify-center mt-5 mb-10">
            <div class="w-full max-w-7xl flex flex-col gap-10">
                <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-5">
                    <div class="min-w-0">
                        <p class="text-text-muted text-lg font-semibold mb-3">
                            Saját profil
                        </p>

                        <div class="flex flex-wrap items-center gap-5">
                            <h1 class="text-5xl font-bold text-text break-all">
                                {{ displayName }}
                            </h1>

                            <span v-if="isAdmin"
                                class="inline-flex px-3 py-2 rounded-xl bg-primary-dark text-white text-sm font-semibold translate-y-1 shadow-lg">
                                Admin
                            </span>
                        </div>

                        <p v-if="authStore.user?.email" class="text-text-muted text-lg break-all">
                            {{ authStore.user.email }}
                        </p>
                    </div>

                    <button type="button"
                        class="px-7 py-3 flex items-center justify-center transition font-semibold rounded-xl shadow-lg bg-primary-dark text-white hover:brightness-120 hover:-translate-y-1 cursor-pointer"
                        @click="logout">
                        Kijelentkezés
                    </button>
                </section>

                <div v-if="profileLoading || spotStore.loading"
                    class="flex flex-col items-center justify-center py-15 gap-5">
                    <div class="w-12 h-12 border-5 border-text-muted border-t-primary-dark rounded-full animate-spin">
                    </div>

                    <p class="text-lg text-text-muted font-semibold">
                        Profil betöltése ...
                    </p>
                </div>

                <template v-else>
                    <section class="flex flex-col gap-5">
                        <div>
                            <h2 class="text-3xl font-bold text-text">
                                Elmentett spotjaid
                            </h2>

                            <p class="text-text-muted text-lg">
                                Itt látod azokat a spotokat, amelyeket könyvjelzővel elmentettél.
                            </p>
                        </div>

                        <div v-if="!savedSpotItems.length"
                            class="bg-background border border-text-muted rounded-xl shadow-lg p-5 text-center text-text-muted">
                            Még nem mentettél el spotot.
                        </div>

                        <div v-else class="flex flex-col gap-5">
                            <div v-for="spot in paginatedSavedSpots" :key="spot.id" class="flex items-stretch gap-5">
                                <BaseSpotBlock :spot="spot" class="flex-1 min-w-0" />

                                <button type="button" :disabled="saveLoadingId === spot.id"
                                    class="shrink-0 self-center w-12 h-12 rounded-xl border border-text-muted bg-background shadow-lg hover:brightness-95 transition cursor-pointer flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed"
                                    @click.stop="toggleSavedSpot(spot)">
                                    <img src="@assets/img/bookmark-filled.svg" alt="Mentés törlése"
                                        class="w-5 h-5 block" />
                                </button>
                            </div>

                            <div v-if="savedSpotItems.length > pageSize"
                                class="flex flex-wrap justify-center items-center gap-3 mt-3 mb-5">
                                <button type="button" @click="goToSavedPage(savedPage - 1)" :disabled="savedPage === 1"
                                    class="shrink-0 w-12 h-12 rounded-xl border border-text-muted bg-background shadow-lg hover:brightness-95 transition cursor-pointer flex items-center justify-center disabled:opacity-40 disabled:cursor-default">
                                    <img src="@assets/img/left-arrow-white.svg" class="w-5 h-5 invert block" />
                                </button>

                                <button v-for="page in savedVisiblePages" :key="page" @click="goToSavedPage(page)"
                                    class="min-w-12 h-12 px-4 rounded-xl border bg-background shadow-lg hover:brightness-95 transition cursor-pointer font-semibold flex items-center justify-center"
                                    :class="page === savedPage ? 'bg-primary-dark text-white border-primary-dark' : 'border-text-muted'">
                                    {{ page }}
                                </button>

                                <button type="button" @click="goToSavedPage(savedPage + 1)"
                                    :disabled="savedPage === savedTotalPages"
                                    class="shrink-0 w-12 h-12 rounded-xl border border-text-muted bg-background shadow-lg hover:brightness-95 transition cursor-pointer flex items-center justify-center disabled:opacity-40 disabled:cursor-default">
                                    <img src="@assets/img/right-arrow-white.svg" class="w-5 h-5 invert block" />
                                </button>
                            </div>
                        </div>
                    </section>

                    <section class="flex flex-col gap-5">
                        <div>
                            <h2 class="text-3xl font-bold text-text">
                                Általad feltöltött spotok
                            </h2>

                            <p class="text-text-muted text-lg">
                                Ezeket a spotokat te töltötted fel, ezért szerkesztheted vagy törölheted őket.
                            </p>
                        </div>

                        <div v-if="!uploadedSpots.length"
                            class="bg-background border border-text-muted rounded-xl shadow-lg p-5 text-center text-text-muted">
                            Még nem töltöttél fel spotot.
                        </div>

                        <div v-else class="flex flex-col gap-5">
                            <div v-for="spot in paginatedUploadedSpots" :key="spot.id" class="flex items-stretch gap-5">
                                <BaseSpotBlock :spot="spot" class="flex-1 min-w-0" />

                                <div class="shrink-0 flex flex-col gap-3 justify-center">
                                    <button type="button"
                                        class="w-12 h-12 rounded-xl border border-text-muted bg-background shadow-lg hover:brightness-95 transition cursor-pointer flex items-center justify-center"
                                        @click.stop="editSpot(spot)">
                                        <img src="@assets/img/edit-black.svg" alt="Szerkesztés" class="w-5 h-5 block" />
                                    </button>

                                    <button type="button" :disabled="deleteLoadingId === spot.id"
                                        class="w-12 h-12 rounded-xl bg-red-700/50 shadow-lg hover:bg-red-700/80 transition cursor-pointer flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed"
                                        @click.stop="deleteSpot(spot)">
                                        <img src="@assets/img/trash-white.svg" alt="Törlés" class="w-5 h-5 block" />
                                    </button>
                                </div>
                            </div>

                            <div v-if="uploadedSpots.length > pageSize"
                                class="flex flex-wrap justify-center items-center gap-3 mt-3 mb-5">
                                <button type="button" @click="goToUploadedPage(uploadedPage - 1)"
                                    :disabled="uploadedPage === 1"
                                    class="shrink-0 w-12 h-12 rounded-xl border border-text-muted bg-background shadow-lg hover:brightness-95 transition cursor-pointer flex items-center justify-center disabled:opacity-40 disabled:cursor-default">
                                    <img src="@assets/img/left-arrow-white.svg" class="w-5 h-5 invert block" />
                                </button>

                                <button v-for="page in uploadedVisiblePages" :key="page" @click="goToUploadedPage(page)"
                                    class="min-w-12 h-12 px-4 rounded-xl border bg-background shadow-lg hover:brightness-95 transition cursor-pointer font-semibold flex items-center justify-center"
                                    :class="page === uploadedPage ? 'bg-primary-dark text-white border-primary-dark' : 'border-text-muted'">
                                    {{ page }}
                                </button>

                                <button type="button" @click="goToUploadedPage(uploadedPage + 1)"
                                    :disabled="uploadedPage === uploadedTotalPages"
                                    class="shrink-0 w-12 h-12 rounded-xl border border-text-muted bg-background shadow-lg hover:brightness-95 transition cursor-pointer flex items-center justify-center disabled:opacity-40 disabled:cursor-default">
                                    <img src="@assets/img/right-arrow-white.svg" class="w-5 h-5 invert block" />
                                </button>
                            </div>
                        </div>
                    </section>

                    <section v-if="isAdmin" class="flex flex-col gap-5">
                        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-3">
                            <div>
                                <h2 class="text-3xl font-bold text-text">
                                    Összes egyéb spot kezelése
                                </h2>

                                <p class="text-text-muted text-lg">
                                    Itt látod az összes többi spotot, vagyis azokat, amelyeket nem te töltöttél fel, de
                                    admin jogosultságod miatt te is kezelheted őket.
                                </p>
                            </div>
                        </div>

                        <div v-if="!otherSpots.length"
                            class="bg-background border border-text-muted rounded-xl shadow-lg p-5 text-center text-text-muted">
                            Nincs még egyéb feltöltött spot.
                        </div>

                        <div v-else class="flex flex-col gap-5">
                            <div v-for="spot in paginatedOtherSpots" :key="spot.id" class="flex items-stretch gap-5">
                                <BaseSpotBlock :spot="spot" class="flex-1 min-w-0" />

                                <div class="shrink-0 flex flex-col gap-3 justify-center">
                                    <button type="button"
                                        class="w-12 h-12 rounded-xl border border-text-muted bg-background shadow-lg hover:brightness-95 transition cursor-pointer flex items-center justify-center"
                                        @click.stop="editSpot(spot)">
                                        <img src="@assets/img/edit-black.svg" alt="Szerkesztés" class="w-5 h-5 block" />
                                    </button>

                                    <button type="button" :disabled="deleteLoadingId === spot.id"
                                        class="w-12 h-12 rounded-xl bg-red-700/50 shadow-lg hover:bg-red-700/80 transition cursor-pointer flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed"
                                        @click.stop="deleteSpot(spot)">
                                        <img src="@assets/img/trash-white.svg" alt="Törlés" class="w-5 h-5 block" />
                                    </button>
                                </div>
                            </div>

                            <div v-if="otherSpots.length > pageSize"
                                class="flex flex-wrap justify-center items-center gap-3 mt-3 mb-5">
                                <button type="button" @click="goToOtherPage(otherPage - 1)" :disabled="otherPage === 1"
                                    class="shrink-0 w-12 h-12 rounded-xl border border-text-muted bg-background shadow-lg hover:brightness-95 transition cursor-pointer flex items-center justify-center disabled:opacity-40 disabled:cursor-default">
                                    <img src="@assets/img/left-arrow-white.svg" class="w-5 h-5 invert block" />
                                </button>

                                <button v-for="page in otherVisiblePages" :key="page" @click="goToOtherPage(page)"
                                    class="min-w-12 h-12 px-4 rounded-xl border bg-background shadow-lg hover:brightness-95 transition cursor-pointer font-semibold flex items-center justify-center"
                                    :class="page === otherPage ? 'bg-primary-dark text-white border-primary-dark' : 'border-text-muted'">
                                    {{ page }}
                                </button>

                                <button type="button" @click="goToOtherPage(otherPage + 1)"
                                    :disabled="otherPage === otherTotalPages"
                                    class="shrink-0 w-12 h-12 rounded-xl border border-text-muted bg-background shadow-lg hover:brightness-95 transition cursor-pointer flex items-center justify-center disabled:opacity-40 disabled:cursor-default">
                                    <img src="@assets/img/right-arrow-white.svg" class="w-5 h-5 invert block" />
                                </button>
                            </div>
                        </div>
                    </section>
                </template>
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