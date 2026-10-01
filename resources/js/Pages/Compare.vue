<template>
  <AppLayout>
    <Head title="Solishtirish" />
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
      <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
          <p class="text-xs font-bold uppercase tracking-[0.2em] text-blue-600">2–3 ta mos taklif</p>
          <h1 class="mt-2 text-4xl font-black tracking-[-0.055em] text-slate-950">Solishtirish</h1>
          <p class="mt-3 max-w-2xl text-slate-600">Narx, joylashish xarajati va yashash shartlarini bir joyda tekshiring.</p>
        </div>
        <Link href="/catalog" class="inline-flex rounded-full border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm">Yana taklif tanlash</Link>
      </div>

      <div v-if="compatibility?.message" class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
        <strong>Taqqoslash cheklovi:</strong> {{ compatibility.message }}
      </div>

      <div v-if="items.length < 2" class="rounded-[28px] border border-dashed border-slate-300 bg-white p-12 text-center shadow-sm">
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-2xl text-blue-600">⇄</div>
        <h2 class="mt-5 text-xl font-bold text-slate-900">Kamida ikkita taklif tanlang</h2>
        <p class="mt-2 text-slate-500">Katalogdan bir xil valyuta va ijara birligidagi 2–3 ta e’lon qo‘shing.</p>
      </div>

      <div v-else class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-[0_20px_55px_rgba(15,23,42,0.07)]">
        <div class="overflow-x-auto">
          <table class="min-w-[760px] w-full text-left text-sm">
            <thead class="bg-slate-950 text-white">
              <tr>
                <th class="w-44 px-5 py-5 text-xs uppercase tracking-wider text-slate-300">Xususiyat</th>
                <th v-for="item in items" :key="item.id" class="min-w-[240px] px-5 py-5 align-top">
                  <div class="flex items-start justify-between gap-3">
                    <Link :href="`/listings/${item.id}`" class="font-bold leading-5 hover:text-sky-300">{{ item.title }}</Link>
                    <button class="text-slate-400 hover:text-white" aria-label="Olib tashlash" @click="removeItem(item.id)">×</button>
                  </div>
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <CompareRow label="Oylik to‘lov" :items="items" :render="item => formatMoney(item.costs?.monthly_payment, item.currency)" emphasis />
              <CompareRow label="Joylashishda" :items="items" :render="item => formatMoney(item.costs?.movein_cost, item.currency)" emphasis />
              <CompareRow label="Aniqlanmagan" :items="items" :render="item => unknownCosts(item)" />
              <CompareRow label="Hudud" :items="items" :render="item => item.district?.name_uz || 'Noma’lum'" />
              <CompareRow label="Ijara turi" :items="items" :render="item => unitLabel(item.rental_unit)" />
              <CompareRow label="Talabalar" :items="items" :render="item => studentLabel(item.students_allowed)" />
              <CompareRow label="Yana necha kishi joylashishi mumkin?" :items="items" :render="item => item.free_places ?? 'Ko‘rsatilmagan'" />
              <CompareRow label="Qachondan ko‘chib kirish mumkin?" :items="items" :render="item => formatDate(item.available_from)" />
            </tbody>
          </table>
        </div>
      </div>
    </section>
  </AppLayout>
</template>

<script setup>
import { h } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '../Layouts/AppLayout.vue';
import { useComparison } from '../composables/useComparison';

const props = defineProps({ items: { type: Array, default: () => [] }, selectedIds: { type: Array, default: () => [] }, compatibility: Object });
const { remove, comparisonUrl } = useComparison();

const CompareRow = (rowProps) => h('tr', { class: rowProps.emphasis ? 'bg-blue-50/40' : '' }, [
  h('td', { class: 'px-5 py-4 font-semibold text-slate-600' }, rowProps.label),
  ...rowProps.items.map(item => h('td', { class: rowProps.emphasis ? 'px-5 py-4 font-bold text-slate-950' : 'px-5 py-4 text-slate-700' }, rowProps.render(item))),
]);
CompareRow.props = ['label', 'items', 'render', 'emphasis'];

function removeItem(id) { remove(id); router.visit(comparisonUrl(), { preserveScroll: true }); }
function formatMoney(value, currency) { return value == null ? 'Noma’lum' : new Intl.NumberFormat('uz-UZ').format(value) + ` ${currency}`; }
function unitLabel(value) { return { whole: 'Butun uy', room: 'Alohida xona', bed: 'Bir kishilik joy' }[value] || 'Ko‘rsatilmagan'; }
function studentLabel(value) { return { yes: 'Ha', no: 'Yo‘q', unknown: 'Ko‘rsatilmagan' }[value] || 'Ko‘rsatilmagan'; }
function formatDate(value) { return value ? new Intl.DateTimeFormat('uz-UZ').format(new Date(value)) : 'Noma’lum'; }
function unknownCosts(item) { const unknown = [...(item.costs?.unknown_monthly_items || []), ...(item.costs?.unknown_movein_items || [])]; return [...new Set(unknown)].join(', ') || 'Yo‘q'; }
</script>
