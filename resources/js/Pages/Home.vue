<template>
  <div class="min-h-screen bg-[#f5f7fb] text-slate-800">
    <header class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/75 backdrop-blur-xl">
      <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-3">
          <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[radial-gradient(circle_at_top,#60a5fa,#1d4ed8_55%,#0f172a)] text-lg font-black text-white shadow-[0_14px_28px_rgba(37,99,235,0.38)]">
            U
          </div>
          <div>
            <div class="text-xl font-black tracking-[-0.06em] text-slate-900">UyTop</div>
            <div class="-mt-0.5 text-[10px] font-semibold uppercase tracking-[0.2em] text-slate-400">Andijon</div>
          </div>
        </div>

        <nav class="hidden items-center gap-8 text-sm font-medium text-slate-600 md:flex">
          <Link href="/catalog" class="transition hover:text-slate-900">Katalog</Link>
          <Link href="/compare" class="transition hover:text-slate-900">Solishtirish</Link>
          <Link href="/projects" class="transition hover:text-slate-900">Yangi qurilishlar</Link>
          <Link href="/login" class="transition hover:text-slate-900">Kirish</Link>
        </nav>

        <div class="flex items-center gap-3">
          <Link href="/catalog" class="hidden rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:text-slate-900 sm:inline-flex">
            Katalog
          </Link>
          <Link href="/login" class="inline-flex rounded-full bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow-[0_18px_30px_rgba(15,23,42,0.18)] transition hover:-translate-y-0.5 hover:bg-slate-800">
            E'lon joylashtirish
          </Link>
        </div>
      </div>
    </header>

    <main>
      <section class="relative overflow-hidden border-b border-slate-200 bg-[#071a2d] text-white">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_15%_20%,rgba(59,130,246,0.28),transparent_22%),radial-gradient(circle_at_80%_0%,rgba(14,165,233,0.18),transparent_25%),linear-gradient(135deg,#071a2d_0%,#0d2340_38%,#112a4d_100%)]" />
        <div class="absolute left-1/2 top-0 h-[540px] w-[540px] -translate-x-1/2 rounded-full bg-[radial-gradient(circle,rgba(56,189,248,0.24),transparent_60%)] blur-3xl" />

        <div class="relative mx-auto grid max-w-7xl gap-12 px-4 py-20 sm:px-6 lg:grid-cols-[1.3fr_0.7fr] lg:px-8 lg:py-24">
          <div class="pt-4">
            <div class="mb-6 inline-flex items-center gap-3 rounded-full border border-white/10 bg-white/5 px-3 py-1.5 text-[11px] font-semibold uppercase tracking-[0.18em] text-blue-100 backdrop-blur-sm">
              <span class="h-2.5 w-2.5 rounded-full bg-emerald-400 shadow-[0_0_18px_rgba(52,211,153,0.8)]" />
              Andijon uchun uy-joy platformasi
            </div>

            <h1 class="max-w-xl text-4xl font-black tracking-[-0.075em] text-white sm:text-5xl lg:text-7xl">
              O'zingizga mos
              <span class="block text-transparent bg-gradient-to-r from-sky-300 via-blue-200 to-cyan-100 bg-clip-text">
                uy-joyni toping
              </span>
            </h1>

            <p class="mt-6 max-w-xl text-lg leading-8 text-slate-200/90">
              Talabalar, oilalar va ijaraga mo'ljallangan uy-joylarni tez qidirish, cheklovlar asosida solishtirish va murojaat qilish uchun qulay platforma.
            </p>

            <div class="mt-8 flex flex-col gap-4 sm:flex-row">
              <Link href="/catalog" class="inline-flex items-center justify-center rounded-full bg-white px-6 py-3.5 text-sm font-semibold text-slate-900 shadow-[0_18px_30px_rgba(255,255,255,0.15)] transition hover:-translate-y-0.5 hover:bg-slate-100">
                Katalogni ko'rish
              </Link>
              <Link href="/compare" class="inline-flex items-center justify-center rounded-full border border-white/15 bg-white/5 px-6 py-3.5 text-sm font-semibold text-white transition hover:bg-white/10">
                Solishtirish
              </Link>
            </div>

            <div class="mt-10 grid max-w-lg grid-cols-3 gap-4">
              <div class="rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur-md">
                <div class="text-2xl font-black text-white">24+</div>
                <div class="mt-1 text-xs uppercase tracking-[0.16em] text-slate-300">demo taklif</div>
              </div>
              <div class="rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur-md">
                <div class="text-2xl font-black text-white">4</div>
                <div class="mt-1 text-xs uppercase tracking-[0.16em] text-slate-300">bo'lim</div>
              </div>
              <div class="rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur-md">
                <div class="text-2xl font-black text-white">3</div>
                <div class="mt-1 text-xs uppercase tracking-[0.16em] text-slate-300">tez filter</div>
              </div>
            </div>
          </div>

          <div class="relative">
            <div class="absolute -top-8 right-8 h-28 w-28 rounded-full bg-sky-400/30 blur-3xl" />
            <div class="relative overflow-hidden rounded-[28px] border border-white/10 bg-white/8 p-5 shadow-[0_30px_80px_rgba(15,23,42,0.45)] backdrop-blur-xl">
              <div class="rounded-[24px] bg-white p-5 text-slate-800 shadow-[0_18px_32px_rgba(15,23,42,0.12)]">
                <div class="mb-5 flex items-center justify-between">
                  <div>
                    <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-400">Qidiruv</p>
                    <h2 class="mt-1 text-2xl font-black tracking-[-0.05em] text-slate-900">Menga mos variantlar</h2>
                  </div>
                  <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="6"/><path d="M16 16L21 21" stroke-linecap="round"/></svg>
                  </div>
                </div>

                <div class="space-y-4">
                  <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700">Bo'lim</label>
                    <select class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                      <option>Butun kvartira / uy</option>
                      <option>Xona</option>
                      <option>O'rin</option>
                      <option>Sotish</option>
                      <option>Yangi qurilishlar</option>
                    </select>
                  </div>

                  <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700">Hudud</label>
                    <select class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                      <option>Markaziy</option>
                      <option>Bo‘ston</option>
                      <option>Asaka</option>
                      <option>Paxtaobod</option>
                    </select>
                  </div>

                  <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700">Narx oralig'i</label>
                    <div class="grid grid-cols-2 gap-3">
                      <input class="rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100" placeholder="Min" />
                      <input class="rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100" placeholder="Max" />
                    </div>
                  </div>

                  <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700">
                    <input type="checkbox" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500" checked />
                    Talabalar qabul qilinadi
                  </label>

                  <Link href="/catalog" class="mt-2 inline-flex w-full items-center justify-center rounded-2xl bg-[linear-gradient(135deg,#2563eb,#0ea5e9)] px-5 py-3.5 text-sm font-semibold text-white shadow-[0_18px_30px_rgba(37,99,235,0.32)] transition hover:-translate-y-0.5">
                    Qidiruvni boshlash
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="bg-[#f5f7fb] py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div class="mb-12 text-center">
            <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-blue-600">Bo'limlar</p>
            <h2 class="mt-4 text-3xl font-black tracking-[-0.06em] text-slate-900 sm:text-4xl">
              Sizga kerak bo'ladigan barcha variantlar
            </h2>
          </div>

          <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            <div class="group rounded-[28px] border border-slate-200 bg-white p-6 shadow-[0_12px_30px_rgba(15,23,42,0.04)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_24px_40px_rgba(37,99,235,0.08)]">
              <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-100 text-blue-600">
                <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 10.5L12 3l9 7.5V19a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1v-8.5Z" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </div>
              <h3 class="text-xl font-bold tracking-[-0.04em] text-slate-900">Butun kvartira / uy</h3>
              <p class="mt-3 text-sm leading-6 text-slate-600">Mustaqil yashash uchun qulay va toza evazlarga ega variantlar.</p>
            </div>

            <div class="group rounded-[28px] border border-slate-200 bg-white p-6 shadow-[0_12px_30px_rgba(15,23,42,0.04)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_24px_40px_rgba(37,99,235,0.08)]">
              <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-2xl bg-violet-100 text-violet-600">
                <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 16V8a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8M4 12h16M8 20v-4m8 4v-4" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </div>
              <h3 class="text-xl font-bold tracking-[-0.04em] text-slate-900">Xona / o'rin</h3>
              <p class="mt-3 text-sm leading-6 text-slate-600">Talabalar va o'rin almashuvchilar uchun mos ijaraviy imkoniyatlar.</p>
            </div>

            <div class="group rounded-[28px] border border-slate-200 bg-white p-6 shadow-[0_12px_30px_rgba(15,23,42,0.04)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_24px_40px_rgba(37,99,235,0.08)]">
              <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600">
                <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 18h16M7 15V9m5 6V6m5 9v-4" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </div>
              <h3 class="text-xl font-bold tracking-[-0.04em] text-slate-900">Sotish</h3>
              <p class="mt-3 text-sm leading-6 text-slate-600">Dastlabki va to'liq ma'lumotlar bilan sotiladigan ko'chmas mulklar.</p>
            </div>

            <div class="group rounded-[28px] border border-slate-200 bg-white p-6 shadow-[0_12px_30px_rgba(15,23,42,0.04)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_24px_40px_rgba(37,99,235,0.08)]">
              <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 text-amber-600">
                <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 20V8l8-5 8 5v12M8 12h8M8 16h8" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </div>
              <h3 class="text-xl font-bold tracking-[-0.04em] text-slate-900">Yangi qurilishlar</h3>
              <p class="mt-3 text-sm leading-6 text-slate-600">Loyiha va novator variantlarini ko'rib chiqish, maslahat olish.</p>
            </div>
          </div>
        </div>
      </section>

      <section class="bg-white py-20">
        <div class="mx-auto grid max-w-7xl gap-8 px-4 sm:px-6 lg:grid-cols-3 lg:px-8">
          <div class="rounded-[28px] border border-slate-200 bg-slate-50 p-8">
            <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-2xl bg-sky-100 text-sky-600">
              <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="6"/><path d="M16 16L21 21" stroke-linecap="round"/></svg>
            </div>
            <h3 class="text-xl font-bold tracking-[-0.04em] text-slate-900">Tez qidiruv</h3>
            <p class="mt-3 text-sm leading-6 text-slate-600">Hudud, bo'lim, narx, talaba qabul qilinishi va mavjudlik sanasi bo'yicha filtrlar.</p>
          </div>

          <div class="rounded-[28px] border border-slate-200 bg-slate-50 p-8">
            <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-2xl bg-cyan-100 text-cyan-600">
              <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 18V7.5A1.5 1.5 0 0 1 5.5 6H18.5A1.5 1.5 0 0 1 20 7.5V18M8 10h8M8 14h5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <h3 class="text-xl font-bold tracking-[-0.04em] text-slate-900">Aniq hisob-kitob</h3>
            <p class="mt-3 text-sm leading-6 text-slate-600">Oylik ijara, kommunal to'lovlar, depozit va dastlabki xarajatlar ko'rsatiladi.</p>
          </div>

          <div class="rounded-[28px] border border-slate-200 bg-slate-50 p-8">
            <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-100 text-rose-600">
              <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M7 10h10M7 14h6M5 5h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <h3 class="text-xl font-bold tracking-[-0.04em] text-slate-900">Murojaat</h3>
            <p class="mt-3 text-sm leading-6 text-slate-600">Qiziqarli takliflar uchun tezkor murojaat yuborish va holatlarni kuzatish.</p>
          </div>
        </div>
      </section>
    </main>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>
