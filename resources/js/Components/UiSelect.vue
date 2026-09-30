<template>
  <div ref="root" class="relative" @keydown="onKeydown">
    <span v-if="label" :id="labelId" class="mb-1.5 block text-[11px] font-black uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400">{{ label }}</span>
    <button
      type="button"
      class="group flex w-full items-center justify-between gap-3 rounded-2xl border border-black/10 bg-white/75 px-4 py-3 text-left text-sm font-semibold text-slate-800 shadow-[0_6px_18px_rgba(35,32,28,0.04)] outline-none transition hover:border-black/20 focus:border-[#e85d3f] focus:ring-4 focus:ring-[#e85d3f]/10 disabled:cursor-not-allowed disabled:opacity-50 dark:border-white/10 dark:bg-white/[0.05] dark:text-slate-100 dark:hover:border-white/20"
      :disabled="disabled"
      :aria-labelledby="label ? labelId : undefined"
      aria-haspopup="listbox"
      :aria-expanded="open"
      @click="toggle"
    >
      <span class="truncate" :class="selected ? '' : 'text-slate-400'">{{ selected?.label || placeholder }}</span>
      <svg viewBox="0 0 20 20" class="h-4 w-4 shrink-0 text-slate-400 transition duration-200" :class="open ? 'rotate-180 text-[#e85d3f]' : ''" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m6 8 4 4 4-4" stroke-linecap="round" stroke-linejoin="round" /></svg>
    </button>

    <Transition enter-active-class="transition duration-150 ease-out" enter-from-class="-translate-y-2 opacity-0" enter-to-class="translate-y-0 opacity-100" leave-active-class="transition duration-100 ease-in" leave-from-class="opacity-100" leave-to-class="-translate-y-1 opacity-0">
      <div v-if="open" class="absolute z-[70] mt-2 w-full min-w-[190px] overflow-hidden rounded-2xl border border-black/10 bg-[#fffdf8] p-1.5 shadow-[0_22px_60px_rgba(35,32,28,0.20)] dark:border-white/10 dark:bg-[#171e26] dark:shadow-black/50">
        <ul ref="list" role="listbox" class="max-h-64 overflow-y-auto overscroll-contain py-0.5" :aria-labelledby="label ? labelId : undefined">
          <li v-for="(option, index) in options" :key="String(option.value)" role="option" :aria-selected="same(option.value, modelValue)">
            <button
              type="button"
              class="flex w-full items-center justify-between gap-3 rounded-xl px-3 py-2.5 text-left text-sm transition"
              :class="[
                same(option.value, modelValue) ? 'bg-[#e85d3f] font-bold text-white' : 'text-slate-700 hover:bg-black/[0.05] dark:text-slate-200 dark:hover:bg-white/[0.06]',
                activeIndex === index && !same(option.value, modelValue) ? 'bg-black/[0.05] dark:bg-white/[0.06]' : '',
              ]"
              @mouseenter="activeIndex = index"
              @click="choose(option)"
            >
              <span class="truncate">{{ option.label }}</span>
              <svg v-if="same(option.value, modelValue)" viewBox="0 0 20 20" class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2"><path d="m5 10 3 3 7-7" stroke-linecap="round" stroke-linejoin="round" /></svg>
            </button>
          </li>
        </ul>
      </div>
    </Transition>

    <span v-if="hint" class="mt-1.5 block text-xs text-slate-400">{{ hint }}</span>
    <span v-if="error" class="mt-1.5 block text-xs font-semibold text-red-600">{{ error }}</span>
  </div>
</template>

<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref } from 'vue';

const props = defineProps({
  modelValue: { type: [String, Number], default: '' },
  options: { type: Array, default: () => [] },
  label: { type: String, default: '' },
  placeholder: { type: String, default: 'Tanlang' },
  hint: { type: String, default: '' },
  error: { type: String, default: '' },
  disabled: { type: Boolean, default: false },
});
const emit = defineEmits(['update:modelValue', 'change']);
const root = ref(null);
const list = ref(null);
const open = ref(false);
const activeIndex = ref(0);
const labelId = `select-label-${Math.random().toString(36).slice(2)}`;
const same = (a, b) => String(a) === String(b);
const selected = computed(() => props.options.find((option) => same(option.value, props.modelValue)));

function toggle() {
  open.value = !open.value;
  if (open.value) {
    activeIndex.value = Math.max(0, props.options.findIndex((option) => same(option.value, props.modelValue)));
    nextTick(() => list.value?.querySelectorAll('button')[activeIndex.value]?.scrollIntoView({ block: 'nearest' }));
  }
}
function choose(option) {
  emit('update:modelValue', option.value);
  emit('change', option.value);
  open.value = false;
}
function onKeydown(event) {
  if (props.disabled || props.options.length === 0) return;
  if (event.key === 'Escape') { open.value = false; return; }
  if (event.key === 'Enter' || event.key === ' ') {
    event.preventDefault();
    if (!open.value) toggle(); else choose(props.options[activeIndex.value]);
    return;
  }
  if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
    event.preventDefault();
    if (!open.value) open.value = true;
    const step = event.key === 'ArrowDown' ? 1 : -1;
    activeIndex.value = (activeIndex.value + step + props.options.length) % props.options.length;
  }
}
function closeOutside(event) { if (!root.value?.contains(event.target)) open.value = false; }
onMounted(() => document.addEventListener('pointerdown', closeOutside));
onBeforeUnmount(() => document.removeEventListener('pointerdown', closeOutside));
</script>
