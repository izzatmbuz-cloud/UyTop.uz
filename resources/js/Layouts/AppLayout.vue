<template>
  <div class="min-h-screen bg-[#f4f7fb] text-slate-800">
    <header class="sticky top-0 z-50 border-b border-slate-200/80 bg-white/90 backdrop-blur-xl">
      <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
        <Link href="/" class="flex items-center gap-3">
          <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-cyan-500 font-black text-white shadow-lg shadow-blue-500/20">U</span>
          <span><span class="block text-lg font-black tracking-[-0.05em] text-slate-950">UyTop</span><span class="block text-[9px] font-bold uppercase tracking-[0.22em] text-slate-400">Andijon</span></span>
        </Link>

        <nav class="hidden items-center gap-7 text-sm font-semibold text-slate-600 md:flex">
          <Link href="/catalog" class="transition hover:text-blue-600">Katalog</Link>
          <Link :href="comparisonUrl()" class="relative transition hover:text-blue-600">Solishtirish<span v-if="count" class="absolute -right-4 -top-3 flex h-5 min-w-5 items-center justify-center rounded-full bg-blue-600 px-1 text-[10px] text-white">{{ count }}</span></Link>
          <Link href="/projects" class="transition hover:text-blue-600">Yangi qurilishlar</Link>
          <Link v-if="$page.props.auth?.user" href="/account/requests" class="transition hover:text-blue-600">Kabinet</Link>
        </nav>

        <div class="hidden items-center gap-2 md:flex">
          <Link v-if="!$page.props.auth?.user" href="/login" class="rounded-full px-4 py-2 text-sm font-semibold text-slate-600">Kirish</Link>
          <Link v-else href="/account/inbox" class="rounded-full px-4 py-2 text-sm font-semibold text-slate-600">Kelganlar</Link>
          <Link :href="$page.props.auth?.user ? '/dashboard' : '/register'" class="rounded-full bg-slate-950 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-slate-900/15">E’lon joylashtirish</Link>
        </div>

        <button type="button" class="rounded-xl border border-slate-200 p-2 md:hidden" aria-label="Menyuni ochish" @click="open = !open">
          <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h16M4 12h16M4 17h16" stroke-linecap="round" /></svg>
        </button>
      </div>

      <nav v-if="open" class="border-t border-slate-100 bg-white px-4 py-4 md:hidden">
        <div class="mx-auto grid max-w-7xl gap-1 text-sm font-semibold text-slate-700">
          <Link href="/catalog" class="rounded-xl px-3 py-2 hover:bg-slate-50">Katalog</Link>
          <Link :href="comparisonUrl()" class="rounded-xl px-3 py-2 hover:bg-slate-50">Solishtirish ({{ count }})</Link>
          <Link href="/projects" class="rounded-xl px-3 py-2 hover:bg-slate-50">Yangi qurilishlar</Link>
          <Link :href="$page.props.auth?.user ? '/account/requests' : '/login'" class="rounded-xl px-3 py-2 hover:bg-slate-50">Kabinet</Link>
        </div>
      </nav>
    </header>

    <main><slot /></main>

    <footer class="mt-16 border-t border-slate-200 bg-white">
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

const open = ref(false);
const { count, comparisonUrl } = useComparison();
</script>
