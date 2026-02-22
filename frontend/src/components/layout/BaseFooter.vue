<script setup>
</script>

<template>
    <footer class="w-full border-t border-text-muted">
        <div class="bg-background mx-auto px-5 md:px-15 xl:px-30 py-8 gap-12 shadow-lg-up
                   grid grid-cols-1 md:grid-cols-3 items-start text-left">

            <div class="flex flex-col gap-3 order-2 md:order-1 md:items-start">
                <h3 class="text-xl font-semibold text-text">Navigáció</h3>

                <ul class="flex flex-col gap-1.5 text-lg text-text-muted font-medium">
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
            </div>

            <div
                class="flex flex-col gap-3 order-1 md:order-2 items-start md:items-center md:text-center md:self-center">
                <RouterLink to="/" class="flex items-center gap-1.5">
                    <img src="../../assets/img/dark-logo.svg" alt="logo" class="h-9 w-auto" />
                    <span class="text-2xl font-bold text-text">FlowFinder</span>
                </RouterLink>

                <p class="text-text-muted text-xl font-medium pl-2">Find. Share. Ride.</p>
            </div>

            <div class="flex flex-col gap-3 order-3 md:items-end md:text-right">
                <h3 class="text-xl font-semibold text-text">Kapcsolat</h3>

                <ul class="flex flex-col gap-1.5 text-lg text-text-muted font-medium">
                    <li>
                        <a href="mailto:info@flowfinder.hu" target="_blank" class="relative inline-block link-hover">
                            info@flowfinder.hu
                        </a>
                    </li>
                    <li>
                        <a href="#" target="_blank" class="relative inline-block link-hover">Instagram</a>
                    </li>
                    <li>
                        <a href="#" target="_blank" class="relative inline-block link-hover">Facebook</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-t border-text-muted bg-background-light shadow-lg-up">
            <div class="mx-auto px-5 md:px-15 xl:px-30 py-5 text-center text-text-muted text-sm sm:text-base">
                2026 © <span class="font-medium text-text">FlowFinder</span> |
                Balla Ádám Levente, Horogszegi Miklós Dávid, Király Márton
            </div>
        </div>
    </footer>
</template>