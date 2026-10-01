<template>
  <AppLayout>
    <Head title="Andijonda uy-joy toping" />
    <section class="relative overflow-hidden border-b border-black/10 dark:border-white/10">
      <div class="pointer-events-none absolute -right-36 -top-36 h-[520px] w-[520px] rounded-full border-[70px] border-[#e85d3f]/10 dark:border-[#e85d3f]/10" />
      <div class="mx-auto grid max-w-7xl gap-12 px-4 py-16 sm:px-6 lg:grid-cols-[1.05fr_.95fr] lg:px-8 lg:py-24">
        <div class="relative z-10">
          <div class="inline-flex -rotate-1 items-center gap-2 border border-black/15 bg-[#eee5d3] px-3 py-2 text-[11px] font-black uppercase tracking-[0.18em] text-slate-700 dark:border-white/15 dark:bg-white/5 dark:text-slate-300">
            <span class="h-2 w-2 rounded-full bg-[#e85d3f]" /> {{ t('heroBadge') }}
          </div>
          <h1 class="mt-8 max-w-2xl text-5xl font-black leading-[.96] tracking-[-0.075em] text-slate-950 dark:text-white sm:text-6xl lg:text-[76px]">
            {{ t('hero1') }}
            <span class="block font-serif font-normal italic text-[#e85d3f]">{{ t('hero2') }}</span>
            {{ t('hero3') }}
          </h1>
          <p class="mt-7 max-w-xl text-lg leading-8 text-slate-600 dark:text-slate-300">{{ t('heroText') }}</p>
          <div class="mt-9 flex flex-wrap gap-3"><Link href="/catalog?deal_type=rent" class="rounded-full bg-slate-950 px-6 py-3.5 text-sm font-bold text-white shadow-xl shadow-black/10 dark:bg-[#e85d3f]">{{ t('viewHomes') }}</Link><Link href="/compare" class="rounded-full border border-black/15 bg-white/50 px-6 py-3.5 text-sm font-bold text-slate-800 dark:border-white/15 dark:bg-white/5 dark:text-slate-100">{{ t('compareVariants') }}</Link></div>
        </div>

        <div class="relative z-10 lg:pt-5">
          <div class="surface relative rounded-[32px] p-5 sm:p-7">
            <span class="absolute -right-3 -top-3 rotate-6 rounded-full bg-[#bedc79] px-4 py-2 text-xs font-black uppercase tracking-wider text-[#24310d] shadow-lg">{{ t('quickSearch') }}</span>
            <h2 class="text-2xl font-black tracking-[-0.04em] text-slate-950 dark:text-white">{{ t('suitable') }}</h2>
            <form class="mt-6 space-y-4" @submit.prevent="search">
              <label class="block"><span class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Qidiruv</span><input v-model="form.search" class="w-full rounded-2xl border-black/10 bg-white/70 px-4 py-3 text-sm font-semibold shadow-sm dark:border-white/10 dark:bg-white/5 dark:text-white" placeholder="Mahalla, universitet yoki manzil" /></label>
              <div class="grid grid-cols-2 gap-3"><button v-for="unit in units" :key="unit.value" type="button" class="rounded-2xl border p-4 text-left transition" :class="form.rental_unit === unit.value ? 'border-[#e85d3f] bg-[#e85d3f]/10 text-[#c7452c]' : 'border-black/10 bg-black/[0.02] text-slate-600 dark:border-white/10 dark:bg-white/[0.03] dark:text-slate-300'" @click="selectUnit(unit.value)"><span class="block text-lg">{{ unit.icon }}</span><span class="mt-2 block text-sm font-bold">{{ unit.label }}</span></button></div>
              <div :key="filterAnimation" class="space-y-4 animate-filter-refresh">
              <UiSelect v-model="form.district_id" :label="t('district')" :options="districtOptions" />
              <UiSelect v-model="form.locality" label="Andijon shahri ichida" :options="localityOptions" />
              <div class="grid grid-cols-[1fr_120px] gap-3"><label class="block"><span class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">{{ t('maxPrice') }}</span><input v-model="form.price_max" type="number" class="w-full rounded-2xl border-black/10 bg-white/70 px-4 py-3 text-sm font-semibold shadow-sm dark:border-white/10 dark:bg-white/5" placeholder="800 000" /></label><UiSelect v-model="form.currency" :label="t('currency')" :options="currencyOptions" /></div>
              <label class="flex items-center gap-3 rounded-2xl bg-[#bedc79]/25 px-4 py-3 text-sm font-semibold text-slate-800 dark:text-slate-100"><input v-model="form.students_allowed" type="checkbox" class="rounded border-black/20 text-[#e85d3f] focus:ring-[#e85d3f]" /> {{ t('students') }}</label>
              <button class="w-full rounded-2xl bg-[#e85d3f] px-5 py-3.5 text-sm font-black text-white transition hover:-translate-y-0.5 hover:bg-[#d94e32]">{{ t('find') }}</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
      <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"><div><p class="text-xs font-black uppercase tracking-[0.2em] text-[#e85d3f]">Katalogdan hozir</p><h2 class="mt-2 text-3xl font-black tracking-[-0.055em] text-slate-950 dark:text-white sm:text-4xl">Yangi takliflar</h2></div><Link href="/catalog" class="text-sm font-bold text-slate-600 underline decoration-[#e85d3f] decoration-2 underline-offset-4 dark:text-slate-300">Barcha 24 taklif →</Link></div>
      <div class="mt-8 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
        <Link v-for="(listing, index) in featuredListings" :key="listing.id" :href="`/listings/${listing.id}`" class="surface group overflow-hidden rounded-[26px] transition hover:-translate-y-1" :class="index === 0 ? 'md:col-span-2 xl:col-span-1' : ''">
          <div class="relative h-44 overflow-hidden bg-[#ded8cb] dark:bg-slate-800"><img v-if="listing.media?.length" :src="`/storage/${listing.media[0].storage_path}`" :alt="listing.title" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" /><div v-else class="absolute inset-0 flex items-center justify-center"><svg viewBox="0 0 120 80" class="h-20 w-28 text-slate-400/50" fill="none" stroke="currentColor"><path d="M8 70h104M18 70V35l27-20 27 20v35M72 70V27l14-10 16 12v41M32 46h12v12H32zM82 38h10v10H82z" stroke-width="3" /></svg></div><span class="absolute left-3 top-3 rounded-full bg-[#f4f1ea]/90 px-3 py-1 text-[10px] font-black uppercase tracking-wider text-slate-700 backdrop-blur">{{ listing.district?.name_uz }}</span></div>
          <div class="p-5"><div class="flex items-center justify-between gap-2"><span class="text-xs font-bold text-[#e85d3f]">{{ unitLabel(listing.rental_unit) }}</span><span v-if="listing.students_allowed === 'yes'" class="text-xs font-bold text-emerald-700 dark:text-emerald-400">Talabalar uchun</span></div><h3 class="mt-3 line-clamp-2 text-lg font-black leading-6 text-slate-950 dark:text-white">{{ listing.title }}</h3><p class="mt-4 text-xl font-black text-slate-950 dark:text-white">{{ money(listing.price, listing.currency) }} <span class="text-xs font-medium text-slate-400">{{ listing.deal_type === 'rent' ? '/ oy' : '' }}</span></p></div>
        </Link>
      </div>
    </section>

    <section class="border-y border-black/10 bg-[#222a25] text-white dark:border-white/10 dark:bg-[#151b18]">
      <div class="mx-auto grid max-w-7xl gap-10 px-4 py-14 sm:px-6 md:grid-cols-3 lg:px-8"><div><span class="text-3xl font-serif italic text-[#bedc79]">01.</span><h3 class="mt-3 font-black">Aniq shartlar</h3><p class="mt-2 text-sm leading-6 text-slate-300">Talabalar, bo‘sh joy va to‘lov birligi alohida ko‘rsatiladi.</p></div><div><span class="text-3xl font-serif italic text-[#bedc79]">02.</span><h3 class="mt-3 font-black">Yashirin xarajatsiz</h3><p class="mt-2 text-sm leading-6 text-slate-300">Depozit, kommunal va komissiya noma’lum bo‘lsa, buni yashirmaymiz.</p></div><div><span class="text-3xl font-serif italic text-[#bedc79]">03.</span><h3 class="mt-3 font-black">Bir joyda murojaat</h3><p class="mt-2 text-sm leading-6 text-slate-300">Variantni toping, solishtiring va egasiga murojaat yuboring.</p></div></div>
    </section>
  </AppLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, reactive, ref } from 'vue';
import AppLayout from '../Layouts/AppLayout.vue';
import UiSelect from '../Components/UiSelect.vue';
import { useLocale } from '../composables/useLocale';

const props = defineProps({ featuredListings: { type: Array, default: () => [] }, districts: { type: Array, default: () => [] }, localities: { type: Array, default: () => [] } });
const form = reactive({ search: '', rental_unit: 'bed', district_id: '', locality: '', price_max: '', currency: 'UZS', students_allowed: true });
const filterAnimation = ref(0);
const { t } = useLocale();
const districtOptions = computed(() => [
  { value: '', label: t('allDistricts') },
  ...props.districts.map((district) => ({ value: district.id, label: district.name_uz })),
]);
const currencyOptions = [{ value: 'UZS', label: 'UZS' }, { value: 'USD', label: 'USD' }];
const localityOptions = computed(() => [{ value: '', label: 'Barcha joylar' }, ...props.localities.map((value) => ({ value, label: value }))]);
const units = computed(() => [{ value: 'whole', label: t('whole'), icon: '⌂' }, { value: 'room', label: t('room'), icon: '▣' }, { value: 'bed', label: t('bed'), icon: '⌁' }, { value: '', label: t('all'), icon: '✦' }]);
function search() { router.get('/catalog', { deal_type: 'rent', search: form.search || undefined, rental_unit: form.rental_unit || undefined, district_id: form.district_id || undefined, locality: form.locality || undefined, price_max: form.price_max || undefined, currency: form.currency, students_allowed: form.students_allowed ? 'yes' : undefined }); }
function selectUnit(value) { if (form.rental_unit === value) return; form.rental_unit = value; filterAnimation.value += 1; }
function money(value, currency) { return value == null ? 'So‘rov bo‘yicha' : new Intl.NumberFormat('uz-UZ').format(value) + ` ${currency}`; }
function unitLabel(value) { return { whole: 'Butun uy', room: 'Xona', bed: 'O‘rin' }[value] || 'Sotuv'; }
</script>

<style scoped>
@keyframes filter-refresh { 0% { opacity: 0; transform: translateY(12px) scale(.985); } 65% { transform: translateY(-2px) scale(1.005); } 100% { opacity: 1; transform: translateY(0) scale(1); } }
.animate-filter-refresh { animation: filter-refresh .36s cubic-bezier(.2,.8,.2,1); }
@media (prefers-reduced-motion: reduce) { .animate-filter-refresh { animation: none; } }
</style>
