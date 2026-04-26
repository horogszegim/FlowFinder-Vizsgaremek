import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { api } from '@utils/http.mjs';

function getStoredUser() {
    try {
        const storedUser = localStorage.getItem('user');
        return storedUser ? JSON.parse(storedUser) : null;
    } catch {
        localStorage.removeItem('user');
        return null;
    }
}

export const useAuthStore = defineStore('auth', () => {
    const token = ref(localStorage.getItem('token'));
    const user = ref(getStoredUser());

    const isAuthenticated = computed(() => {
        return !!token.value;
    });

    const isAdmin = computed(() => {
        return user.value?.role === 'admin';
    });

    function setToken(accessToken) {
        token.value = accessToken;
        localStorage.setItem('token', accessToken);
    }

    function setUser(authUser) {
        user.value = authUser;

        if (authUser) {
            localStorage.setItem('user', JSON.stringify(authUser));
            return;
        }

        localStorage.removeItem('user');
    }

    async function fetchUser() {
        if (!token.value) {
            setUser(null);
            return null;
        }

        const response = await api.get('user');
        setUser(response.data.data);
        return response.data.data;
    }

    async function logout() {
        try {
            if (token.value) {
                await api.post('logout');
            }
        } catch {
        } finally {
            token.value = null;
            setUser(null);
            localStorage.removeItem('token');
        }
    }

    async function login(payload) {
        const response = await api.post('login', payload);
        setToken(response.data.data.token);
        setUser(response.data.data.user || null);

        if (!user.value) {
            await fetchUser();
        }
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
        user,
        isAuthenticated,
        isAdmin,
        setToken,
        setUser,
        fetchUser,
        logout,
        login,
        register,
    };
});