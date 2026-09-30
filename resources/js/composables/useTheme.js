import { computed, ref } from 'vue';

const dark = ref(false);
let initialized = false;

function initialize() {
    if (initialized || typeof window === 'undefined') return;
    initialized = true;
    dark.value = document.documentElement.classList.contains('dark');
}

export function useTheme() {
    initialize();
    const label = computed(() => dark.value ? 'Kunduzgi rejim' : 'Tungi rejim');

    function toggleTheme() {
        dark.value = !dark.value;
        document.documentElement.classList.toggle('dark', dark.value);
        window.localStorage.setItem('uytop-theme', dark.value ? 'dark' : 'light');
    }

    return { dark, label, toggleTheme };
}
