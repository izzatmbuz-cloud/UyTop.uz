<template>
  <div class="min-h-screen bg-slate-50 text-slate-800">
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-600 to-cyan-500 text-white flex items-center justify-center font-black">U</div>
            <div>
              <div class="text-lg font-black tracking-tight">UyTop</div>
              <div class="text-[11px] text-slate-500 uppercase tracking-[0.18em]">Andijon</div>
            </div>
          </div>

          <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-600">
            <Link href="/catalog" class="hover:text-blue-600 transition">Katalog</Link>
            <Link href="/compare" class="hover:text-blue-600 transition">Solishtirish</Link>
            <Link href="/projects" class="hover:text-blue-600 transition">Yangi qurilishlar</Link>
            <Link href="/login" class="hover:text-blue-600 transition">Kirish</Link>
          </nav>

          <div class="flex items-center gap-3">
            <Link href="/catalog" class="hidden sm:inline-flex px-4 py-2 rounded-lg border border-slate-200 text-slate-700 hover:border-blue-300 hover:text-blue-600 transition">Katalog</Link>
            <Link href="/login" class="inline-flex px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-500 text-white font-semibold shadow-lg shadow-blue-600/20 hover:shadow-xl hover:shadow-blue-600/30 transition">E'lon joylashtirish</Link>
          </div>
        </div>
      </div>
    </header>

    <main>
      <section class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-blue-950 to-blue-800 text-white">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,_rgba(56,189,248,0.22),transparent_25%),radial-gradient(circle_at_bottom_left,_rgba(59,130,246,0.18),transparent_25%)]" />
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28">
          <div class="grid lg:grid-cols-[1.2fr_0.8fr] items-center gap-10">
            <div>
              <div class="inline-flex items-center gap-2 bg-white/10 border border-white/15 px-3 py-1.5 rounded-full text-xs font-medium tracking-[0.1em] uppercase mb-6">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                Andijon uchun uy-joy platformasi
              </div>

              <h1 class="text-4xl md:text-6xl font-black tracking-tight leading-tight mb-5">
                Andijonda o'zingizga mos
                <span class="text-blue-200">uy-joyni toping</span>
              </h1>

              <p class="max-w-xl text-lg text-slate-200 leading-8 mb-8">
                Talabalar, oilalar va ijaraga mo'ljallangan ko'chmas mulklarni solishtirish, filtr qilish va tez murojaat qilish uchun qulay platforma.
              </p>

              <div class="flex flex-col sm:flex-row gap-4 mb-10">
                <Link href="/catalog" class="inline-flex justify-center items-center px-6 py-3.5 rounded-xl bg-white text-slate-900 font-semibold shadow-lg hover:bg-slate-100 transition">
                  Katalogni ko'rish
                </Link>
                <Link href="/compare" class="inline-flex justify-center items-center px-6 py-3.5 rounded-xl border border-white/20 bg-white/5 text-white font-semibold hover:bg-white/10 transition">
                  Solishtirish
                </Link>
              </div>

              <div class="grid grid-cols-3 gap-4 max-w-lg">
                <div class="bg-white/5 border border-white/10 rounded-2xl p-4 backdrop-blur-sm">
                  <div class="text-2xl font-black">24+</div>
                  <div class="text-sm text-slate-200">demo takliflar</div>
                </div>
                <div class="bg-white/5 border border-white/10 rounded-2xl p-4 backdrop-blur-sm">
                  <div class="text-2xl font-black">4</div>
                  <div class="text-sm text-slate-200">asosiy bo'lim</div>
                </div>
                <div class="bg-white/5 border border-white/10 rounded-2xl p-4 backdrop-blur-sm">
                  <div class="text-2xl font-black">3</div>
                  <div class="text-sm text-slate-200">tez filtering</div>
                </div>
              </div>
            </div>

            <div class="bg-white/10 border border-white/15 backdrop-blur-md rounded-3xl p-5 shadow-2xl">
              <div class="bg-white rounded-2xl p-5 text-slate-800">
                <div class="mb-5">
                  <div class="text-xs uppercase tracking-[0.15em] text-slate-400 mb-2">Qidiruv</div>
                  <h2 class="text-2xl font-bold">Menga mos variantlar</h2>
                </div>

                <div class="space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Bo'lim</label>
                    <select class="w-full rounded-xl border border-slate-200 px-3 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                      <option>Butun kvartira / uy</option>
                      <option>Xona</option>
                      <option>O'rin</option>
                      <option>Sotish</option>
                      <option>Yangi qurilishlar</option>
                    </select>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Hudud</label>
                    <select class="w-full rounded-xl border border-slate-200 px-3 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                      <option>Markaziy</option>
                      <option>Bo‘ston</option>
                      <option>Asaka</option>
                      <option>Paxtaobod</option>
                    </select>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Narx oralig'i</label>
                    <div class="grid grid-cols-2 gap-3">
                      <input class="w-full rounded-xl border border-slate-200 px-3 py-2.5" placeholder="Min" />
                      <input class="w-full rounded-xl border border-slate-200 px-3 py-2.5" placeholder="Max" />
                    </div>
                  </div>

                  <label class="flex items-center gap-3 text-sm text-slate-700">
                    <input type="checkbox" class="w-4 h-4 text-blue-600 rounded" checked />
                    Talabalar qabul qilinadi
                  </label>

                  <Link href="/catalog" class="mt-2 inline-flex w-full justify-center items-center px-5 py-3 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-500 text-white font-semibold shadow-lg shadow-blue-600/20 hover:shadow-xl hover:shadow-blue-600/30 transition">
                    Qidiruvni boshlash
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center mb-12">
            <div class="text-xs uppercase tracking-[0.2em] text-blue-600 font-semibold mb-3">Bo'limlar</div>
            <h2 class="text-3xl md:text-4xl font-black tracking-tight text-slate-900">Sizga kerak bo'ladigan turli imkoniyatlar</h2>
          </div>

          <div class="grid md:grid-cols-2 xl:grid-cols-4 gap-6">
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 hover:shadow-lg transition">
              <div class="text-4xl mb-4">🏠</div>
              <h3 class="text-xl font-bold text-slate-900 mb-2">Butun kvartira / uy</h3>
              <p class="text-slate-600">Mustaqil yashash uchun qulay va toza evazlarga ega variantlar.</p>
            </div>

            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 hover:shadow-lg transition">
              <div class="text-4xl mb-4">🛏️</div>
              <h3 class="text-xl font-bold text-slate-900 mb-2">Xona / o'rin</h3>
              <p class="text-slate-600">Talabalar va o'rin almashuvchilar uchun mos ijaraviy imkoniyatlar.</p>
            </div>

            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 hover:shadow-lg transition">
              <div class="text-4xl mb-4">📈</div>
              <h3 class="text-xl font-bold text-slate-900 mb-2">Sotish</h3>
              <p class="text-slate-600">Dastlabki va to'liq ma'lumotlar bilan sotiladigan ko'chmas mulklar.</p>
            </div>

            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 hover:shadow-lg transition">
              <div class="text-4xl mb-4">🏗️</div>
              <h3 class="text-xl font-bold text-slate-900 mb-2">Yangi qurilishlar</h3>
              <p class="text-slate-600">Loyiha va novator variantlarini ko'rib chiqish, maslahat olish.</p>
            </div>
          </div>
        </div>
      </section>

      <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="grid lg:grid-cols-3 gap-8">
            <div class="bg-slate-50 rounded-3xl p-8 border border-slate-200">
              <div class="text-3xl mb-4">🔎</div>
              <h3 class="text-xl font-bold text-slate-900 mb-3">Tez qidiruv</h3>
              <p class="text-slate-600">Hudud, bo'lim, narx, talaba qabul qilinishi va mavjudlik sanasi bo'yicha filtrlar.</p>
            </div>

            <div class="bg-slate-50 rounded-3xl p-8 border border-slate-200">
              <div class="text-3xl mb-4">📊</div>
              <h3 class="text-xl font-bold text-slate-900 mb-3">Aniq hisob-kitob</h3>
              <p class="text-slate-600">Oylik ijara, kommunal to'lovlar, depozit va dastlabki xarajatlar ko'rsatiladi.</p>
            </div>

            <div class="bg-slate-50 rounded-3xl p-8 border border-slate-200">
              <div class="text-3xl mb-4">💬</div>
              <h3 class="text-xl font-bold text-slate-900 mb-3">Murojaat</h3>
              <p class="text-slate-600">Qiziqarli takliflar uchun tezkor murojaat yuborish va holatlarni kuzatish.</p>
            </div>
          </div>
        </div>
      </section>
    </main>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>
