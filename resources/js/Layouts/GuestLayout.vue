<template>
  <div class="relative min-h-screen overflow-hidden bg-[#f4f1ea] text-slate-950 dark:bg-[#0d1117] dark:text-white">
    <div class="pointer-events-none absolute -left-40 -top-40 h-[480px] w-[480px] rounded-full border-[72px] border-[#e85d3f]/10" />
    <div class="pointer-events-none absolute -bottom-52 -right-32 h-[520px] w-[520px] rounded-full bg-[#bedc79]/15 blur-3xl" />
    <header class="relative z-10 mx-auto flex max-w-7xl items-center justify-between px-4 py-5 sm:px-6 lg:px-8">
      <Link href="/" class="flex items-center gap-3"><span class="flex h-10 w-10 rotate-3 items-center justify-center rounded-[14px] bg-[#e85d3f] font-black text-white shadow-lg shadow-orange-900/15">U</span><span><span class="block text-lg font-black tracking-[-0.05em]">UyTop</span><span class="block text-[9px] font-bold uppercase tracking-[0.22em] text-slate-500 dark:text-slate-400">Andijon</span></span></Link>
      <div class="flex items-center gap-2"><button type="button" class="rounded-full border border-black/10 bg-white/60 p-2.5 dark:border-white/10 dark:bg-white/5" :title="label" :aria-label="label" @click="toggleTheme"><svg v-if="dark" viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2"/></svg><svg v-else viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 15.5A8 8 0 0 1 8.5 4 8 8 0 1 0 20 15.5Z"/></svg></button><Link href="/" class="rounded-full px-4 py-2 text-sm font-bold text-slate-600 transition hover:text-[#e85d3f] dark:text-slate-300">Bosh sahifa</Link></div>
    </header>
    <main class="relative z-10 mx-auto grid min-h-[calc(100vh-80px)] max-w-7xl items-center gap-12 px-4 py-10 sm:px-6 lg:grid-cols-[.9fr_1.1fr] lg:px-8">
      <div class="hidden lg:block"><p class="text-xs font-black uppercase tracking-[0.22em] text-[#e85d3f]">UyTop hisobingiz</p><h1 class="mt-5 max-w-lg text-6xl font-black leading-[.98] tracking-[-0.065em]">Uy topish jarayoni <span class="font-serif font-normal italic text-[#e85d3f]">osonroq.</span></h1><p class="mt-6 max-w-md text-lg leading-8 text-slate-600 dark:text-slate-300">E’lonlarni saqlang, variantlarni solishtiring va uy egasiga murojaat yuboring.</p><div class="mt-9 flex gap-6 text-sm font-bold text-slate-500 dark:text-slate-400"><span>✓ Aniq xarajatlar</span><span>✓ Tekshirilgan shartlar</span></div></div>
      <div class="mx-auto w-full max-w-lg"><slot /></div>
    </main>
  </div>
</template>
<script setup>
import { Link } from '@inertiajs/vue3';
import { useTheme } from '../composables/useTheme';
const { dark, label, toggleTheme } = useTheme();
</script>
