<template>
  <AppLayout>
    <Head title="Murojaat yuborish" />
    <section class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
      <Link :href="`/listings/${listing.id}`" class="text-sm font-semibold text-slate-500">← E’longa qaytish</Link>
      <div class="mt-5 overflow-hidden rounded-[30px] border border-slate-200 bg-white shadow-[0_20px_55px_rgba(15,23,42,0.08)]">
        <div class="border-b border-slate-100 bg-slate-950 p-7 text-white"><p class="text-xs font-bold uppercase tracking-wider text-blue-300">Ko‘rish yoki bog‘lanish</p><h1 class="mt-2 text-3xl font-black tracking-[-0.05em]">Murojaat yuborish</h1><p class="mt-2 text-slate-300">{{ listing.title }}</p></div>
        <form class="space-y-5 p-7" @submit.prevent="submit">
          <div v-if="form.errors.request" class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">{{ form.errors.request }}</div>
          <div class="grid gap-5 sm:grid-cols-2"><Field label="Ism" :error="form.errors.name"><input v-model="form.name" class="field" required /></Field><Field label="Telefon" :error="form.errors.phone"><input v-model="form.phone" class="field" placeholder="+998 90 123 45 67" required /></Field></div>
          <div class="grid gap-5 sm:grid-cols-2"><Field label="Ko‘rish sanasi" :error="form.errors.proposed_at"><input v-model="form.proposed_at" type="date" :min="today" class="field" /></Field><Field label="Yashovchilar soni" :error="form.errors.occupants_count"><input v-model="form.occupants_count" type="number" min="1" :max="listing.free_places || undefined" class="field" /></Field></div>
          <Field label="Xabar" :error="form.errors.message"><textarea v-model="form.message" rows="4" class="field" placeholder="O‘zingiz haqingizda qisqacha yozing..." /></Field>
          <div class="flex items-center justify-between gap-4 pt-2"><p class="text-xs leading-5 text-slate-400">Telefon faqat e’lon egasi va administratorga ko‘rinadi.</p><button :disabled="form.processing" class="shrink-0 rounded-2xl bg-blue-600 px-6 py-3 text-sm font-bold text-white disabled:opacity-50">{{ form.processing ? 'Yuborilmoqda…' : 'Yuborish' }}</button></div>
        </form>
      </div>
    </section>
  </AppLayout>
</template>

<script setup>
import { h } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '../Layouts/AppLayout.vue';

const props = defineProps({ listing: Object });
const user = usePage().props.auth?.user;
const today = new Date().toISOString().slice(0, 10);
const form = useForm({ name: user?.name || '', phone: user?.phone || '', proposed_at: '', time_start: null, time_end: null, occupants_count: 1, message: '', idempotency_key: crypto.randomUUID() });
const Field = (p, { slots }) => h('label', { class: 'block' }, [h('span', { class: 'mb-1.5 block text-sm font-semibold text-slate-700' }, p.label), slots.default?.(), p.error ? h('span', { class: 'mt-1 block text-xs text-red-600' }, p.error) : null]);
Field.props = ['label', 'error'];
function submit() { form.post(`/listings/${props.listing.id}/requests`, { preserveScroll: true }); }
</script>

<style scoped>.field { @apply w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-blue-100; }</style>
