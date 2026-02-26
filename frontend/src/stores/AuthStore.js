import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { api } from '@utils/http.mjs';

export const useAuthStore = defineStore('auth', () => {
    const token = ref(localStorage.getItem('token'));

    const isAuthenticated = computed(() => {
        return !!token.value;
    });

    function setToken(accessToken) {
        token.value = accessToken;
        localStorage.setItem('token', accessToken);
    }

    function logout() {
        token.value = null;
        localStorage.removeItem('token');
    }

    async function login(payload) {
        const response = await api.post('login', payload);
        setToken(response.data.data.token);
    }

    async function register(payload) {
        await api.post('registration', payload);
        await login({
            email: payload.email,
            password: payload.password,
        });
    }

    return {
        token,
        isAuthenticated,
        setToken,
        logout,
        login,
        register,
    };
});