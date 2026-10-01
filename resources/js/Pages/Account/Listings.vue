<template>
  <AppLayout>
    <Head title="Mening e’lonlarim" />
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
      <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div><p class="eyebrow">Shaxsiy kabinet</p><h1 class="page-title">Mening e’lonlarim</h1><p class="mt-3 text-slate-600 dark:text-slate-300">Qoralamalar, moderatsiya va faol takliflarni bir joydan boshqaring.</p></div>
        <Link href="/account/listings/create" class="rounded-full bg-[#e85d3f] px-5 py-3 text-sm font-black text-white shadow-lg shadow-orange-900/15">+ Yangi e’lon</Link>
      </div>
      <div v-if="$page.props.flash?.success" class="mt-6 rounded-2xl bg-emerald-100 px-4 py-3 text-sm font-semibold text-emerald-900 dark:bg-emerald-950 dark:text-emerald-200">{{ $page.props.flash.success }}</div>
      <div v-if="!listings.length" class="surface mt-8 rounded-[28px] p-12 text-center"><h2 class="text-xl font-black">Hozircha e’lon yo‘q</h2><p class="mt-2 text-slate-500">Birinchi e’loningizni qoralama sifatida saqlashingiz mumkin.</p></div>
      <div class="mt-8 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        <article v-for="listing in listings" :key="listing.id" class="surface rounded-[26px] p-5">
          <div class="flex items-start justify-between gap-3"><span class="rounded-full px-3 py-1 text-[11px] font-black uppercase tracking-wider" :class="statusClass(listing.moderation_status)">{{ statusLabel(listing.moderation_status) }}</span><span class="text-xs text-slate-400">#{{ listing.id }}</span></div>
          <h2 class="mt-4 line-clamp-2 text-lg font-black text-slate-950 dark:text-white">{{ listing.title }}</h2>
          <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ listing.district?.name_uz }} · {{ listing.deal_type === 'rent' ? 'Ijara' : 'Sotuv' }}</p>
          <p class="mt-4 text-xl font-black">{{ price(listing) }}</p>
          <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">{{ freshness(listing.confirmed_at) }}</p>
          <div v-if="listing.moderation_status === 'rejected' && listing.moderation_events?.[0]?.reason" class="mt-3 rounded-xl bg-red-50 p-3 text-xs font-semibold text-red-800 dark:bg-red-950 dark:text-red-200">Sabab: {{ listing.moderation_events[0].reason }}</div>
          <div class="mt-5 grid grid-cols-1 gap-2 sm:grid-cols-2"><Link :href="`/account/listings/${listing.id}/edit`" class="rounded-xl bg-slate-950 px-3 py-2.5 text-center text-sm font-bold text-white dark:bg-white dark:text-slate-950">Tahrirlash</Link><button v-if="listing.moderation_status === 'approved' && !listing.archived_at" class="rounded-xl bg-[#bedc79] px-3 py-2.5 text-sm font-bold text-[#24310d]" @click="confirmListing(listing.id)">Hali ham mavjud</button><button v-if="listing.availability_status === 'available' && !listing.archived_at" class="rounded-xl border border-black/10 px-3 py-2.5 text-sm font-bold text-slate-600 dark:border-white/10 dark:text-slate-300" @click="markUnavailable(listing)">{{ listing.deal_type === 'sale' ? 'Sotildi deb belgilash' : 'Ijaraga berildi deb belgilash' }}</button><button v-if="listing.availability_status !== 'available' && !listing.archived_at" class="rounded-xl border border-black/10 px-3 py-2.5 text-sm font-bold text-slate-600 dark:border-white/10 dark:text-slate-300" @click="setAvailability(listing.id, 'available')">Qayta e’lon qilish</button><button v-if="!listing.archived_at" class="rounded-xl border border-black/10 px-3 py-2.5 text-sm font-bold text-slate-600 dark:border-white/10 dark:text-slate-300" @click="archive(listing.id)">Arxivga olish</button></div>
        </article>
      </div>
    </section>
  </AppLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '../../Layouts/AppLayout.vue';
defineProps({ listings: { type: Array, default: () => [] } });
const statusLabel = (value) => ({ draft: 'Qoralama', pending: 'Moderatsiyada', approved: 'Faol', rejected: 'Rad etilgan', blocked: 'Bloklangan' }[value] || value);
const statusClass = (value) => ({ draft: 'bg-slate-100 text-slate-600', pending: 'bg-amber-100 text-amber-800', approved: 'bg-emerald-100 text-emerald-800', rejected: 'bg-red-100 text-red-700', blocked: 'bg-red-100 text-red-700' }[value]);
const price = (item) => item.price == null ? 'So‘rov bo‘yicha' : `${new Intl.NumberFormat('uz-UZ').format(item.price)} ${item.currency}`;
function archive(id) { if (window.confirm('E’lonni arxivlaysizmi?')) router.patch(`/account/listings/${id}/archive`, {}, { preserveScroll: true }); }
function confirmListing(id) { router.post(`/account/listings/${id}/confirm`, {}, { preserveScroll: true }); }
function setAvailability(id, status) { router.patch(`/account/listings/${id}/availability`, { status }, { preserveScroll: true }); }
function markUnavailable(item) { setAvailability(item.id, item.deal_type === 'sale' ? 'sold' : 'rented'); }
function freshness(value) { if (!value) return 'Hali dolzarbligi tasdiqlanmagan'; const days = Math.floor((Date.now() - new Date(value).getTime()) / 86400000); return days > 7 ? 'Anchadan beri yangilanmagan' : `Tasdiqlangan: ${new Intl.DateTimeFormat('uz-UZ').format(new Date(value))}`; }
</script>
