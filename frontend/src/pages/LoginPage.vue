<template>
  <div class="min-h-screen flex items-center justify-center p-4 transition-all duration-300 relative bg-slate-50 dark:bg-slate-950">
    <!-- Floating Theme Toggle Button -->
    <button
      @click="toggleTheme"
      type="button"
      class="fixed top-4 right-4 z-50 p-2 rounded-md hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 transition-colors shadow-sm flex items-center justify-center"
      title="Toggle Theme"
    >
      <!-- Sun Icon (shows in Dark Mode to switch to Light) -->
      <svg v-if="isDark" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-retro-yellow" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
      </svg>
      <!-- Moon Icon (shows in Light Mode to switch to Dark) -->
      <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
      </svg>
    </button>

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
          <span class="px-2 py-0.5 text-[9px] font-semibold font-mono tracking-wider">
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
import { useTheme } from '@/utils/theme'

const authStore = useAuthStore()
const { isDark, toggleTheme } = useTheme()

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
