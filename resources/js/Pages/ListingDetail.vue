<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div v-if="listing" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="mb-6 flex items-center justify-between">
        <Link href="/catalog" class="text-blue-600 hover:text-blue-700 font-medium">← Katalogga qaytish</Link>
        <button @click="toggleCompare" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
          {{ inCompare ? 'Solishtirishdan olib tashlash' : 'Solishtirishga qo‘shish' }}
        </button>
      </div>

      <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2">
          <div class="p-4">
            <div class="h-[420px] rounded-xl bg-gray-200 overflow-hidden">
              <img v-if="listing.media?.length" :src="`/storage/${listing.media[0].storage_path}`" :alt="listing.title" class="w-full h-full object-cover" />
              <div v-else class="w-full h-full flex items-center justify-center text-gray-500">Rasm mavjud emas</div>
            </div>
          </div>

          <div class="p-6 lg:p-8">
            <div class="flex items-center gap-3 mb-3">
              <span class="rounded-full bg-blue-100 text-blue-700 text-xs font-medium px-2 py-1">{{ labelForDeal(listing.deal_type) }}</span>
              <span class="rounded-full bg-green-100 text-green-700 text-xs font-medium px-2 py-1">{{ labelForUnit(listing.rental_unit) }}</span>
            </div>

            <h1 class="text-3xl font-bold text-gray-900 mb-3">{{ listing.title }}</h1>

            <div class="text-lg font-semibold text-blue-600 mb-4">
              {{ formatPrice(listing.price, listing.currency) }}
              <span class="text-sm text-gray-500 font-normal">{{ listing.price_basis ? `(${priceBasisLabel(listing.price_basis)})` : '' }}</span>
            </div>

            <div class="grid grid-cols-2 gap-4 text-sm text-gray-700 mb-6">
              <div><span class="text-gray-500">Hudud:</span> {{ listing.district?.name_uz || 'Noma’lum' }}</div>
              <div><span class="text-gray-500">Mavjudlik:</span> {{ listing.available_from || 'Tez orada' }}</div>
              <div><span class="text-gray-500">Xonalar:</span> {{ listing.rooms || 'Noma’lum' }}</div>
              <div><span class="text-gray-500">Bo‘sh o‘rinlar:</span> {{ listing.free_places ?? 'Noma’lum' }}</div>
            </div>

            <p class="text-gray-700 leading-7 mb-6">{{ listing.description }}</p>

            <div class="flex flex-wrap gap-3 mb-8">
              <Link :href="`/listings/${listing.id}/request`" class="px-5 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">Ko‘rish uchun murojaat</Link>
              <Link href="/compare" class="px-5 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium">Solishtirish</Link>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 rounded-xl p-4">
              <div>
                <h2 class="text-lg font-bold text-gray-900 mb-3">Xarajatlar</h2>
                <ul class="space-y-2 text-sm text-gray-700">
                  <li>Ijara: {{ formatPrice(listing.price, listing.currency) }}</li>
                  <li>Kommunal: {{ listing.utilities_mode ? costLabel(listing.utilities_mode, listing.utilities_amount) : 'Ko‘rsatilmagan' }}</li>
                  <li>Depozit: {{ listing.deposit_mode ? depositLabel(listing.deposit_mode, listing.deposit_amount) : 'Ko‘rsatilmagan' }}</li>
                  <li>Komissiya: {{ listing.commission_mode ? commissionLabel(listing.commission_mode, listing.commission_amount) : 'Ko‘rsatilmagan' }}</li>
                </ul>
              </div>

              <div>
                <h2 class="text-lg font-bold text-gray-900 mb-3">Shartlar</h2>
                <ul class="space-y-2 text-sm text-gray-700">
                  <li>Talabalar: {{ listing.students_allowed === 'yes' ? 'Qabul qilinadi' : listing.students_allowed === 'no' ? 'Qabul qilinmaydi' : 'Ko‘rsatilmagan' }}</li>
                  <li>Hudud: {{ listing.location_text || 'Noma’lum' }}</li>
                  <li>Muallif turi: {{ listing.author_type || 'Noma’lum' }}</li>
                  <li>Manba: {{ listing.source_type || 'Noma’lum' }}</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
  listing: Object,
});

const selected = computed(() => {
  const ids = localStorage.getItem('compare_ids');
  return ids ? JSON.parse(ids) : [];
});

const inCompare = computed(() => props.listing && selected.value.includes(Number(props.listing.id)));

function formatPrice(price, currency) {
  if (!price) return 'So‘rov bo‘yicha';
  return new Intl.NumberFormat('uz-UZ', {
    style: 'currency',
    currency: currency || 'UZS',
    maximumFractionDigits: 0,
  }).format(price);
}

function labelForDeal(v) {
  return { rent: 'Ijara', sale: 'Sotish' }[v] || 'Noma’lum';
}

function labelForUnit(v) {
  return { whole: 'Butun kvartira/uy', room: 'Xona', bed: 'O‘rin' }[v] || 'Noma’lum';
}

function priceBasisLabel(v) {
  return { monthly_unit: 'oyiga', total: 'umumiy', per_m2: 'm² uchun', on_request: 'so‘rov bo‘yicha' }[v] || '';
}

function costLabel(mode, amount) {
  if (mode === 'included') return 'Narxga kiritilgan';
  if (mode === 'fixed') return formatPrice(amount, props.listing?.currency || 'UZS');
  return 'Noma’lum';
}

function depositLabel(mode, amount) {
  if (mode === 'none') return 'Yo‘q';
  if (mode === 'fixed') return formatPrice(amount, props.listing?.currency || 'UZS');
  return 'Noma’lum';
}

function commissionLabel(mode, amount) {
  if (mode === 'none') return 'Yo‘q';
  if (mode === 'fixed') return formatPrice(amount, props.listing?.currency || 'UZS');
  return 'Noma’lum';
}

function toggleCompare() {
  const items = JSON.parse(localStorage.getItem('compare_ids') || '[]');
  const id = Number(props.listing.id);
  const idx = items.indexOf(id);

  if (idx >= 0) {
    items.splice(idx, 1);
  } else {
    if (items.length >= 3) {
      items.shift();
    }
    items.push(id);
  }

  localStorage.setItem('compare_ids', JSON.stringify(items));
  window.location.reload();
}
</script>
