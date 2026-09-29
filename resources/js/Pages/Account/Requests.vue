<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Mening murojaatlarim</h1>
        <Link href="/catalog" class="text-blue-600 hover:text-blue-700 font-medium">Katalogga qaytish</Link>
      </div>

      <div v-if="requests.length === 0" class="bg-white rounded-2xl shadow-sm p-8 text-center text-gray-600">
        Hozircha murojaatlar yo‘q.
      </div>

      <div v-else class="space-y-4">
        <div v-for="request in requests" :key="request.id" class="bg-white rounded-2xl shadow-sm p-5">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
              <div class="text-lg font-semibold text-gray-900">{{ request.listing?.title || 'E’lon' }}</div>
              <div class="text-sm text-gray-500">{{ request.listing?.district?.name_uz || 'Hudud' }}</div>
            </div>
            <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">{{ statusLabel(request.status) }}</span>
          </div>

          <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-3 text-sm text-gray-700">
            <div><span class="text-gray-500">Telefon:</span> {{ request.phone }}</div>
            <div><span class="text-gray-500">Sana:</span> {{ request.proposed_at || 'Aniqlanmagan' }}</div>
            <div><span class="text-gray-500">Yashovchilar:</span> {{ request.occupants_count || '-' }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  requests: Array,
});

function statusLabel(status) {
  return {
    new: 'Yangi',
    accepted: 'Qabul qilindi',
    alternative_proposed: 'Boshqa vaqt taklif qilindi',
    rejected: 'Rad etildi',
    cancelled: 'Bekor qilindi',
    completed: 'Yakunlangan',
  }[status] || status;
}
</script>
