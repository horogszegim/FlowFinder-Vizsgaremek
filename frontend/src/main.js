import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { router } from '@/router/index.js';
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate';
import { plugin, defaultConfig } from '@formkit/vue';

import App from '@/App.vue';
import '@assets/main.css';

createApp(App)
  .use(createPinia().use(piniaPluginPersistedstate))
  .use(router)
  .use(
    plugin,
    defaultConfig({
      config: {
        classes: {
          form: 'flex flex-col gap-5',
          wrapper: 'flex flex-col gap-1',
          label: 'text-lg font-semibold text-text',
          input: `
            mt-1 shadow-lg
            w-full bg-background
            border border-text-muted
            rounded-lg px-4 py-3
            text-lg text-text-muted
            outline-none transition
            focus:border-primary-dark
            focus:ring-2 focus:ring-primary-dark
          `,
          wrapper: 'my-2',
          messages: 'text-sm text-red-600 mt-1',
          message: 'block'
        }
      }
    })
  )
  .mount('#app');