<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';

const isOpen = ref(false);
const toggleMenu = () => isOpen.value = !isOpen.value;
const closeMenu = () => isOpen.value = false;

const showNav = ref(true);
let lastScrollY = 0;

const handleScroll = () => {
  const current = window.scrollY;

  if (isOpen.value) {
    showNav.value = true;
    lastScrollY = current;
    return
  }

  if (current > lastScrollY && current > 50) showNav.value = false;
  else showNav.value = true;

  lastScrollY = current;
}

onMounted(() => window.addEventListener('scroll', handleScroll, { passive: true }));
onBeforeUnmount(() => window.removeEventListener('scroll', handleScroll));
</script>

<template>
  <nav
    class="w-full h-20 fixed top-0 left-0 z-50 px-5 md:px-15 xl:px-30 border-b border-text-muted bg-background transition-transform duration-300 shadow-lg"
    :class="isOpen ? 'translate-y-0' : (showNav ? 'translate-y-0' : '-translate-y-full')">
    <div class="h-full flex items-center justify-between">
      <RouterLink to="/" class="flex items-center gap-3 -translate-x-2">
        <img src="../../assets/img/dark-logo.svg" alt="logo" class="h-12 w-auto" />
        <span class="text-3xl font-bold text-text">
          FlowFinder
        </span>
      </RouterLink>

      <button class="max-[950px]:block hidden z-60 cursor-pointer" @click="toggleMenu">
        <img src="../../assets/img/hamburger.svg" alt="menu" class="h-7" />
      </button>

      <ul class="flex items-center text-2xl gap-10 text-text-muted font-medium max-[950px]:hidden">
        <li>
          <RouterLink to="/" class="relative inline-block link-hover">Kezdőlap</RouterLink>
        </li>
        <li>
          <RouterLink to="/spotkereso" class="relative inline-block link-hover">Spotkereső</RouterLink>
        </li>
        <li>
          <RouterLink to="/feltoltes" class="relative inline-block link-hover">Feltöltés</RouterLink>
        </li>
        <li>
          <RouterLink to="/profil" class="relative inline-block">
            <img src="../../assets/img/profile-icon.png" alt="profil" class="h-10 w-auto pt-1.5" />
          </RouterLink>
        </li>
      </ul>
    </div>
  </nav>

  <div class="fixed inset-0 bg-black/50 transition-opacity duration-500 z-55"
    :class="isOpen ? 'opacity-100 pointer-events-auto cursor-pointer' : 'opacity-0 pointer-events-none'"
    @click="closeMenu"></div>

  <aside class="fixed top-0 right-0 h-full w-90 border-l border-text-muted bg-background shadow-lg
               transition-transform duration-300 z-60" :class="isOpen ? 'translate-x-0' : 'translate-x-full'">
    <div class="h-20 flex items-center justify-between px-5 border-b border-text-muted bg-background-light shadow-lg">
      <RouterLink to="/profil" class="flex items-center gap-3">
        <img src="../../assets/img/profile-icon.png" alt="profil" class="h-9 w-auto" />
      </RouterLink>

      <button @click="closeMenu" class="cursor-pointer">
        <img src="../../assets/img/x.svg" alt="close" class="h-8" />
      </button>
    </div>

    <ul class="flex flex-col gap-7 px-7 py-5 text-2xl">
      <li>
        <RouterLink to="/" class="relative inline-block link-hover">Kezdőlap</RouterLink>
      </li>
      <li>
        <RouterLink to="/spotkereso" class="relative inline-block link-hover">Spotkereső</RouterLink>
      </li>
      <li>
        <RouterLink to="/feltoltes" class="relative inline-block link-hover">Feltöltés</RouterLink>
      </li>
    </ul>
  </aside>
</template>