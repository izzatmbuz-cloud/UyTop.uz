<template>
  <AppLayout>
    <Head :title="listing ? 'E’lonni tahrirlash' : 'Yangi e’lon'" />
    <section class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
      <Link href="/account/listings" class="text-sm font-bold text-[#e85d3f]">← E’lonlarim</Link>
      <div v-if="$page.props.flash?.error" class="mt-5 rounded-2xl bg-red-100 px-4 py-3 text-sm font-semibold text-red-900 dark:bg-red-950 dark:text-red-200">{{ $page.props.flash.error }}</div>
      <div class="mt-5"><p class="eyebrow">E’lon ustasi</p><h1 class="page-title">{{ listing ? 'E’lonni tahrirlash' : 'Yangi e’lon yarating' }}</h1><p class="mt-3 text-slate-600 dark:text-slate-300">Avval qoralama saqlang yoki tayyor bo‘lsa moderatsiyaga yuboring.</p></div>
      <section class="mt-8 overflow-hidden rounded-[28px] border border-[#e85d3f]/20 bg-gradient-to-br from-[#fff5ef] to-white p-5 dark:from-[#2a1814] dark:to-slate-900 sm:p-7">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between"><div><p class="eyebrow">AI yordamchi</p><h2 class="section-title mt-1">Telegram matnidan to‘ldirish</h2><p class="mt-2 max-w-2xl text-sm text-slate-600 dark:text-slate-300">E’lon matnini kiriting. AI faqat matnda bor ma’lumotlarni taklif qiladi, yakuniy tekshiruv sizda qoladi.</p></div><span class="w-fit rounded-full bg-[#bedc79] px-3 py-1 text-xs font-black text-[#24310d]">Beta</span></div>
        <textarea v-model="aiText" rows="5" class="field mt-5 resize-y" placeholder="Andijon shahar, universitet yonida 3 xonali kvartiraga..." />
        <p v-if="aiError" class="error">{{ aiError }}</p>
        <div v-if="reviewFields.length" class="mt-4 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900"><strong>Tekshirish kerak:</strong><ul class="mt-2 list-disc space-y-1 pl-5"><li v-for="note in reviewFields" :key="note">{{ note }}</li></ul></div>
        <button type="button" class="mt-4 rounded-full bg-slate-950 px-5 py-3 text-sm font-black text-white disabled:cursor-wait disabled:opacity-60 dark:bg-white dark:text-slate-950" :disabled="aiLoading || aiText.trim().length < 20" @click="parseWithAi">{{ aiLoading ? 'Tahlil qilinmoqda…' : 'Matndan to‘ldirish' }}</button>
      </section>
      <form class="mt-8 space-y-6" @submit.prevent>
        <section class="surface rounded-[28px] p-5 sm:p-7"><h2 class="section-title">1. Asosiy ma’lumotlar</h2><div class="mt-5 grid gap-4 sm:grid-cols-2">
          <UiSelect v-model="form.deal_type" label="Bitim turi" :options="dealOptions" :error="form.errors.deal_type" />
          <UiSelect v-model="form.property_type" label="Uy turi" :options="propertyOptions" :error="form.errors.property_type" />
          <UiSelect v-if="form.deal_type === 'rent'" v-model="form.rental_unit" label="Ijara birligi" :options="rentalOptions" :error="form.errors.rental_unit" />
          <UiSelect v-model="form.district_id" label="Hudud" :options="districtOptions" :error="form.errors.district_id" />
          <label class="sm:col-span-2"><span class="field-label">Sarlavha</span><input v-model="form.title" class="field" /><span class="error">{{ form.errors.title }}</span></label>
          <label class="sm:col-span-2"><span class="field-label">Tavsif</span><textarea v-model="form.description" rows="5" class="field resize-y" /><span class="error">{{ form.errors.description }}</span></label>
          <label class="sm:col-span-2"><span class="field-label">Mahalla, kichik daha yoki mo‘ljal</span><input v-model="form.location_text" list="andijan-areas" class="field" placeholder="Masalan: Eski shahar, universitet yonida" /><datalist id="andijan-areas"><option v-for="area in localities" :key="area" :value="area" /></datalist></label>
        </div></section>

        <section class="surface rounded-[28px] p-5 sm:p-7"><h2 class="section-title">2. Narx va to‘lovlar</h2><div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
          <UiSelect v-model="form.currency" label="Valyuta" :options="currencyOptions" />
          <label><span class="field-label">Narx</span><input v-model="form.price" type="number" min="0" class="field" /><span class="error">{{ form.errors.price }}</span></label>
          <UiSelect v-model="form.price_basis" label="Narx birligi" :options="priceOptions" :error="form.errors.price_basis" />
          <UiSelect v-model="form.utilities_mode" label="Kommunal" :options="utilitiesOptions" />
          <label v-if="form.utilities_mode === 'fixed'"><span class="field-label">Kommunal summasi</span><input v-model="form.utilities_amount" type="number" min="0" class="field" /><span class="error">{{ form.errors.utilities_amount }}</span></label>
          <UiSelect v-if="form.deal_type === 'rent'" v-model="form.utilities_payment_timing" label="Kommunal to‘lov vaqti" :options="utilitiesTimingOptions" />
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
          <label v-if="form.deal_type === 'rent'"><span class="field-label">Minimal muddat, oy</span><input v-model="form.min_months" type="number" min="1" class="field" /></label>
          <div class="sm:col-span-2 lg:col-span-3"><span class="field-label">Qulayliklar</span><div class="flex flex-wrap gap-2"><label v-for="amenity in amenities" :key="amenity.id" class="cursor-pointer rounded-full border border-black/10 px-4 py-2 text-sm font-semibold dark:border-white/10" :class="form.amenity_ids.includes(amenity.id) ? 'bg-[#bedc79] text-[#24310d]' : ''"><input v-model="form.amenity_ids" type="checkbox" :value="amenity.id" class="sr-only" />{{ amenity.name_uz }}</label></div></div>
        </div></section>

        <section class="surface rounded-[28px] p-5 sm:p-7"><div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between"><div><h2 class="section-title">4. Rasmlar</h2><p class="mt-1 text-sm text-slate-500">JPEG, PNG yoki WebP · har biri 5 MBgacha · jami 8 ta.</p></div><label class="cursor-pointer rounded-full bg-[#bedc79] px-5 py-2.5 text-sm font-black text-[#24310d]" :class="totalImages >= 8 ? 'pointer-events-none opacity-50' : ''"><input :key="fileInputKey" type="file" accept="image/jpeg,image/png,image/webp" multiple class="sr-only" @change="selectImages" />+ Rasm tanlash</label></div>
          <div v-if="existingImages.length || previews.length" class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-4"><div v-for="image in existingImages" :key="`saved-${image.id}`" class="group relative aspect-[4/3] overflow-hidden rounded-2xl bg-slate-100"><img :src="`/storage/${image.storage_path}`" alt="E’lon rasmi" class="h-full w-full object-cover" /><button type="button" class="absolute right-2 top-2 rounded-full bg-slate-950/75 px-2.5 py-1 text-xs font-black text-white opacity-100 sm:opacity-0 sm:transition sm:group-hover:opacity-100" @click="removeExisting(image.id)">O‘chirish</button></div><div v-for="(preview, index) in previews" :key="preview.url" class="group relative aspect-[4/3] overflow-hidden rounded-2xl bg-slate-100"><img :src="preview.url" alt="Yangi rasm ko‘rinishi" class="h-full w-full object-cover" /><span class="absolute bottom-2 left-2 rounded-full bg-[#bedc79] px-2 py-1 text-[10px] font-black text-[#24310d]">Yangi</span><button type="button" class="absolute right-2 top-2 rounded-full bg-slate-950/75 px-2.5 py-1 text-xs font-black text-white" @click="removeSelected(index)">O‘chirish</button></div></div>
          <div v-else class="mt-5 rounded-2xl border-2 border-dashed border-black/10 p-8 text-center text-sm text-slate-500 dark:border-white/10">Hozircha rasm tanlanmagan. Qoralamani rasmsiz saqlash mumkin.</div><span class="error">{{ form.errors.images }}</span>
        </section>

        <div v-if="form.processing" class="rounded-2xl bg-blue-50 px-4 py-3 text-sm font-semibold text-blue-800 dark:bg-blue-950 dark:text-blue-200">Rasmlar qayta ishlanmoqda… {{ form.progress ? `${form.progress.percentage}%` : '' }} Sahifani yopmang.</div>
        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end"><button type="button" class="rounded-full border border-black/10 px-6 py-3 text-sm font-black dark:border-white/10" :disabled="form.processing" @click="save(false)">{{ form.processing ? 'Saqlanmoqda…' : 'Qoralama saqlash' }}</button><button type="button" class="rounded-full bg-[#e85d3f] px-6 py-3 text-sm font-black text-white disabled:opacity-60" :disabled="form.processing" @click="save(true)">{{ form.processing ? 'Yuborilmoqda…' : 'Moderatsiyaga yuborish' }}</button></div>
      </form>
    </section>
  </AppLayout>
</template>

<script setup>
import { computed, onBeforeUnmount, ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import AppLayout from '../../Layouts/AppLayout.vue';
import UiSelect from '../../Components/UiSelect.vue';
const props = defineProps({ listing: { type: Object, default: null }, districts: { type: Array, default: () => [] }, amenities: { type: Array, default: () => [] }, localities: { type: Array, default: () => [] } });
const item = props.listing;
const form = useForm({ deal_type: item?.deal_type || 'rent', rental_unit: item?.rental_unit || 'whole', property_type: item?.property_type || 'apartment', students_allowed: item?.students_allowed || 'unknown', district_id: item?.district_id || '', title: item?.title || '', description: item?.description || '', currency: item?.currency || 'UZS', price: item?.price || '', price_basis: item?.price_basis || 'monthly_unit', utilities_mode: item?.utilities_mode || 'unknown', utilities_amount: item?.utilities_amount || '', utilities_payment_timing: item?.utilities_payment_timing || 'unknown', deposit_mode: item?.deposit_mode || 'unknown', deposit_amount: item?.deposit_amount || '', commission_mode: item?.commission_mode || 'unknown', commission_amount: item?.commission_amount || '', capacity: item?.capacity || '', free_places: item?.free_places ?? '', available_from: item?.available_from?.slice(0, 10) || '', min_months: item?.min_months || '', area_m2: item?.area_m2 || '', rooms: item?.rooms || '', floor: item?.floor ?? '', location_text: item?.location_text || '', amenity_ids: item?.amenities?.map((a) => a.id) || [], images: [], submit_for_moderation: false });
const existingImages = ref([...(item?.media || [])]); const previews = ref([]); const fileInputKey = ref(0); const totalImages = computed(() => existingImages.value.length + form.images.length);
const aiText = ref(''); const aiLoading = ref(false); const aiError = ref(''); const reviewFields = ref([]);
const districtOptions = computed(() => props.districts.map((d) => ({ value: d.id, label: d.name_uz })));
const dealOptions = [{ value: 'rent', label: 'Ijara' }, { value: 'sale', label: 'Sotuv' }]; const propertyOptions = [{ value: 'apartment', label: 'Kvartira' }, { value: 'house', label: 'Hovli uy' }, { value: 'dormitory', label: 'Yotoqxona' }]; const rentalOptions = [{ value: 'whole', label: 'Butun uy' }, { value: 'room', label: 'Xona' }, { value: 'bed', label: 'O‘rin' }]; const currencyOptions = [{ value: 'UZS', label: 'UZS' }, { value: 'USD', label: 'USD' }]; const priceOptions = [{ value: 'monthly_unit', label: 'Oyiga' }, { value: 'total', label: 'Umumiy' }, { value: 'from_total', label: 'Boshlang‘ich narx' }, { value: 'per_m2', label: '1 m² uchun' }, { value: 'on_request', label: 'So‘rov bo‘yicha' }]; const utilitiesOptions = [{ value: 'included', label: 'Narx ichida' }, { value: 'fixed', label: 'Alohida summa' }, { value: 'unknown', label: 'Aniqlashtiriladi' }]; const paymentOptions = [{ value: 'none', label: 'Yo‘q' }, { value: 'fixed', label: 'Belgilangan summa' }, { value: 'unknown', label: 'Aniqlashtiriladi' }]; const studentOptions = [{ value: 'yes', label: 'Ha' }, { value: 'no', label: 'Yo‘q' }, { value: 'unknown', label: 'Aniqlashtiriladi' }];
const utilitiesTimingOptions = [{ value: 'move_in', label: 'Ko‘chib kirishda' }, { value: 'later', label: 'Keyinroq' }, { value: 'unknown', label: 'Aniqlashtiriladi' }];
async function parseWithAi() {
  aiLoading.value = true; aiError.value = ''; reviewFields.value = [];
  try {
    const { data } = await axios.post('/account/listings/ai-parse', { text: aiText.value });
    const allowed = ['deal_type', 'rental_unit', 'property_type', 'students_allowed', 'title', 'description', 'location_text', 'currency', 'price', 'price_basis', 'utilities_mode', 'utilities_amount', 'deposit_mode', 'deposit_amount', 'commission_mode', 'commission_amount', 'capacity', 'free_places', 'rooms', 'area_m2'];
    allowed.forEach((key) => { if (data[key] !== null && data[key] !== undefined) form[key] = data[key]; });
    form.amenity_ids = (data.amenities || []).map((code) => props.amenities.find((item) => item.code === code)?.id).filter(Boolean);
    data.review_fields ||= [];
    if (data.district_name) { const district = props.districts.find((item) => item.name_uz.toLowerCase().includes(data.district_name.toLowerCase()) || data.district_name.toLowerCase().includes(item.name_uz.toLowerCase())); if (district) form.district_id = district.id; else data.review_fields.push(`Hudud topilmadi: ${data.district_name}`); }
    reviewFields.value = data.review_fields || [];
  } catch (error) { aiError.value = error.response?.data?.message || 'AI xizmatiga ulanib bo‘lmadi. Keyinroq urinib ko‘ring.'; }
  finally { aiLoading.value = false; }
}
function save(publish) {
  form.transform((data) => ({ ...data, submit_for_moderation: publish, ...(item ? { _method: 'put' } : {}) }));
  form.post(item ? `/account/listings/${item.id}` : '/account/listings', { preserveScroll: true, forceFormData: true });
}
function selectImages(event) { const available = Math.max(0, 8 - totalImages.value); const files = Array.from(event.target.files || []).slice(0, available); form.images.push(...files); previews.value.push(...files.map((file) => ({ file, url: URL.createObjectURL(file) }))); fileInputKey.value += 1; }
function removeSelected(index) { URL.revokeObjectURL(previews.value[index].url); previews.value.splice(index, 1); form.images.splice(index, 1); }
function removeExisting(id) { if (!window.confirm('Rasmni o‘chirasizmi?')) return; router.delete(`/account/listings/${item.id}/images/${id}`, { preserveScroll: true, onSuccess: () => { existingImages.value = existingImages.value.filter((image) => image.id !== id); } }); }
onBeforeUnmount(() => previews.value.forEach((preview) => URL.revokeObjectURL(preview.url)));
</script>

<style scoped>
.field-label { @apply mb-1.5 block text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400; }
.field { @apply w-full rounded-2xl border-black/10 bg-white/75 px-4 py-3 text-sm font-semibold text-slate-800 outline-none transition focus:border-[#e85d3f] focus:ring-4 focus:ring-[#e85d3f]/10 dark:border-white/10 dark:bg-white/5 dark:text-white; }
.error { @apply mt-1 block text-xs font-semibold text-red-600; }
.section-title { @apply text-xl font-black tracking-[-0.03em] text-slate-950 dark:text-white; }
</style>
