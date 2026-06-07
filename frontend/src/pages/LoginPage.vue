<template>
  <div class="min-h-screen flex items-center justify-center p-4 transition-all duration-300 relative" style="background-color: #111827;">
    <!-- Grid Overlay -->
    <div class="absolute inset-0 pointer-events-none bg-grid z-0 animate-fadeIn" />

    <div class="w-full max-w-sm animate-slideUp z-10">
      <!-- Text Custom Header with Logo -->
      <div class="text-center mb-8 flex flex-col items-center">
        <div class="w-16 h-16 mb-3 p-1 bg-slate-800 rounded-lg border border-slate-700 shadow-md">
          <img src="/logo.svg" alt="Retro Komputer Logo" class="w-full h-full object-contain" />
        </div>
        <h1 class="text-2xl font-bold font-mono tracking-wider text-retro-orange drop-shadow-lg">Retro Komputer</h1>
        <p class="text-slate-300 drop-shadow text-xs mt-2 font-medium tracking-wide">Sistem POS</p>
      </div>

      <!-- Card Container -->
      <div class="bg-white rounded-lg border border-slate-200 p-6 shadow-md">
        <h2 class="text-sm font-semibold text-slate-800 mb-1">Masuk</h2>
        <p class="text-xs text-slate-500 mb-5">Masukkan username dan password</p>

        <div v-if="authStore.error" class="mb-4 p-2.5 rounded-md bg-red-50 border border-red-200 text-red-600 text-xs">
          {{ authStore.error }}
        </div>

        <form @submit.prevent="handleLogin" class="space-y-3">
          <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Username</label>
            <input
              id="login-username"
              v-model="username"
              type="text"
              class="w-full px-3 py-2 text-xs border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-retro-blue focus:border-retro-blue"
              placeholder="admin"
              required
            />
          </div>
          <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Password</label>
            <input
              id="login-password"
              v-model="password"
              type="password"
              class="w-full px-3 py-2 text-xs border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-retro-blue focus:border-retro-blue"
              placeholder="••••••••"
              required
            />
          </div>
          <button
            id="login-submit"
            type="submit"
            :disabled="authStore.loading"
            class="w-full py-2 text-xs font-bold text-white bg-retro-orange hover:bg-retro-orange-dark disabled:opacity-50 rounded-md transition-colors shadow-md hover:shadow-lg"
          >
            {{ authStore.loading ? 'Memproses...' : 'Masuk' }}
          </button>
        </form>
        <div class="flex items-center justify-center mt-5 pt-3 border-t border-slate-100">
          <span class="px-2 py-0.5 text-[9px] font-semibold font-mono tracking-wider bg-slate-50 text-slate-400/80 rounded border border-slate-200/60">
            v1.0
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const username = ref('')
const password = ref('')

async function handleLogin() {
  try {
    await authStore.login(username.value, password.value)
  } catch {
    // handled by store
  }
}
</script>

<style scoped>
.bg-grid {
  background-size: 24px 24px;
  background-image: 
    linear-gradient(to right, rgba(255, 122, 0, 0.1) 1px, transparent 1px),
    linear-gradient(to bottom, rgba(255, 122, 0, 0.1) 1px, transparent 1px);
}
</style>
