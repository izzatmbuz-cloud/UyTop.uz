<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-sm p-6 sm:p-8">
      <h1 class="text-3xl font-bold text-gray-900 mb-2">Murojaat yuborish</h1>
      <p class="text-gray-600 mb-6">{{ listing?.title }} uchun ariza yuborish</p>

      <form @submit.prevent="submitRequest" class="space-y-5">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Ism</label>
          <input v-model="form.name" class="w-full border border-gray-300 rounded-lg px-3 py-2" required />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
          <input v-model="form.phone" class="w-full border border-gray-300 rounded-lg px-3 py-2" required />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Ko‘rish sanasi</label>
            <input type="date" v-model="form.proposed_at" class="w-full border border-gray-300 rounded-lg px-3 py-2" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Yashovchilar soni</label>
            <input type="number" min="1" v-model="form.occupants_count" class="w-full border border-gray-300 rounded-lg px-3 py-2" />
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Xabar</label>
          <textarea v-model="form.message" rows="4" class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="Qisqacha ma’lumot..." />
        </div>

        <div class="flex items-center justify-between pt-2">
          <Link href="/catalog" class="text-gray-600 hover:text-gray-700">Bekor qilish</Link>
          <button :disabled="submitting" class="px-5 py-3 rounded-lg bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-60">
            {{ submitting ? 'Yuborilmoqda...' : 'Yuborish' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import { reactive, ref } from 'vue';

const props = defineProps({
  listing: Object,
});

const submitting = ref(false);
const form = reactive({
  name: '',
  phone: '',
  proposed_at: '',
  occupants_count: 1,
  message: '',
});

async function submitRequest() {
  submitting.value = true;

  try {
    const response = await fetch(`/listings/${props.listing.id}/requests`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
      body: JSON.stringify(form),
    });

    const data = await response.json();

    if (!response.ok) {
      throw new Error(data?.message || data?.error || 'Xatolik yuz berdi');
    }

    router.visit('/account/requests');
  } catch (error) {
    alert(error.message || 'Yuborishda xatolik bo‘ldi');
  } finally {
    submitting.value = false;
  }
}
</script>
