<template>
  <AppLayout>
    <Head :title="listing.title" />
    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
      <div class="mb-5 flex items-center justify-between gap-4"><Link href="/catalog" class="text-sm font-semibold text-slate-500 hover:text-blue-600">← Katalogga qaytish</Link><button class="rounded-full border px-4 py-2 text-sm font-semibold" :class="contains(listing.id) ? 'border-blue-600 bg-blue-50 text-blue-700' : 'border-slate-200 bg-white text-slate-700'" @click="toggleItem">{{ contains(listing.id) ? 'Tanlangan' : 'Solishtirishga qo‘shish' }}</button></div>
      <div v-if="notice" class="mb-4 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">{{ notice }}</div>

      <div class="grid gap-7 lg:grid-cols-[1.2fr_0.8fr]">
        <div>
          <div class="overflow-hidden rounded-[30px] border border-slate-200 bg-slate-200 shadow-sm"><img v-if="listing.media?.length" :src="`/storage/${listing.media[0].storage_path}`" :alt="listing.title" class="h-[420px] w-full object-cover" /><div v-else class="flex h-[420px] items-center justify-center text-slate-400">Rasm mavjud emas</div></div>
          <div class="mt-6 rounded-[26px] border border-slate-200 bg-white p-6"><h2 class="text-xl font-bold text-slate-950">E’lon haqida</h2><p class="mt-4 whitespace-pre-line leading-7 text-slate-600">{{ listing.description }}</p></div>
        </div>

        <aside class="h-fit rounded-[30px] border border-slate-200 bg-white p-6 shadow-[0_20px_55px_rgba(15,23,42,0.08)] lg:sticky lg:top-24">
          <div class="flex flex-wrap gap-2"><span class="tag bg-blue-50 text-blue-700">{{ listing.deal_type === 'rent' ? 'Ijara' : 'Sotuv' }}</span><span v-if="listing.rental_unit" class="tag bg-slate-100 text-slate-700">{{ unitLabel(listing.rental_unit) }}</span><span v-if="listing.is_demo" class="tag bg-amber-50 text-amber-700">Demo</span></div>
          <h1 class="mt-4 text-3xl font-black leading-tight tracking-[-0.05em] text-slate-950">{{ listing.title }}</h1>
          <p class="mt-3 text-sm text-slate-500">{{ listing.district?.name_uz }} · {{ listing.location_text || 'Mo‘ljal ko‘rsatilmagan' }}</p>
          <p class="mt-6 text-3xl font-black tracking-[-0.05em] text-blue-600">{{ money(listing.price) }} <span class="text-sm font-semibold text-slate-400">{{ basisLabel(listing.price_basis) }}</span></p>

          <div v-if="costs" class="mt-6 grid grid-cols-2 gap-3"><div class="rounded-2xl bg-slate-950 p-4 text-white"><p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Oyiga</p><p class="mt-2 text-lg font-bold">{{ money(costs.monthly_payment) }}</p><p v-if="!costs.monthly_complete" class="mt-1 text-xs text-amber-300">Qisman ma’lum</p></div><div class="rounded-2xl bg-blue-600 p-4 text-white"><p class="text-[10px] font-bold uppercase tracking-wider text-blue-200">Joylashishda</p><p class="mt-2 text-lg font-bold">{{ money(costs.movein_cost) }}</p><p v-if="!costs.movein_complete" class="mt-1 text-xs text-blue-100">Qisman ma’lum</p></div></div>

          <dl class="mt-6 divide-y divide-slate-100 text-sm"><InfoRow label="Talabalar" :value="studentLabel(listing.students_allowed)" /><InfoRow label="Bo‘sh o‘rin" :value="listing.free_places ?? 'Noma’lum'" /><InfoRow label="Xonalar" :value="listing.rooms ?? 'Noma’lum'" /><InfoRow label="Mavjud sana" :value="formatDate(listing.available_from)" /><InfoRow label="Kommunal" :value="costMode(listing.utilities_mode, listing.utilities_amount)" /><InfoRow label="Depozit" :value="costMode(listing.deposit_mode, listing.deposit_amount)" /><InfoRow label="Komissiya" :value="costMode(listing.commission_mode, listing.commission_amount)" /></dl>

          <Link :href="`/listings/${listing.id}/request`" class="mt-6 flex w-full items-center justify-center rounded-2xl bg-slate-950 px-5 py-3.5 text-sm font-bold text-white shadow-lg shadow-slate-900/15">Ko‘rish uchun murojaat</Link>
          <button v-if="$page.props.auth?.user?.id !== listing.owner_user_id" type="button" class="mt-3 w-full rounded-2xl border border-black/10 px-5 py-3 text-sm font-bold text-slate-500 transition hover:border-red-200 hover:bg-red-50 hover:text-red-700 dark:border-white/10 dark:hover:bg-red-950" @click="reportOpen = true">Shikoyat qilish</button>
        </aside>
      </div>
    </section>

    <div v-if="reportOpen" class="fixed inset-0 z-[80] flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm" @click.self="reportOpen = false"><form class="surface w-full max-w-md rounded-[28px] p-6" @submit.prevent="submitReport"><div class="flex items-start justify-between gap-4"><div><p class="text-xs font-black uppercase tracking-wider text-red-600">Shikoyat</p><h2 class="mt-2 text-2xl font-black">Muammoni bildiring</h2></div><button type="button" class="rounded-full p-2 text-slate-400 hover:bg-black/5" aria-label="Yopish" @click="reportOpen = false">✕</button></div><p class="mt-3 text-sm text-slate-500">Administrator e’lon va sababni tekshiradi. Bir e’lon uchun bitta shikoyat yuborish mumkin.</p><div class="mt-5 grid gap-2"><label v-for="reason in reportReasons" :key="reason.value" class="flex cursor-pointer items-center gap-3 rounded-xl border border-black/10 px-3 py-2.5 text-sm font-semibold dark:border-white/10"><input v-model="reportForm.reason" type="radio" :value="reason.value" class="text-[#e85d3f] focus:ring-[#e85d3f]" />{{ reason.label }}</label></div><textarea v-model="reportForm.comment" rows="3" class="mt-4 w-full rounded-2xl border-black/10 bg-white/70 px-4 py-3 text-sm dark:border-white/10 dark:bg-white/5" placeholder="Qo‘shimcha izoh" /><p v-if="reportForm.errors.reason || reportForm.errors.comment" class="mt-2 text-xs font-semibold text-red-600">{{ reportForm.errors.reason || reportForm.errors.comment }}</p><button class="mt-4 w-full rounded-2xl bg-red-600 px-5 py-3 text-sm font-black text-white disabled:opacity-50" :disabled="reportForm.processing">{{ reportForm.processing ? 'Yuborilmoqda…' : 'Shikoyatni yuborish' }}</button></form></div>
  </AppLayout>
</template>

<script setup>
import { h, ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '../Layouts/AppLayout.vue';
import { useComparison } from '../composables/useComparison';

const props = defineProps({ listing: Object, costs: Object });
const { contains, toggle } = useComparison();
const notice = ref('');
const reportOpen = ref(false);
const reportForm = useForm({ reason: '', comment: '' });
const reportReasons = [{ value: 'incorrect', label: 'Ma’lumot noto‘g‘ri' }, { value: 'unavailable', label: 'Uy-joy mavjud emas' }, { value: 'fraud', label: 'Firibgarlikdan shubha' }, { value: 'duplicate', label: 'Takroriy e’lon' }, { value: 'other', label: 'Boshqa sabab' }];
const InfoRow = ({ label, value }) => h('div', { class: 'flex justify-between gap-4 py-3' }, [h('dt', { class: 'text-slate-500' }, label), h('dd', { class: 'text-right font-semibold text-slate-900' }, String(value))]);
InfoRow.props = ['label', 'value'];

function toggleItem() { const result = toggle(props.listing.id); notice.value = result.error || (result.added ? 'Taklif solishtirishga qo‘shildi.' : 'Taklif olib tashlandi.'); }
function submitReport() { reportForm.post(`/listings/${props.listing.id}/reports`, { preserveScroll: true, onSuccess: () => { reportOpen.value = false; notice.value = 'Shikoyat yuborildi.'; reportForm.reset(); } }); }
function money(value) { return value == null ? 'Noma’lum' : new Intl.NumberFormat('uz-UZ').format(value) + ` ${props.listing.currency}`; }
function unitLabel(value) { return { whole: 'Butun uy', room: 'Xona', bed: 'O‘rin' }[value] || 'Noma’lum'; }
function basisLabel(value) { return { monthly_unit: '/ oy', total: 'umumiy', from_total: 'dan boshlab', per_m2: '/ m²' }[value] || ''; }
function studentLabel(value) { return { yes: 'Qabul qilinadi', no: 'Qabul qilinmaydi', unknown: 'Ko‘rsatilmagan' }[value] || 'Ko‘rsatilmagan'; }
function formatDate(value) { return value ? new Intl.DateTimeFormat('uz-UZ').format(new Date(value)) : 'Noma’lum'; }
function costMode(mode, amount) { if (mode === 'none') return 'Yo‘q'; if (mode === 'included') return 'Narxga kiritilgan'; if (mode === 'fixed') return money(amount); return 'Aniqlashtiriladi'; }
</script>

<style scoped>.tag { @apply rounded-full px-3 py-1 text-xs font-bold; }</style>
