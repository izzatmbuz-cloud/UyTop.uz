<template>
  <AppLayout>
    <Head title="Yangi qurilishlar" />
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
      <p class="text-xs font-bold uppercase tracking-[0.2em] text-blue-600">Quruvchilar takliflari</p>
      <h1 class="mt-2 text-4xl font-black tracking-[-0.06em] text-slate-950">Yangi qurilishlar</h1>
      <p class="mt-3 max-w-2xl text-slate-600">Andijondagi yangi loyihalar va ular bo‘yicha mavjud takliflar.</p>
      <div v-if="!projects.length" class="mt-8 rounded-[28px] border border-dashed border-slate-300 bg-white p-12 text-center text-slate-500">Hozircha tasdiqlangan loyiha yo‘q.</div>
      <div class="mt-8 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
        <Link v-for="project in projects" :key="project.id" :href="`/projects/${project.id}`" class="surface group overflow-hidden rounded-[28px] transition hover:-translate-y-1">
          <div class="relative h-44 overflow-hidden bg-gradient-to-br from-[#222a25] via-[#365148] to-[#e85d3f] p-6 text-white"><img v-if="project.media?.length" :src="`/storage/${project.media[0].storage_path}`" :alt="project.name" class="absolute inset-0 h-full w-full object-cover opacity-60 transition duration-500 group-hover:scale-105" /><div class="relative"><span v-if="project.is_demo" class="rounded-full bg-white/15 px-3 py-1 text-xs font-bold backdrop-blur">Demo loyiha</span><h2 class="mt-8 text-2xl font-black">{{ project.name }}</h2></div></div>
          <div class="p-6"><p class="text-sm font-semibold text-[#e85d3f]">{{ project.developer_name || 'Quruvchi ko‘rsatilmagan' }}</p><p class="mt-3 line-clamp-3 text-sm leading-6 text-slate-600 dark:text-slate-300">{{ project.description }}</p><dl class="mt-5 grid grid-cols-2 gap-3 text-sm"><div class="rounded-xl bg-slate-50 p-3"><dt class="text-xs text-slate-400">Hudud</dt><dd class="mt-1 font-semibold">{{ project.district?.name_uz }}</dd></div><div class="rounded-xl bg-slate-50 p-3"><dt class="text-xs text-slate-400">Takliflar</dt><dd class="mt-1 font-semibold">{{ project.listings_count }} ta</dd></div></dl></div>
        </Link>
      </div>
    </section>
  </AppLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '../Layouts/AppLayout.vue';
defineProps({ projects: { type: Array, default: () => [] } });
</script>
