import { computed, ref } from 'vue';

const selectedIds = ref([]);
let initialized = false;

function initialize() {
    if (initialized || typeof window === 'undefined') return;
    initialized = true;
    try {
        const stored = JSON.parse(window.localStorage.getItem('compare_ids') || '[]');
        selectedIds.value = Array.isArray(stored)
            ? [...new Set(stored.map(Number).filter(Number.isInteger))].slice(0, 3)
            : [];
    } catch {
        selectedIds.value = [];
    }
}

function persist() {
    window.localStorage.setItem('compare_ids', JSON.stringify(selectedIds.value));
}

export function useComparison() {
    initialize();
    const count = computed(() => selectedIds.value.length);
    const contains = (id) => selectedIds.value.includes(Number(id));

    function toggle(id) {
        const numericId = Number(id);
        if (contains(numericId)) {
            selectedIds.value = selectedIds.value.filter((item) => item !== numericId);
            persist();
            return { added: false };
        }
        if (selectedIds.value.length >= 3) {
            return { added: false, error: 'Solishtirish uchun ko‘pi bilan 3 ta taklif tanlash mumkin.' };
        }
        selectedIds.value = [...selectedIds.value, numericId];
        persist();
        return { added: true };
    }

    function remove(id) {
        selectedIds.value = selectedIds.value.filter((item) => item !== Number(id));
        persist();
    }

    function comparisonUrl() {
        const query = new URLSearchParams();
        selectedIds.value.forEach((id) => query.append('ids[]', id));
        return `/compare${query.size ? `?${query.toString()}` : ''}`;
    }

    return { selectedIds, count, contains, toggle, remove, comparisonUrl };
}
