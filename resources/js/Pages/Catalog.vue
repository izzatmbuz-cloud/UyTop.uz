<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Page title -->
      <h1 class="text-3xl font-bold text-gray-900 mb-8">Katalog</h1>

      <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Filters sidebar -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-lg shadow-sm p-6 sticky top-4">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Filtrlar</h2>

            <!-- Deal type -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-2">Bo'lim</label>
              <select v-model="filters.deal_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600">
                <option value="">Hammasi</option>
                <option value="rent">Ijara</option>
                <option value="sale">Sotish</option>
              </select>
            </div>

            <!-- Rental unit -->
            <div class="mb-6" v-if="filters.deal_type === 'rent'">
              <label class="block text-sm font-medium text-gray-700 mb-2">Tur</label>
              <select v-model="filters.rental_unit" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600">
                <option value="">Hammasi</option>
                <option value="whole">Butun kvartira/uy</option>
                <option value="room">Xona</option>
                <option value="bed">O'rin</option>
              </select>
            </div>

            <!-- Students allowed -->
            <div class="mb-6">
              <label class="flex items-center gap-2">
                <input type="checkbox" v-model="filters.students_allowed" :value="'yes'" class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                <span class="text-sm text-gray-700">Talabalar qabul qilinadi</span>
              </label>
            </div>

            <!-- District -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-2">Hududlar</label>
              <select v-model="filters.district_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600">
                <option value="">Tanlang...</option>
                <option v-for="(name, id) in districts" :key="id" :value="id">{{ name }}</option>
              </select>
            </div>

            <!-- Price range -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-2">Narx oralig'i</label>
              <select v-model="filters.currency" class="w-full px-3 py-2 border border-gray-300 rounded-lg mb-2 text-sm focus:ring-2 focus:ring-blue-600">
                <option value="UZS">UZS</option>
                <option value="USD">USD</option>
              </select>
              <input type="number" v-model="filters.price_min" placeholder="Eng kam" class="w-full px-3 py-2 border border-gray-300 rounded-lg mb-2 text-sm" />
              <input type="number" v-model="filters.price_max" placeholder="Eng ko'p" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" />
            </div>

            <!-- Buttons -->
            <div class="flex gap-2">
              <button @click="applyFilters" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                Qidiruv
              </button>
              <button @click="clearFilters" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-sm font-medium">
                Tozalash
              </button>
            </div>
          </div>
        </div>

        <!-- Listings -->
        <div class="lg:col-span-3">
          <!-- Sort -->
          <div class="mb-6 flex justify-between items-center">
            <p class="text-sm text-gray-600">
              {{ listings.total }} ta taklif topildi
            </p>
            <select v-model="filters.sort" @change="applyFilters" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-600">
              <option value="confirmed_at">Yangi avval</option>
              <option value="price_asc">Narx past avval</option>
              <option value="price_desc">Narx yuqori avval</option>
              <option value="date">Nashr sanasi</option>
            </select>
          </div>

          <!-- Empty state -->
          <div v-if="listings.data.length === 0" class="text-center py-12">
            <p class="text-gray-500 text-lg mb-4">Hech qanday taklif topilmadi</p>
            <button @click="clearFilters" class="px-4 py-2 text-blue-600 hover:text-blue-700 transition font-medium">
              Filtrlarni tozalash
            </button>
          </div>

          <!-- Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div v-for="listing in listings.data" :key="listing.id" @click="goToListing(listing.id)" class="bg-white rounded-lg shadow hover:shadow-lg transition cursor-pointer overflow-hidden">
              <!-- Image -->
              <div class="w-full h-48 bg-gray-200 overflow-hidden">
                <img v-if="listing.media.length" :src="`/storage/${listing.media[0].storage_path}`" :alt="listing.title" class="w-full h-full object-cover" />
                <div v-else class="w-full h-full flex items-center justify-center bg-gray-300">
                  <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                </div>
              </div>

              <!-- Content -->
              <div class="p-4">
                <h3 class="font-bold text-gray-900 mb-1 line-clamp-2">{{ listing.title }}</h3>
                
                <div class="flex items-center gap-2 mb-2 text-sm text-gray-600">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                  </svg>
                  {{ listing.district.name_uz }}
                </div>

                <!-- Price -->
                <div class="mb-3 font-bold text-lg text-blue-600">
                  {{ formatPrice(listing.price, listing.currency) }}
                  <span class="text-xs font-normal text-gray-600 ml-1">{{ getPriceBasis(listing.price_basis) }}</span>
                </div>

                <!-- Details -->
                <div class="space-y-1 text-xs text-gray-600 mb-3">
                  <div v-if="listing.rooms">{{ listing.rooms }} xona</div>
                  <div v-if="listing.free_places">{{ listing.free_places }} o'rin</div>
                  <div v-if="listing.students_allowed === 'yes'" class="flex items-center gap-1 text-blue-600">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M6 2a2 2 0 11-4 0 2 2 0 014 0zm10 0a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Talabalar
                  </div>
                </div>

                <!-- Status -->
                <div class="flex gap-2">
                  <Link :href="`/listings/${listing.id}`" class="flex-1 px-3 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition text-center font-medium">
                    Ko'rish
                  </Link>
                  <button @click.stop="compareRequest(listing.id)" class="flex-1 px-3 py-2 bg-gray-100 text-gray-700 text-sm rounded hover:bg-gray-200 transition font-medium">
                    Solishtirish
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Pagination -->
          <div v-if="listings.total > listings.per_page" class="flex justify-center gap-2 mt-8">
            <Link v-for="link in listings.links" :key="link.url" :href="link.url || '#'" :class="[
              'px-3 py-2 rounded text-sm font-medium',
              link.active ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50',
            ]" v-html="link.label" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
  listings: Object,
  districts: Object,
  filters: Object,
});

const filters = ref({
  deal_type: '',
  rental_unit: '',
  students_allowed: false,
  district_id: '',
  price_min: '',
  price_max: '',
  currency: 'UZS',
  sort: 'confirmed_at',
  search: '',
});

function formatPrice(price, currency) {
  if (!price) return 'So\'rov bo\'yicha';
  return new Intl.NumberFormat('uz-UZ', {
    style: 'currency',
    currency: currency,
    maximumFractionDigits: 0,
  }).format(price);
}

function getPriceBasis(basis) {
  const map = {
    'monthly_unit': 'oyiga',
    'total': 'umumiy',
    'per_m2': 'm² uchun',
    'on_request': 'so\'rov bo\'yicha',
  };
  return map[basis] || '';
}

function applyFilters() {
  router.get('/catalog', filters.value);
}

function clearFilters() {
  filters.value = {
    deal_type: '',
    rental_unit: '',
    students_allowed: false,
    district_id: '',
    price_min: '',
    price_max: '',
    currency: 'UZS',
    sort: 'confirmed_at',
  };
  applyFilters();
}

function goToListing(id) {
  router.visit(`/listings/${id}`);
}

function compareRequest(id) {
  console.log('Compare:', id);
}
</script>
