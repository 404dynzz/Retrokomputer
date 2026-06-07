<template>
  <div class="min-h-screen flex items-center justify-center p-4 transition-all duration-300 relative"
       :class="isDark ? 'bg-[#111827]' : 'bg-slate-100'">
    <!-- Grid Overlay -->
    <div class="absolute inset-0 pointer-events-none bg-grid z-0 animate-fadeIn" />

    <!-- Theme Toggle Switch (Fixed Top Right) -->
    <div class="fixed top-4 right-4 z-50">
      <button
        @click="toggleTheme"
        class="relative flex items-center justify-between w-14 h-7 p-1 rounded-full bg-slate-200/80 dark:bg-slate-800/80 border border-slate-300/60 dark:border-slate-700/80 transition-all duration-300 focus:outline-none hover:scale-105 active:scale-95 shadow-inner"
        role="switch"
        :aria-checked="isDark"
        title="Ganti Tema"
      >
        <!-- Background Icons -->
        <span class="w-full flex justify-between px-1 pointer-events-none select-none">
          <!-- Moon Icon (Light Mode indicator on right) -->
          <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-400/80" viewBox="0 0 20 20" fill="currentColor">
            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
          </svg>
          <!-- Sun Icon (Dark Mode indicator on left) -->
          <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-retro-yellow/50" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464-5.636a1 1 0 010 1.414l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-5.464 5.636a1 1 0 01-1.414 0l-.707-.707a1 1 0 011.414-1.414l.707.707a1 1 0 010 1.414zM9 17a1 1 0 100-2v1a1 1 0 102 0v-1a1 1 0 10-2 0v1zm-4-5a1 1 0 100-2H4a1 1 0 100 2h1zm1.464-5.636a1 1 0 011.414 0l.707-.707a1 1 0 01-1.414 1.414l-.707-.707a1 1 0 010-1.414z" clip-rule="evenodd" />
          </svg>
        </span>
        <!-- Sliding Knob -->
        <span
          :class="[
            'absolute top-0.5 left-0.5 w-6 h-6 rounded-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 shadow-md flex items-center justify-center transition-transform duration-300 ease-out',
            isDark ? 'translate-x-7' : 'translate-x-0'
          ]"
        >
          <!-- Sun (shown in Dark Mode) -->
          <svg v-if="isDark" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-retro-yellow" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464-5.636a1 1 0 010 1.414l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-5.464 5.636a1 1 0 01-1.414 0l-.707-.707a1 1 0 011.414-1.414l.707.707a1 1 0 010 1.414zM9 17a1 1 0 100-2v1a1 1 0 102 0v-1a1 1 0 10-2 0v1zm-4-5a1 1 0 100-2H4a1 1 0 100 2h1zm1.464-5.636a1 1 0 011.414 0l.707-.707a1 1 0 01-1.414 1.414l-.707-.707a1 1 0 010-1.414z" clip-rule="evenodd" />
          </svg>
          <!-- Moon (shown in Light Mode) -->
          <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-slate-600" viewBox="0 0 20 20" fill="currentColor">
            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
          </svg>
        </span>
      </button>
    </div>

    <div class="w-full max-w-sm animate-slideUp z-10">
      <!-- Text Custom Header with Logo -->
      <div class="text-center mb-8 flex flex-col items-center">
        <div class="w-16 h-16 mb-3 p-1 bg-slate-800 dark:bg-slate-800 rounded-lg border border-slate-700 dark:border-slate-700 shadow-md"
             :class="{ '!bg-slate-200 !border-slate-300': !isDark }">
          <img src="/logo.svg" alt="Retro Komputer Logo" class="w-full h-full object-contain" />
        </div>
        <h1 class="text-2xl font-bold font-mono tracking-wider text-retro-orange drop-shadow-lg">Retro Komputer</h1>
        <p class="drop-shadow text-xs mt-2 font-medium tracking-wide"
           :class="isDark ? 'text-slate-300' : 'text-slate-500'">Sistem POS</p>
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
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useTheme } from '@/utils/theme'

const authStore = useAuthStore()
const { isDark, toggleTheme, initTheme } = useTheme()
const username = ref('')
const password = ref('')

onMounted(() => {
  initTheme()
})

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
