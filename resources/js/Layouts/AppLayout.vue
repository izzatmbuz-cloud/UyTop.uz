<template>
  <div class="min-h-screen bg-[#f4f1ea] text-slate-900 transition-colors dark:bg-[#0d1117] dark:text-slate-100">
    <header class="sticky top-0 z-50 border-b border-black/10 bg-[#f4f1ea]/90 backdrop-blur-xl dark:border-white/10 dark:bg-[#0d1117]/90">
      <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
        <Link href="/" class="flex items-center gap-3">
          <span class="flex h-11 w-11 items-center justify-center overflow-hidden rounded-[14px] bg-white/80 p-1 shadow-lg shadow-black/10 dark:bg-white/10"><img src="/logo.png" alt="UyTop logotipi" class="h-full w-full object-contain" /></span>
          <span><span class="block text-lg font-black tracking-[-0.05em] text-slate-950 dark:text-white">UyTop</span><span class="block text-[9px] font-bold uppercase tracking-[0.22em] text-slate-500 dark:text-slate-400">Andijon</span></span>
        </Link>

        <nav class="hidden items-center gap-7 text-sm font-semibold text-slate-600 dark:text-slate-300 md:flex">
          <Link href="/catalog" class="transition hover:text-[#e85d3f]">{{ t('catalog') }}</Link>
          <Link :href="comparisonUrl()" class="relative transition hover:text-[#e85d3f]">{{ t('compare') }}<span v-if="count" class="absolute -right-4 -top-3 flex h-5 min-w-5 items-center justify-center rounded-full bg-[#e85d3f] px-1 text-[10px] text-white">{{ count }}</span></Link>
          <Link href="/projects" class="transition hover:text-[#e85d3f]">{{ t('projects') }}</Link>
          <Link v-if="$page.props.auth?.user" href="/account/listings" class="transition hover:text-[#e85d3f]">{{ t('cabinet') }}</Link>
          <Link v-if="$page.props.auth?.user?.role === 'admin'" href="/admin/moderation" class="transition hover:text-[#e85d3f]">{{ t('moderation') }}</Link>
        </nav>

        <div class="hidden items-center gap-2 md:flex">
          <button type="button" class="rounded-full border border-black/10 bg-white/60 px-3 py-2 text-xs font-black dark:border-white/10 dark:bg-white/5" @click="toggleLocale">{{ nextLocale }}</button>
          <button type="button" class="rounded-full border border-black/10 bg-white/60 p-2.5 text-slate-700 transition hover:rotate-12 dark:border-white/10 dark:bg-white/5 dark:text-slate-200" :aria-label="label" :title="label" @click="toggleTheme"><svg v-if="dark" viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg><svg v-else viewBox="0 0 24 24" class="h-4 w-4" fill="currentColor"><path d="M20.4 15.25A8.4 8.4 0 0 1 8.75 3.6 9 9 0 1 0 20.4 15.25Z"/></svg></button>
          <Link v-if="!$page.props.auth?.user" href="/login" class="rounded-full px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300">{{ t('login') }}</Link>
          <Link :href="$page.props.auth?.user ? '/account/listings/create' : '/register'" class="rounded-full bg-slate-950 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-slate-900/15 dark:bg-[#e85d3f]">{{ t('publish') }}</Link>
          <div v-if="$page.props.auth?.user" ref="profileRoot" class="relative"><button type="button" class="flex h-10 w-10 items-center justify-center rounded-full bg-[#bedc79] text-sm font-black text-[#24310d] ring-2 ring-white dark:ring-[#0d1117]" :aria-expanded="profileOpen" aria-label="Profil menyusi" @click="profileOpen = !profileOpen">{{ initials }}</button><Transition enter-active-class="transition duration-150" enter-from-class="-translate-y-2 opacity-0" leave-active-class="transition duration-100" leave-to-class="-translate-y-1 opacity-0"><div v-if="profileOpen" class="absolute right-0 top-12 w-64 rounded-2xl border border-black/10 bg-[#fffdf8] p-2 shadow-2xl dark:border-white/10 dark:bg-[#171e26]"><div class="border-b border-black/10 px-3 py-3 dark:border-white/10"><p class="truncate text-sm font-black">{{ $page.props.auth.user.name }}</p><p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $page.props.auth.user.email }}</p></div><Link href="/profile" class="menu-item">{{ t('profile') }}</Link><Link href="/account/listings" class="menu-item">{{ t('listings') }}</Link><Link href="/account/requests" class="menu-item">{{ t('requests') }}</Link><Link href="/account/inbox" class="menu-item">{{ t('messages') }}</Link><Link href="/logout" method="post" as="button" class="menu-item w-full text-red-600" @click="clearComparison">{{ t('logout') }}</Link></div></Transition></div>
        </div>

        <button type="button" class="rounded-xl border border-black/10 p-2 dark:border-white/10 md:hidden" aria-label="Menyuni ochish" @click="open = !open">
          <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h16M4 12h16M4 17h16" stroke-linecap="round" /></svg>
        </button>
      </div>

      <nav v-if="open" class="border-t border-black/10 bg-[#f4f1ea] px-4 py-4 dark:border-white/10 dark:bg-[#0d1117] md:hidden">
        <div class="mx-auto grid max-w-7xl gap-1 text-sm font-semibold text-slate-700 dark:text-slate-200">
          <Link href="/catalog" class="rounded-xl px-3 py-2 hover:bg-slate-50">{{ t('catalog') }}</Link>
          <Link :href="comparisonUrl()" class="rounded-xl px-3 py-2 hover:bg-slate-50">{{ t('compare') }} ({{ count }})</Link>
          <Link href="/projects" class="rounded-xl px-3 py-2 hover:bg-slate-50">{{ t('projects') }}</Link>
          <Link :href="$page.props.auth?.user ? '/account/listings' : '/login'" class="rounded-xl px-3 py-2 hover:bg-black/5 dark:hover:bg-white/5">{{ t('cabinet') }}</Link>
          <Link v-if="$page.props.auth?.user" href="/account/inbox" class="rounded-xl px-3 py-2 hover:bg-black/5 dark:hover:bg-white/5">{{ t('messages') }}</Link>
          <Link v-if="$page.props.auth?.user" href="/profile" class="rounded-xl px-3 py-2 hover:bg-black/5 dark:hover:bg-white/5">{{ t('profile') }}</Link>
          <Link v-if="$page.props.auth?.user" href="/logout" method="post" as="button" class="rounded-xl px-3 py-2 text-left text-red-600" @click="clearComparison">{{ t('logout') }}</Link>
          <Link v-if="$page.props.auth?.user?.role === 'admin'" href="/admin/moderation" class="rounded-xl px-3 py-2 hover:bg-black/5 dark:hover:bg-white/5">Moderatsiya</Link>
          <button class="rounded-xl px-3 py-2 text-left hover:bg-black/5 dark:hover:bg-white/5" @click="toggleLocale">Til / Язык: {{ nextLocale }}</button><button class="rounded-xl px-3 py-2 text-left hover:bg-black/5 dark:hover:bg-white/5" @click="toggleTheme">{{ label }}</button>
        </div>
      </nav>
    </header>

    <main><slot /></main>

    <footer class="mt-16 border-t border-black/10 bg-white/50 dark:border-white/10 dark:bg-white/[0.02]">
      <div class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-8 text-sm text-slate-500 sm:px-6 md:flex-row md:justify-between lg:px-8">
        <p>© 2026 UyTop. Andijonda uy-joy qidirish.</p><p>Demo ma’lumotlar alohida belgilanadi.</p>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { useComparison } from '../composables/useComparison';
import { useTheme } from '../composables/useTheme';
import { useLocale } from '../composables/useLocale';

const open = ref(false);
const profileOpen = ref(false);
const profileRoot = ref(null);
const { count, clear: clearComparison, comparisonUrl } = useComparison();
const { dark, label, toggleTheme } = useTheme();
const { nextLocale, t, toggleLocale } = useLocale();
const page = usePage();
const initials = computed(() => page.props.auth?.user?.name?.split(/\s+/).slice(0, 2).map((part) => part[0]).join('').toUpperCase() || 'U');
function closeProfile(event) { if (!profileRoot.value?.contains(event.target)) profileOpen.value = false; }
onMounted(() => document.addEventListener('pointerdown', closeProfile));
onBeforeUnmount(() => document.removeEventListener('pointerdown', closeProfile));
</script>

<style scoped>.menu-item { @apply mt-1 block rounded-xl px-3 py-2 text-left text-sm font-semibold text-slate-700 transition hover:bg-black/5 dark:text-slate-200 dark:hover:bg-white/5; }</style>
