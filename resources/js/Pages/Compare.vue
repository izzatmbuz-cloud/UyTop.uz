<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="mb-6 flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-900">Solishtirish</h1>
        <Link href="/catalog" class="text-blue-600 hover:text-blue-700 font-medium">Katalogga qaytish</Link>
      </div>

      <div v-if="items.length < 2" class="bg-white rounded-2xl p-8 shadow-sm text-center">
        <p class="text-xl font-medium text-gray-700 mb-2">Kamida ikkita taklif tanlang</p>
        <p class="text-gray-500">Solishtirish uchun katalogdan 2–3 ta e’lon tanlang.</p>
      </div>

      <div v-else class="overflow-x-auto bg-white rounded-2xl shadow-sm">
        <table class="min-w-full text-left text-sm">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-4 py-3 font-semibold text-gray-700">Xususiyat</th>
              <th v-for="item in items" :key="item.id" class="px-4 py-3 font-semibold text-gray-700 min-w-[220px]">
                {{ item.title }}
              </th>
            </tr>
          </thead>
          <tbody>
            <tr class="border-t">
              <td class="px-4 py-3 font-medium text-gray-700">Narx</td>
              <td v-for="item in items" :key="`price-${item.id}`" class="px-4 py-3 text-gray-800">{{ formatPrice(item.price, item.currency) }}</td>
            </tr>
            <tr class="border-t">
              <td class="px-4 py-3 font-medium text-gray-700">Hudud</td>
              <td v-for="item in items" :key="`district-${item.id}`" class="px-4 py-3 text-gray-800">{{ item.district?.name_uz || 'Noma’lum' }}</td>
            </tr>
            <tr class="border-t">
              <td class="px-4 py-3 font-medium text-gray-700">Talabalar</td>
              <td v-for="item in items" :key="`std-${item.id}`" class="px-4 py-3 text-gray-800">{{ item.students_allowed === 'yes' ? 'Qabul qilinadi' : 'Noma’lum' }}</td>
            </tr>
            <tr class="border-t">
              <td class="px-4 py-3 font-medium text-gray-700">Bo‘sh o‘rinlar</td>
              <td v-for="item in items" :key="`free-${item.id}`" class="px-4 py-3 text-gray-800">{{ item.free_places ?? 'Noma’lum' }}</td>
            </tr>
            <tr class="border-t">
              <td class="px-4 py-3 font-medium text-gray-700">Mavjudlik</td>
              <td v-for="item in items" :key="`date-${item.id}`" class="px-4 py-3 text-gray-800">{{ item.available_from || 'Noma’lum' }}</td>
            </tr>
            <tr class="border-t">
              <td class="px-4 py-3 font-medium text-gray-700">O‘rin</td>
              <td v-for="item in items" :key="`type-${item.id}`" class="px-4 py-3 text-gray-800">{{ labelForUnit(item.rental_unit) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  items: Array,
  selectedIds: Array,
});

function formatPrice(price, currency) {
  if (!price) return 'So‘rov bo‘yicha';
  return new Intl.NumberFormat('uz-UZ', {
    style: 'currency',
    currency: currency || 'UZS',
    maximumFractionDigits: 0,
  }).format(price);
}

function labelForUnit(v) {
  return { whole: 'Butun kvartira/uy', room: 'Xona', bed: 'O‘rin' }[v] || 'Noma’lum';
}
</script>
