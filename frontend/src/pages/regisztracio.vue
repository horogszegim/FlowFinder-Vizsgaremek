<script setup>
import BaseLayout from '@layouts/BaseLayout.vue';
import { useAuthStore } from '@/stores/AuthStore.js';
import { useRouter } from 'vue-router';

const auth = useAuthStore();
const router = useRouter();

async function submitForm(data) {
    await auth.register(data);
    router.push('/');
}

function minLength(node, min) {
    const value = node.value || '';
    return value.length >= min;
}

function maxLength(node, max) {
    const value = node.value || '';
    return value.length <= max;
}

/* Username szabályok
    - 3 és 24 karakter közötti hosszúság
    - Az első karakter mindig egy kisbetű (a-z)
    - Többi részen megengedett karakterek: kisbetű (a-z), szám (0-9) és aláhúzás (_)
    - Nem lehet csak szám vagy csak aláhúzás (az első betű miatt ez eleve kizárt)
    - Csak betű lehet (pl. "adam" oké) 
*/

function usernameStart(node) {
    const value = node.value || '';
    if (!value) return true;
    return /^[a-z]/.test(value);
}

function usernameChars(node) {
    const value = node.value || '';
    if (!value) return true;
    return /^[a-z0-9_]+$/.test(value);
}

function noUppercase(node) {
    return !/[A-Z]/.test(node.value || '');
}


/* Jelszó szabályok:
    - 8 és 255 karakter közötti hosszúság
    - Tartalmaznia kell legalább egy kisbetűt
    - Tartalmaznia kell legalább egy nagybetűt
    - Tartalmaznia kell legalább egy számot
    - Tartalmaznia kell legalább egy speciális karaktert: !, @, #, $, %, &, _
    - Más speciális karakter nem megengedett
*/

function hasLowercase(node) {
    return /[a-z]/.test(node.value || '');
}

function hasUppercase(node) {
    return /[A-Z]/.test(node.value || '');
}

function hasNumber(node) {
    return /[0-9]/.test(node.value || '');
}

function hasSpecialChar(node) {
    return /[!@#$%&_]/.test(node.value || '');
}

function onlyAllowedChars(node) {
    return /^[A-Za-z0-9!@#$%&_]*$/.test(node.value || '');
}

function passwordMatch(node) {
    const value = String(node.value ?? '');
    const formValue = node?.parent?.value ?? {};
    return value === String(formValue.password ?? '');
}
</script>

<template>
    <BaseLayout>
        <div class="w-full flex justify-center items-center my-10 -translate-y-3">
            <div class="w-full max-w-2xl">
                <div class="flex items-center justify-center gap-3 -translate-x-2 mb-15">
                    <img src="@assets/img/dark-logo.svg" alt="logo" class="h-16 w-auto" />
                    <span class="text-5xl font-bold text-text">FlowFinder</span>
                </div>

                <h1 class="text-4xl font-bold text-text mb-2">Üdv!</h1>
                <p class="text-xl text-text-muted mb-10">
                    Regisztrálj egy fiókot a folytatáshoz.
                </p>

                <FormKit type="form" :actions="false" @submit="submitForm">
                    <FormKit type="text" name="username" label="Felhasználónév"
                        placeholder="Add meg a felhasználónevedet ..."
                        validation="required|minLength:3|maxLength:24|usernameStart|usernameChars|noUppercase"
                        validation-visibility="dirty"
                        :validation-rules="{ minLength, maxLength, usernameStart, usernameChars, noUppercase }"
                        :validation-messages="{
                            required: 'A felhasználónév megadása kötelező!',
                            minLength: 'A felhasználónévnek legalább 3 karakter hosszúnak kell lennie.',
                            maxLength: 'A felhasználónév legfeljebb 24 karakter hosszú lehet.',
                            usernameStart: 'A felhasználónév nem kezdődhet számmal, aláhúzással vagy egyéb nem engedélyezett karakterrel.',
                            usernameChars: 'A felhasználónév csak kisbetűket (angol ABC), számokat és aláhúzást tartalmazhat.',
                            noUppercase: 'A felhasználónév nem tartalmazhat nagybetűt.',
                        }" />

                    <FormKit type="email" name="email" label="Email" placeholder="Add meg az email címedet ..."
                        validation="required|email|length:1,255" validation-visibility="dirty" :validation-messages="{
                            required: 'Az email megadása kötelező!',
                            email: 'Érvénytelen email formátum.',
                            length: 'Az email cím túl hosszú (maximum 255 karakter).',
                        }" />

                    <FormKit type="password" name="password" label="Jelszó" placeholder="Add meg a jelszavadat ..."
                        validation="required|minLength:8|maxLength:255|hasLowercase|hasUppercase|hasNumber|hasSpecialChar|onlyAllowedChars"
                        validation-visibility="dirty"
                        :validation-rules="{ minLength, maxLength, hasLowercase, hasUppercase, hasNumber, hasSpecialChar, onlyAllowedChars }"
                        :validation-messages="{
                            required: 'A jelszó megadása kötelező!',
                            minLength: 'A jelszónak legalább 8 karakter hosszúnak kell lennie.',
                            maxLength: 'A jelszó legfeljebb 255 karakter hosszú lehet.',
                            hasLowercase: 'A jelszónak tartalmaznia kell legalább egy kisbetűt.',
                            hasUppercase: 'A jelszónak tartalmaznia kell legalább egy nagybetűt.',
                            hasNumber: 'A jelszónak tartalmaznia kell legalább egy számot.',
                            hasSpecialChar: 'A jelszónak tartalmaznia kell legalább egy speciális karaktert: !, @, #, $, %, &, _',
                            onlyAllowedChars: 'A jelszó nem megengedett karaktert tartalmaz. Csak ezek használhatók: !, @, #, $, %, &, _',
                        }" />

                    <FormKit type="password" name="password_confirmation" label="Jelszó megerősítése"
                        placeholder="Erősítsd meg a jelszavadat ..." validation="required|passwordMatch"
                        validation-visibility="dirty" :validation-rules="{ passwordMatch }" :validation-messages="{
                            required: 'A jelszó megerősítése kötelező!',
                            passwordMatch: 'A két jelszó nem egyezik.',
                        }" />

                    <button type="submit"
                        class="mt-5 w-full text-xl font-semibold py-3 bg-primary-dark text-white rounded-xl shadow-lg cursor-pointer
                        transition-all duration-200 ease-out hover:brightness-120 hover:-translate-y-1 active:translate-y-0
                        focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-primary-dark focus-visible:ring-offset-3">
                        Regisztráció
                    </button>
                </FormKit>

                <div class="text-lg text-center mt-5">
                    <span class="text-text-muted">Már van fiókod?</span>
                    <RouterLink to="/bejelentkezes" class="relative inline-block font-medium ml-1 link-hover">
                        Itt bejelentkezhetsz!
                    </RouterLink>
                </div>
            </div>
        </div>
    </BaseLayout>
</template>

<route lang="yaml">
name: regisztracio
meta:
  title: Regisztráció
</route>