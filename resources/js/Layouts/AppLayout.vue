<template>
  <div class="min-h-screen bg-[#f4f1ea] text-slate-900 transition-colors dark:bg-[#0d1117] dark:text-slate-100">
    <header class="sticky top-0 z-50 border-b border-black/10 bg-[#f4f1ea]/90 backdrop-blur-xl dark:border-white/10 dark:bg-[#0d1117]/90">
      <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
        <Link href="/" class="flex items-center gap-3">
          <span class="flex h-10 w-10 rotate-3 items-center justify-center rounded-[14px] bg-[#e85d3f] font-black text-white shadow-lg shadow-orange-900/15">U</span>
          <span><span class="block text-lg font-black tracking-[-0.05em] text-slate-950 dark:text-white">UyTop</span><span class="block text-[9px] font-bold uppercase tracking-[0.22em] text-slate-500 dark:text-slate-400">Andijon</span></span>
        </Link>

        <nav class="hidden items-center gap-7 text-sm font-semibold text-slate-600 dark:text-slate-300 md:flex">
          <Link href="/catalog" class="transition hover:text-[#e85d3f]">Katalog</Link>
          <Link :href="comparisonUrl()" class="relative transition hover:text-[#e85d3f]">Solishtirish<span v-if="count" class="absolute -right-4 -top-3 flex h-5 min-w-5 items-center justify-center rounded-full bg-[#e85d3f] px-1 text-[10px] text-white">{{ count }}</span></Link>
          <Link href="/projects" class="transition hover:text-[#e85d3f]">Yangi qurilishlar</Link>
          <Link v-if="$page.props.auth?.user" href="/account/listings" class="transition hover:text-[#e85d3f]">Kabinet</Link>
          <Link v-if="$page.props.auth?.user?.role === 'admin'" href="/admin/moderation" class="transition hover:text-[#e85d3f]">Moderatsiya</Link>
        </nav>

        <div class="hidden items-center gap-2 md:flex">
          <button type="button" class="rounded-full border border-black/10 bg-white/60 p-2.5 text-slate-700 transition hover:rotate-12 dark:border-white/10 dark:bg-white/5 dark:text-slate-200" :aria-label="label" :title="label" @click="toggleTheme"><svg v-if="dark" viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg><svg v-else viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 15.5A8 8 0 0 1 8.5 4 8 8 0 1 0 20 15.5Z"/></svg></button>
          <Link v-if="!$page.props.auth?.user" href="/login" class="rounded-full px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300">Kirish</Link>
          <Link v-else href="/account/inbox" class="rounded-full px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300">Kelganlar</Link>
          <Link :href="$page.props.auth?.user ? '/account/listings/create' : '/register'" class="rounded-full bg-slate-950 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-slate-900/15 dark:bg-[#e85d3f]">E’lon joylashtirish</Link>
        </div>

        <button type="button" class="rounded-xl border border-black/10 p-2 dark:border-white/10 md:hidden" aria-label="Menyuni ochish" @click="open = !open">
          <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h16M4 12h16M4 17h16" stroke-linecap="round" /></svg>
        </button>
      </div>

      <nav v-if="open" class="border-t border-black/10 bg-[#f4f1ea] px-4 py-4 dark:border-white/10 dark:bg-[#0d1117] md:hidden">
        <div class="mx-auto grid max-w-7xl gap-1 text-sm font-semibold text-slate-700">
          <Link href="/catalog" class="rounded-xl px-3 py-2 hover:bg-slate-50">Katalog</Link>
          <Link :href="comparisonUrl()" class="rounded-xl px-3 py-2 hover:bg-slate-50">Solishtirish ({{ count }})</Link>
          <Link href="/projects" class="rounded-xl px-3 py-2 hover:bg-slate-50">Yangi qurilishlar</Link>
          <Link :href="$page.props.auth?.user ? '/account/listings' : '/login'" class="rounded-xl px-3 py-2 hover:bg-black/5 dark:hover:bg-white/5">Kabinet</Link>
          <Link v-if="$page.props.auth?.user?.role === 'admin'" href="/admin/moderation" class="rounded-xl px-3 py-2 hover:bg-black/5 dark:hover:bg-white/5">Moderatsiya</Link>
          <button class="rounded-xl px-3 py-2 text-left hover:bg-black/5 dark:hover:bg-white/5" @click="toggleTheme">{{ label }}</button>
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
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useComparison } from '../composables/useComparison';
import { useTheme } from '../composables/useTheme';

const open = ref(false);
const { count, comparisonUrl } = useComparison();
const { dark, label, toggleTheme } = useTheme();
</script>
