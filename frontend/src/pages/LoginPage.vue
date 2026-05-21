<template>
  <div class="min-h-screen flex items-center justify-center p-4 transition-all duration-300 relative" :style="backgroundStyle">
    <!-- Grid Overlay -->
    <div v-if="bgType === 'grid'" class="absolute inset-0 pointer-events-none bg-grid z-0 animate-fadeIn" />

    <div class="w-full max-w-sm animate-slideUp z-10">
      <!-- Logo Custom Header -->
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center mb-4">
          <!-- Logo Type: IMAGE -->
          <div v-if="logoType === 'image' && logoUrl" 
               class="flex items-center justify-center bg-gradient-to-br from-retro-dark to-retro-dark-card px-6 rounded-lg border-2 border-retro-orange/50 shadow-xl animate-fadeIn transition-all duration-150 hover:border-retro-orange hover:shadow-2xl hover:scale-105"
               :style="{ height: (parseInt(logoHeight) + 24) + 'px' }">
            <img :src="logoUrl" class="object-contain filter drop-shadow-lg" :style="{ height: logoHeight + 'px' }" alt="Retro Logo" />
          </div>
          <!-- Logo Type: TEXT -->
          <div v-else-if="logoType === 'text'" 
               class="px-6 bg-gradient-to-br from-retro-dark to-retro-dark-card flex items-center justify-center rounded-lg border-2 border-retro-orange/50 shadow-xl animate-fadeIn transition-all duration-150 hover:border-retro-orange hover:shadow-2xl hover:scale-105"
               :style="{ height: (parseInt(logoHeight) + 24) + 'px' }">
            <span class="text-retro-orange font-mono font-bold tracking-widest drop-shadow-lg"
                  :style="{ fontSize: (parseInt(logoHeight) * 0.45) + 'px' }">
              ▌{{ logoText }}▐
            </span>
          </div>
          <!-- Logo Type: DEFAULT -->
          <div v-else class="w-16 h-16 rounded-lg bg-gradient-to-br from-retro-blue to-retro-blue-deep flex items-center justify-center text-white font-bold text-3xl shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-150">
            R
          </div>
        </div>
        <h1 class="text-2xl font-bold font-mono tracking-wider" :class="isDarkBg ? 'text-retro-orange drop-shadow-lg' : 'text-slate-900'">{{ logoText }}</h1>
        <p :class="isDarkBg ? 'text-slate-300 drop-shadow' : 'text-slate-600'" class="text-xs mt-2 font-medium tracking-wide">📊 Sistem POS Retro Modern</p>
      </div>

      <!-- Card Container -->
      <div class="bg-white rounded-lg border border-slate-200 p-6 shadow-md">
        <h2 class="text-sm font-semibold text-slate-800 mb-1">Masuk</h2>
        <p class="text-xs text-slate-500 mb-5">Masukkan email dan password</p>

        <div v-if="authStore.error" class="mb-4 p-2.5 rounded-md bg-red-50 border border-red-200 text-red-600 text-xs">
          {{ authStore.error }}
        </div>

        <form @submit.prevent="handleLogin" class="space-y-3">
          <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Email</label>
            <input
              id="login-email"
              v-model="email"
              type="email"
              class="w-full px-3 py-2 text-xs border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-retro-blue focus:border-retro-blue"
              placeholder="email@contoh.com"
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

        <div class="mt-5 pt-4 border-t border-slate-200">
          <p class="text-[10px] text-slate-400 text-center mb-2 uppercase tracking-wider">Demo Akses</p>
          <div class="grid grid-cols-3 gap-2">
            <button @click="fillDemo('admin@retro.com')" class="text-[10px] py-1.5 font-semibold rounded border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors">Admin</button>
            <button @click="fillDemo('kasir@retro.com')" class="text-[10px] py-1.5 font-semibold rounded border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors">Kasir</button>
            <button @click="fillDemo('owner@retro.com')" class="text-[10px] py-1.5 font-semibold rounded border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors">Owner</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { settingService } from '@/services'

const authStore = useAuthStore()
const email = ref('')
const password = ref('')

const logoType = ref('text')
const logoText = ref('Retro Komputer')
const logoUrl = ref('')
const logoHeight = ref('32')

const bgType = ref('default')
const bgColor = ref('#f8fafc')
const bgUrl = ref('')

onMounted(async () => {
  try {
    const res = await settingService.getActive()
    logoType.value = res.data.logo_type
    logoText.value = res.data.logo_text
    logoUrl.value = res.data.logo_url
    logoHeight.value = res.data.logo_height || '32'
    bgType.value = res.data.bg_type
    bgColor.value = res.data.bg_color
    bgUrl.value = res.data.bg_url
  } catch (err) {
    console.error('Gagal mengambil active settings:', err)
  }
})

const isDarkBg = computed(() => {
  return bgType.value === 'grid' || (bgType.value === 'color' && isHexDark(bgColor.value))
})

const backgroundStyle = computed(() => {
  if (bgType.value === 'default') {
    return { backgroundColor: '#f8fafc' }
  } else if (bgType.value === 'color') {
    return { backgroundColor: bgColor.value }
  } else if (bgType.value === 'grid') {
    return { backgroundColor: '#111827' }
  } else if (bgType.value === 'image') {
    return bgUrl.value
      ? { backgroundImage: `url(${bgUrl.value})`, backgroundSize: 'cover', backgroundPosition: 'center', backgroundRepeat: 'no-repeat' }
      : { backgroundColor: '#f8fafc' }
  }
  return { backgroundColor: '#f8fafc' }
})

async function handleLogin() {
  try {
    await authStore.login(email.value, password.value)
  } catch {
    // handled by store
  }
}

function fillDemo(demoEmail: string) {
  email.value = demoEmail
  password.value = 'password'
}

function isHexDark(color: string) {
  const hex = color.replace('#', '')
  if (hex.length !== 6) return false
  const r = parseInt(hex.substring(0, 2), 16)
  const g = parseInt(hex.substring(2, 4), 16)
  const b = parseInt(hex.substring(4, 6), 16)
  const brightness = (r * 299 + g * 587 + b * 114) / 1000
  return brightness < 128
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
