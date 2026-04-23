import { createRouter, createWebHistory } from 'vue-router'
import { setTitle } from '@/router/guards/SetTitleGuard.mjs'
import { authGuard } from '@/router/guards/AuthGuard.js'
import { routes } from 'vue-router/auto-routes'

export const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach(authGuard)
router.beforeEach(setTitle)