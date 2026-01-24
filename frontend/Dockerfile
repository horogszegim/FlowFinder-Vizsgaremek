FROM node:24-alpine3.22

WORKDIR /app

COPY ./package.json /app/package.json

RUN corepack enable pnpm

RUN apk add fish

RUN mkdir -p /root/.config/fish/functions/ &&  printf 'function fish_prompt  \n\
  set -l last_status $status  \n\
    set -l stat  \n\
    if test $last_status -ne 0  \n\
        set stat (set_color red)" [$last_status]"(set_color normal)  \n\
    end  \n\
    string join "" -- (set_color green) "[frontend] " $PWD (set_color normal) $stat " >"  \n\
end' > /root/.config/fish/functions/fish_prompt.fish 

RUN pnpm store add @vitejs/plugin-vue@6.0.1 \
                    pinia@3.0.3 \
                    pinia-plugin-persistedstate@4.4.1 \
                    @formkit/vue@1.6.9 \
                    @formkit/i18n@1.6.9 \
                    vue@3.5.18 \
                    vue-i18n@11.1.11 \
                    vue-router@4.5.1 \
                    unplugin-vue-router@0.14.0 \
                    axios@1.11.0 \
                    vite@7.0.6 \
                    vite-plugin-vue-devtools@8.0.0 \
                    tailwindcss@4.1.11 \
                    @tailwindcss/vite@4.1.11 \
                    shadcn-vue@2.2.0 \
                    class-variance-authority@0.7.1 \
                    clsx@2.1.1 \
                    lucide-vue-next@0.516.0 \
                    tailwind-merge@3.3.1 \
                    tw-animate-css@1.3.6 \
                    @vueuse/core@13.6.0 \
                    reka-ui@2.4.1 \
                    @tanstack/vue-table@8.21.3 
                    
RUN pnpm i

CMD ["pnpm", "dev"]