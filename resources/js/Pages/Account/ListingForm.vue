<template>
  <AppLayout>
    <Head :title="listing ? 'E’lonni tahrirlash' : 'Yangi e’lon'" />
    <section class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
      <Link href="/account/listings" class="text-sm font-bold text-[#e85d3f]">← E’lonlarim</Link>
      <div class="mt-5"><p class="eyebrow">E’lon ustasi</p><h1 class="page-title">{{ listing ? 'E’lonni tahrirlash' : 'Yangi e’lon yarating' }}</h1><p class="mt-3 text-slate-600 dark:text-slate-300">Avval qoralama saqlang yoki tayyor bo‘lsa moderatsiyaga yuboring.</p></div>
      <form class="mt-8 space-y-6" @submit.prevent>
        <section class="surface rounded-[28px] p-5 sm:p-7"><h2 class="section-title">1. Asosiy ma’lumotlar</h2><div class="mt-5 grid gap-4 sm:grid-cols-2">
          <UiSelect v-model="form.deal_type" label="Bitim turi" :options="dealOptions" :error="form.errors.deal_type" />
          <UiSelect v-model="form.property_type" label="Uy turi" :options="propertyOptions" :error="form.errors.property_type" />
          <UiSelect v-if="form.deal_type === 'rent'" v-model="form.rental_unit" label="Ijara birligi" :options="rentalOptions" :error="form.errors.rental_unit" />
          <UiSelect v-model="form.district_id" label="Hudud" :options="districtOptions" :error="form.errors.district_id" />
          <label class="sm:col-span-2"><span class="field-label">Sarlavha</span><input v-model="form.title" class="field" /><span class="error">{{ form.errors.title }}</span></label>
          <label class="sm:col-span-2"><span class="field-label">Tavsif</span><textarea v-model="form.description" rows="5" class="field resize-y" /><span class="error">{{ form.errors.description }}</span></label>
          <label class="sm:col-span-2"><span class="field-label">Manzil mo‘ljali</span><input v-model="form.location_text" class="field" placeholder="Universitet yonida" /></label>
        </div></section>

        <section class="surface rounded-[28px] p-5 sm:p-7"><h2 class="section-title">2. Narx va to‘lovlar</h2><div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
          <UiSelect v-model="form.currency" label="Valyuta" :options="currencyOptions" />
          <label><span class="field-label">Narx</span><input v-model="form.price" type="number" min="0" class="field" /><span class="error">{{ form.errors.price }}</span></label>
          <UiSelect v-model="form.price_basis" label="Narx birligi" :options="priceOptions" :error="form.errors.price_basis" />
          <UiSelect v-model="form.utilities_mode" label="Kommunal" :options="utilitiesOptions" />
          <label v-if="form.utilities_mode === 'fixed'"><span class="field-label">Kommunal summasi</span><input v-model="form.utilities_amount" type="number" min="0" class="field" /><span class="error">{{ form.errors.utilities_amount }}</span></label>
          <UiSelect v-model="form.deposit_mode" label="Depozit" :options="paymentOptions" />
          <label v-if="form.deposit_mode === 'fixed'"><span class="field-label">Depozit summasi</span><input v-model="form.deposit_amount" type="number" min="0" class="field" /></label>
          <UiSelect v-model="form.commission_mode" label="Komissiya" :options="paymentOptions" />
          <label v-if="form.commission_mode === 'fixed'"><span class="field-label">Komissiya summasi</span><input v-model="form.commission_amount" type="number" min="0" class="field" /></label>
        </div></section>

        <section class="surface rounded-[28px] p-5 sm:p-7"><h2 class="section-title">3. Uy va yashash shartlari</h2><div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
          <UiSelect v-if="form.deal_type === 'rent'" v-model="form.students_allowed" label="Talabalar" :options="studentOptions" />
          <label><span class="field-label">Xonalar</span><input v-model="form.rooms" type="number" min="1" class="field" /></label><label><span class="field-label">Maydon, m²</span><input v-model="form.area_m2" type="number" min="1" class="field" /></label>
          <label v-if="form.deal_type === 'rent'"><span class="field-label">Jami sig‘im</span><input v-model="form.capacity" type="number" min="1" class="field" /></label><label v-if="form.deal_type === 'rent'"><span class="field-label">Bo‘sh joylar</span><input v-model="form.free_places" type="number" min="0" class="field" /><span class="error">{{ form.errors.free_places }}</span></label>
          <label><span class="field-label">Qavat</span><input v-model="form.floor" type="number" min="0" class="field" /></label><label v-if="form.deal_type === 'rent'"><span class="field-label">Mavjud sana</span><input v-model="form.available_from" type="date" class="field" /></label>
          <div class="sm:col-span-2 lg:col-span-3"><span class="field-label">Qulayliklar</span><div class="flex flex-wrap gap-2"><label v-for="amenity in amenities" :key="amenity.id" class="cursor-pointer rounded-full border border-black/10 px-4 py-2 text-sm font-semibold dark:border-white/10" :class="form.amenity_ids.includes(amenity.id) ? 'bg-[#bedc79] text-[#24310d]' : ''"><input v-model="form.amenity_ids" type="checkbox" :value="amenity.id" class="sr-only" />{{ amenity.name_uz }}</label></div></div>
        </div></section>

        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end"><button type="button" class="rounded-full border border-black/10 px-6 py-3 text-sm font-black dark:border-white/10" :disabled="form.processing" @click="save(false)">Qoralama saqlash</button><button type="button" class="rounded-full bg-[#e85d3f] px-6 py-3 text-sm font-black text-white" :disabled="form.processing" @click="save(true)">Moderatsiyaga yuborish</button></div>
      </form>
    </section>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '../../Layouts/AppLayout.vue';
import UiSelect from '../../Components/UiSelect.vue';
const props = defineProps({ listing: { type: Object, default: null }, districts: { type: Array, default: () => [] }, amenities: { type: Array, default: () => [] } });
const item = props.listing;
const form = useForm({ deal_type: item?.deal_type || 'rent', rental_unit: item?.rental_unit || 'whole', property_type: item?.property_type || 'apartment', students_allowed: item?.students_allowed || 'unknown', district_id: item?.district_id || '', title: item?.title || '', description: item?.description || '', currency: item?.currency || 'UZS', price: item?.price || '', price_basis: item?.price_basis || 'monthly_unit', utilities_mode: item?.utilities_mode || 'unknown', utilities_amount: item?.utilities_amount || '', utilities_payment_timing: item?.utilities_payment_timing || 'unknown', deposit_mode: item?.deposit_mode || 'unknown', deposit_amount: item?.deposit_amount || '', commission_mode: item?.commission_mode || 'unknown', commission_amount: item?.commission_amount || '', capacity: item?.capacity || '', free_places: item?.free_places ?? '', available_from: item?.available_from?.slice(0, 10) || '', min_months: item?.min_months || '', area_m2: item?.area_m2 || '', rooms: item?.rooms || '', floor: item?.floor ?? '', location_text: item?.location_text || '', amenity_ids: item?.amenities?.map((a) => a.id) || [], submit_for_moderation: false });
const districtOptions = computed(() => props.districts.map((d) => ({ value: d.id, label: d.name_uz })));
const dealOptions = [{ value: 'rent', label: 'Ijara' }, { value: 'sale', label: 'Sotuv' }]; const propertyOptions = [{ value: 'apartment', label: 'Kvartira' }, { value: 'house', label: 'Hovli uy' }, { value: 'dormitory', label: 'Yotoqxona' }]; const rentalOptions = [{ value: 'whole', label: 'Butun uy' }, { value: 'room', label: 'Xona' }, { value: 'bed', label: 'O‘rin' }]; const currencyOptions = [{ value: 'UZS', label: 'UZS' }, { value: 'USD', label: 'USD' }]; const priceOptions = [{ value: 'monthly_unit', label: 'Oyiga' }, { value: 'total', label: 'Umumiy' }, { value: 'from_total', label: 'Boshlang‘ich narx' }, { value: 'per_m2', label: '1 m² uchun' }, { value: 'on_request', label: 'So‘rov bo‘yicha' }]; const utilitiesOptions = [{ value: 'included', label: 'Narx ichida' }, { value: 'fixed', label: 'Alohida summa' }, { value: 'unknown', label: 'Aniqlashtiriladi' }]; const paymentOptions = [{ value: 'none', label: 'Yo‘q' }, { value: 'fixed', label: 'Belgilangan summa' }, { value: 'unknown', label: 'Aniqlashtiriladi' }]; const studentOptions = [{ value: 'yes', label: 'Ha' }, { value: 'no', label: 'Yo‘q' }, { value: 'unknown', label: 'Aniqlashtiriladi' }];
function save(publish) {
  form.transform((data) => ({ ...data, submit_for_moderation: publish }));
  if (item) form.put(`/account/listings/${item.id}`, { preserveScroll: true });
  else form.post('/account/listings', { preserveScroll: true });
}
</script>

<style scoped>
.field-label { @apply mb-1.5 block text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400; }
.field { @apply w-full rounded-2xl border-black/10 bg-white/75 px-4 py-3 text-sm font-semibold text-slate-800 outline-none transition focus:border-[#e85d3f] focus:ring-4 focus:ring-[#e85d3f]/10 dark:border-white/10 dark:bg-white/5 dark:text-white; }
.error { @apply mt-1 block text-xs font-semibold text-red-600; }
.section-title { @apply text-xl font-black tracking-[-0.03em] text-slate-950 dark:text-white; }
</style>
