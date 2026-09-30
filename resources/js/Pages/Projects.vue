<template>
  <AppLayout>
    <Head title="Yangi qurilishlar" />
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
      <p class="text-xs font-bold uppercase tracking-[0.2em] text-blue-600">Quruvchilar takliflari</p>
      <h1 class="mt-2 text-4xl font-black tracking-[-0.06em] text-slate-950">Yangi qurilishlar</h1>
      <p class="mt-3 max-w-2xl text-slate-600">Andijondagi yangi loyihalar va ular bo‘yicha mavjud takliflar.</p>
      <div v-if="!projects.length" class="mt-8 rounded-[28px] border border-dashed border-slate-300 bg-white p-12 text-center text-slate-500">Hozircha tasdiqlangan loyiha yo‘q.</div>
      <div class="mt-8 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
        <article v-for="project in projects" :key="project.id" class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
          <div class="h-40 bg-gradient-to-br from-blue-950 via-blue-800 to-cyan-600 p-6 text-white"><span v-if="project.is_demo" class="rounded-full bg-white/15 px-3 py-1 text-xs font-bold">Demo loyiha</span><h2 class="mt-7 text-2xl font-black">{{ project.name }}</h2></div>
          <div class="p-6"><p class="text-sm font-semibold text-blue-600">{{ project.developer_name || 'Quruvchi ko‘rsatilmagan' }}</p><p class="mt-3 line-clamp-3 text-sm leading-6 text-slate-600">{{ project.description }}</p><dl class="mt-5 grid grid-cols-2 gap-3 text-sm"><div class="rounded-xl bg-slate-50 p-3"><dt class="text-xs text-slate-400">Hudud</dt><dd class="mt-1 font-semibold">{{ project.district?.name_uz }}</dd></div><div class="rounded-xl bg-slate-50 p-3"><dt class="text-xs text-slate-400">Topshirish</dt><dd class="mt-1 font-semibold">{{ project.completion_text || 'Aniqlanmoqda' }}</dd></div></dl></div>
        </article>
      </div>
    </section>
  </AppLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '../Layouts/AppLayout.vue';
defineProps({ projects: { type: Array, default: () => [] } });
</script>
