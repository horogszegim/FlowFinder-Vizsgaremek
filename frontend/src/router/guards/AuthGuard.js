import { useAuthStore } from '@/stores/AuthStore';
import { useToastStore } from '@/stores/ToastStore';

export function authGuard(to, from, next) {
    const authStore = useAuthStore();
    const toast = useToastStore();

    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        toast.trigger('Ehhez először be kell jelentkezned!', 'error');
        return next('/bejelentkezes');
    }

    if (to.meta.guestOnly && authStore.isAuthenticated) {
        toast.trigger('Már be vagy jelentkezve!', 'error');
        return next('/profil');
    }

    next();
}