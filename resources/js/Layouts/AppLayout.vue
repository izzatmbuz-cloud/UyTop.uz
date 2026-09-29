<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <!-- Logo -->
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
              <span class="text-white font-bold text-lg">𝐔𝐓</span>
            </div>
            <span class="text-xl font-bold text-gray-900">UyTop</span>
          </div>

          <!-- Menu -->
          <div class="hidden md:flex items-center gap-8">
            <Link href="/catalog" class="text-gray-700 hover:text-blue-600 transition">
              Katalog
            </Link>
            <Link href="/projects" class="text-gray-700 hover:text-blue-600 transition">
              Yangi qurilishlar
            </Link>
            <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
              E'lon joylashtirish
            </button>
            <Link href="/account" class="text-gray-700 hover:text-blue-600 transition">
              Kabinet
            </Link>
          </div>

          <!-- Mobile menu button -->
          <button class="md:hidden">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
        </div>
      </div>
    </nav>

    <!-- Main content -->
    <main>
      <slot />
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white mt-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="text-center text-gray-400">
          <p>&copy; 2026 UyTop. Andijonda uy-joy qidirish.</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>
