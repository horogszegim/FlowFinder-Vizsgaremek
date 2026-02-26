<script setup>
import BaseLayout from '@layouts/BaseLayout.vue'
import { useAuthStore } from '@/stores/AuthStore.js'
import { useRouter } from 'vue-router'
import { ref } from 'vue'

const auth = useAuthStore()
const router = useRouter()

const loginError = ref('')

async function submitForm(data) {
  loginError.value = ''

  try {
    await auth.login(data)
    router.push('/')
  } catch (error) {
    loginError.value = 'Hibás email cím vagy jelszó, kérlek próbáld újra!'
  }
}
</script>

<template>
  <BaseLayout>
    <div class="w-full flex justify-center items-center my-10 -translate-y-3">
      <div class="w-full max-w-2xl">
        <div class="flex items-center justify-center gap-3 -translate-x-2 mb-15">
          <img src="@assets/img/dark-logo.svg" alt="logo" class="h-16 w-auto" />
          <span class="text-5xl font-bold text-text">
            FlowFinder
          </span>
        </div>

        <h1 class="text-4xl font-bold text-text mb-2">
          Üdv újra!
        </h1>
        <p class="text-xl text-text-muted mb-10">
          Jelentkezz be a fiókodba a folytatáshoz.
        </p>

        <FormKit type="form" :actions="false" @submit="submitForm">
          <FormKit type="email" name="email" label="Email" placeholder="Add meg az email címedet ..."
            validation="required" validation-visibility="dirty" :validation-messages="{
              required: 'Az email megadása kötelező!',
            }" />

          <FormKit type="password" name="password" label="Jelszó" placeholder="Add meg a jelszavadat ..."
            validation="required" validation-visibility="dirty" :validation-messages="{
              required: 'A jelszó megadása kötelező!',
            }" />

          <p v-if="loginError" class="mt-4 text-lg text-red-600 text-center font-medium">
            {{ loginError }}
          </p>

          <button type="submit" class="mt-5 w-full text-xl font-semibold py-3 bg-primary-dark text-white rounded-xl shadow-lg cursor-pointer
            transition-all duration-200 ease-out hover:brightness-120 hover:-translate-y-1 active:translate-y-0
            focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-primary-dark focus-visible:ring-offset-3
          ">
            Bejelentkezés
          </button>
        </FormKit>

        <div class="text-lg text-center mt-5">
          <span class="text-text-muted">
            Még nincs fiókod?
          </span>
          <RouterLink to="/regisztracio" class="relative inline-block font-medium ml-1 link-hover">
            Itt regisztrálhatsz!
          </RouterLink>
        </div>
      </div>
    </div>
  </BaseLayout>
</template>

<route lang="yaml">
name: bejelentkezes
meta:
  title: Bejelentkezés
</route>