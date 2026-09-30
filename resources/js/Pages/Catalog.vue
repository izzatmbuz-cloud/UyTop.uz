<template>
  <AppLayout>
    <Head title="Katalog" />
    <section class="border-b border-slate-200 bg-white">
      <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <p class="text-xs font-bold uppercase tracking-[0.2em] text-blue-600">Andijondagi takliflar</p>
        <div class="mt-2 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
          <div><h1 class="text-4xl font-black tracking-[-0.06em] text-slate-950">Mos uy-joyni toping</h1><p class="mt-3 text-slate-600">Narx, yashash turi va talabalar uchun shartlar bo‘yicha qidiring.</p></div>
          <div class="flex gap-2 rounded-2xl bg-slate-100 p-1.5">
            <button v-for="option in dealOptions" :key="option.value" class="rounded-xl px-4 py-2 text-sm font-semibold transition" :class="filters.deal_type === option.value ? 'bg-white text-slate-950 shadow-sm' : 'text-slate-500'" @click="filters.deal_type = option.value; applyFilters()">{{ option.label }}</button>
          </div>
        </div>
      </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
      <div class="grid gap-7 lg:grid-cols-[280px_1fr]">
        <aside class="h-fit rounded-[24px] border border-slate-200 bg-white p-5 shadow-sm lg:sticky lg:top-24">
          <div class="flex items-center justify-between"><h2 class="font-bold text-slate-950">Filtrlar</h2><button class="text-xs font-semibold text-blue-600" @click="clearFilters">Tozalash</button></div>
          <div class="mt-5 space-y-5">
            <label class="block"><span class="field-label">Qidiruv</span><input v-model="filters.search" class="field" placeholder="Hudud yoki tavsif" @keyup.enter="applyFilters" /></label>
            <UiSelect v-if="filters.deal_type === 'rent'" v-model="filters.rental_unit" label="Ijara turi" :options="rentalOptions" />
            <UiSelect v-model="filters.district_id" label="Hudud" :options="districtOptions" />
            <div><UiSelect v-model="filters.currency" label="Narx va valyuta" :options="currencyOptions" /><div class="mt-2 grid grid-cols-2 gap-2"><input v-model="filters.price_min" type="number" class="field" placeholder="Min" /><input v-model="filters.price_max" type="number" class="field" placeholder="Max" /></div></div>
            <label v-if="filters.deal_type !== 'sale'" class="flex items-start gap-3 rounded-2xl bg-blue-50 p-3 text-sm text-slate-700"><input v-model="filters.students_allowed" type="checkbox" class="mt-0.5 rounded border-slate-300 text-blue-600" /><span><strong class="block text-slate-900">Talabalar qabul qilinadi</strong>Faqat aniq “ha” deb belgilanganlar</span></label>
            <button class="w-full rounded-2xl bg-slate-950 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-slate-900/10" @click="applyFilters">Natijalarni ko‘rsatish</button>
          </div>
        </aside>

        <div>
          <div class="mb-5 flex flex-wrap items-center justify-between gap-3"><p class="text-sm text-slate-500"><strong class="text-slate-950">{{ listings.total }}</strong> ta taklif topildi</p><UiSelect v-model="filters.sort" :options="sortOptions" @change="applyFilters" /></div>

          <div v-if="notice" class="mb-4 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">{{ notice }}</div>
          <div v-if="!listings.data.length" class="rounded-[28px] border border-dashed border-slate-300 bg-white p-12 text-center"><h2 class="text-xl font-bold text-slate-900">Mos taklif topilmadi</h2><p class="mt-2 text-slate-500">Filtrlarni yengillashtirib qayta urinib ko‘ring.</p></div>

          <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
            <article v-for="listing in listings.data" :key="listing.id" class="group overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-[0_10px_30px_rgba(15,23,42,0.04)] transition hover:-translate-y-1 hover:shadow-[0_22px_45px_rgba(15,23,42,0.09)]">
              <Link :href="`/listings/${listing.id}`" class="block">
                <div class="relative h-48 bg-gradient-to-br from-slate-100 to-slate-200"><img v-if="listing.media?.length" :src="`/storage/${listing.media[0].storage_path}`" :alt="listing.title" class="h-full w-full object-cover" /><div v-else class="flex h-full items-center justify-center text-sm font-medium text-slate-400">Rasm tayyorlanmoqda</div><span v-if="listing.is_demo" class="absolute left-3 top-3 rounded-full bg-slate-950/80 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-white">Demo</span></div>
                <div class="p-5"><div class="flex items-center justify-between gap-2 text-xs font-semibold text-slate-500"><span>{{ listing.district?.name_uz }}</span><span :class="listing.students_allowed === 'yes' ? 'text-emerald-600' : ''">{{ listing.students_allowed === 'yes' ? 'Talabalar uchun' : 'Shartni aniqlang' }}</span></div><h2 class="mt-3 line-clamp-2 min-h-12 text-lg font-bold leading-6 text-slate-950">{{ listing.title }}</h2><p class="mt-3 text-2xl font-black tracking-[-0.04em] text-slate-950">{{ formatPrice(listing.price, listing.currency) }} <span class="text-xs font-medium text-slate-400">{{ priceBasis(listing.price_basis) }}</span></p><div class="mt-4 flex gap-3 text-xs text-slate-500"><span>{{ unitLabel(listing.rental_unit) }}</span><span v-if="listing.rooms">{{ listing.rooms }} xona</span><span v-if="listing.free_places">{{ listing.free_places }} bo‘sh</span></div></div>
              </Link>
              <div class="flex gap-2 border-t border-slate-100 p-3"><Link :href="`/listings/${listing.id}`" class="flex-1 rounded-xl bg-slate-950 px-3 py-2.5 text-center text-sm font-semibold text-white">Batafsil</Link><button class="flex-1 rounded-xl border px-3 py-2.5 text-sm font-semibold transition" :class="contains(listing.id) ? 'border-blue-600 bg-blue-50 text-blue-700' : 'border-slate-200 text-slate-700'" @click="toggleCompare(listing.id)">{{ contains(listing.id) ? 'Tanlandi' : 'Solishtirish' }}</button></div>
            </article>
          </div>

          <div v-if="listings.links?.length > 3" class="mt-8 flex flex-wrap justify-center gap-2"><Link v-for="link in listings.links" :key="link.label" :href="link.url || '#'" class="rounded-xl border px-3 py-2 text-sm" :class="link.active ? 'border-slate-950 bg-slate-950 text-white' : 'border-slate-200 bg-white text-slate-600'" v-html="link.label" /></div>
        </div>
      </div>
    </section>

    <Link v-if="count" :href="comparisonUrl()" class="fixed bottom-5 left-1/2 z-40 -translate-x-1/2 rounded-full bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-2xl shadow-blue-600/30">Solishtirish · {{ count }}</Link>
  </AppLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, reactive, ref } from 'vue';
import AppLayout from '../Layouts/AppLayout.vue';
import { useComparison } from '../composables/useComparison';
import UiSelect from '../Components/UiSelect.vue';

const props = defineProps({ listings: Object, districts: Object, filters: Object });
const filters = reactive({ deal_type: props.filters?.deal_type || 'rent', rental_unit: props.filters?.rental_unit || '', students_allowed: props.filters?.students_allowed === 'yes', district_id: props.filters?.district_id || '', price_min: props.filters?.price_min || '', price_max: props.filters?.price_max || '', currency: props.filters?.currency || 'UZS', sort: props.filters?.sort || 'confirmed_at', search: props.filters?.search || '' });
const dealOptions = [{ value: 'rent', label: 'Ijara' }, { value: 'sale', label: 'Sotuv' }, { value: '', label: 'Barchasi' }];
const rentalOptions = [{ value: '', label: 'Hammasi' }, { value: 'whole', label: 'Butun uy' }, { value: 'room', label: 'Xona' }, { value: 'bed', label: 'O‘rin' }];
const districtOptions = computed(() => [
  { value: '', label: 'Barcha hududlar' },
  ...Object.entries(props.districts || {}).map(([value, label]) => ({ value, label })),
]);
const currencyOptions = [{ value: 'UZS', label: 'UZS' }, { value: 'USD', label: 'USD' }];
const sortOptions = [{ value: 'confirmed_at', label: 'Yaqinda tasdiqlangan' }, { value: 'price_asc', label: 'Narx: pastdan' }, { value: 'price_desc', label: 'Narx: yuqoridan' }, { value: 'date', label: 'Yangi e’lonlar' }];
const notice = ref('');
const { count, contains, toggle, comparisonUrl } = useComparison();

function applyFilters() { router.get('/catalog', { ...filters, students_allowed: filters.students_allowed ? 'yes' : undefined }, { preserveState: true, replace: true }); }
function clearFilters() { Object.assign(filters, { deal_type: 'rent', rental_unit: '', students_allowed: false, district_id: '', price_min: '', price_max: '', currency: 'UZS', sort: 'confirmed_at', search: '' }); applyFilters(); }
function toggleCompare(id) { const result = toggle(id); notice.value = result.error || (result.added ? 'Taklif solishtirishga qo‘shildi.' : 'Taklif solishtirishdan olib tashlandi.'); }
function formatPrice(price, currency) { return price == null ? 'So‘rov bo‘yicha' : new Intl.NumberFormat('uz-UZ').format(price) + ` ${currency}`; }
function priceBasis(value) { return { monthly_unit: '/ oy', total: 'umumiy', from_total: 'dan boshlab', per_m2: '/ m²' }[value] || ''; }
function unitLabel(value) { return { whole: 'Butun uy', room: 'Xona', bed: 'O‘rin' }[value] || 'Sotuv'; }
</script>

<style scoped>
.field-label { @apply mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500; }
.field { @apply w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-blue-500 focus:ring-blue-100; }
</style>
