<template>
  <div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between border-b border-slate-200 pb-3">
      <div>
        <h2 class="text-base font-semibold text-slate-800">Pengaturan Sistem</h2>
        <p class="text-xs text-slate-500">Sesuaikan logo dan latar belakang retro untuk antarmuka aplikasi.</p>
      </div>
      <div class="text-xs px-2.5 py-1 bg-retro-blue/10 text-retro-blue font-semibold rounded-full border border-retro-blue/20">
        Admin Mode
      </div>
    </div>

    <!-- Status Alerts -->
    <div v-if="successMsg" class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs rounded-md">
      {{ successMsg }}
    </div>
    <div v-if="errorMsg" class="p-3 bg-red-50 border border-red-200 text-red-700 text-xs rounded-md">
      {{ errorMsg }}
    </div>

    <form @submit.prevent="saveSettings" class="space-y-6">
      <!-- Card: Logo Retro Komputer -->
      <div class="bg-white rounded-lg border border-slate-200 p-5 space-y-4 shadow-sm">
        <div class="flex items-center gap-2 border-b border-slate-100 pb-2">
          <span class="text-retro-orange text-lg">■</span>
          <h3 class="text-sm font-semibold text-slate-700">Kustomisasi Logo Retro Komputer</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- Logo Type Choice -->
          <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Tipe Logo</label>
            <div class="flex gap-4">
              <label class="inline-flex items-center text-xs text-slate-700 cursor-pointer">
                <input v-model="settings.logo_type" type="radio" value="text" class="mr-2 text-retro-orange focus:ring-retro-orange" />
                Logo Teks (Pixel)
              </label>
              <label class="inline-flex items-center text-xs text-slate-700 cursor-pointer">
                <input v-model="settings.logo_type" type="radio" value="image" class="mr-2 text-retro-orange focus:ring-retro-orange" />
                Logo Gambar (Upload)
              </label>
            </div>
          </div>

          <!-- Logo Text Input -->
          <div v-if="settings.logo_type === 'text'">
            <label class="block text-xs font-semibold text-slate-600 mb-1">Teks Logo</label>
            <input
              v-model="settings.logo_text"
              type="text"
              class="w-full px-3 py-1.5 text-xs border border-slate-300 rounded focus:outline-none focus:ring-2 focus:ring-retro-orange focus:border-retro-orange"
              placeholder="Retro Komputer"
            />
          </div>

          <!-- Logo Image File Upload -->
          <div v-else>
            <label class="block text-xs font-semibold text-slate-600 mb-2">📤 Pilih Gambar Logo</label>
            <div class="relative">
              <input
                @change="onLogoFileChange"
                type="file"
                accept="image/png,image/jpeg,image/gif"
                class="block w-full text-sm text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-retro-orange file:text-white hover:file:bg-retro-orange-dark cursor-pointer"
              />
            </div>
            <p class="text-[10px] text-slate-500 mt-2 leading-relaxed">
              <strong class="text-retro-orange">💡 Rekomendasi:</strong> Gunakan format <strong>PNG dengan background transparan</strong> agar logo menyatu sempurna dengan latar gelap tanpa tepian kotak putih. Maksimal 2MB. Ukuran ideal: 256x256px atau lebih besar.
            </p>
          </div>

          <!-- Logo Size (Flexible Height) Slider -->
          <div class="col-span-1 md:col-span-2 border-t border-slate-100/50 pt-3">
            <label class="block text-xs font-semibold text-slate-600 mb-1.5 flex justify-between items-center">
              <span>Ukuran Tinggi Logo (Fleksibel)</span>
              <span class="text-retro-orange font-mono font-bold">{{ settings.logo_height }}px</span>
            </label>
            <div class="flex items-center gap-3">
              <span class="text-[10px] text-slate-400">20px</span>
              <input 
                v-model="settings.logo_height" 
                type="range" 
                min="20" 
                max="100" 
                class="flex-1 h-1.5 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-retro-orange focus:outline-none"
              />
              <span class="text-[10px] text-slate-400">100px</span>
            </div>
            <p class="text-[10px] text-slate-400 mt-1">Sesuaikan tinggi gambar/teks logo di header/sidebar secara dinamis antara 20 piksel hingga 100 piksel.</p>
          </div>
        </div>

        <!-- Preview Logo -->
        <div class="bg-gradient-to-br from-retro-dark to-retro-dark-card rounded-lg border-2 border-retro-orange/30 p-4 mt-2">
          <p class="text-[10px] font-semibold uppercase tracking-wider text-retro-orange drop-shadow mb-3">✨ Preview Logo Retro</p>
          <div class="bg-retro-dark rounded-md border border-retro-orange/20 p-4 flex items-center justify-center min-h-24">
            <div class="flex items-center gap-3">
              <div class="px-4 bg-retro-dark flex items-center justify-center rounded border-2 border-retro-orange/40 shadow-lg transition-all duration-300 hover:border-retro-orange hover:shadow-xl hover:scale-105"
                   :style="{ height: (parseInt(settings.logo_height) + 12) + 'px' }">
                <span v-if="settings.logo_type === 'text'" class="text-retro-orange font-mono font-bold tracking-widest drop-shadow-lg transition-all duration-300 hover:scale-110"
                      :style="{ fontSize: (parseInt(settings.logo_height) * 0.45) + 'px' }">
                  ▌{{ settings.logo_text || 'Retro Komputer' }}▐
                </span>
                <img v-else-if="logoPreviewUrl" :src="logoPreviewUrl" class="object-contain filter drop-shadow-lg transition-all duration-300 hover:scale-110" 
                     :style="{ height: settings.logo_height + 'px' }" alt="Logo Preview" />
                <div v-else class="flex flex-col items-center justify-center text-slate-400">
                  <span class="text-2xl mb-1">📁</span>
                  <span class="text-xs italic">No image yet</span>
                </div>
              </div>
              <div class="text-xs">
                <p class="text-retro-orange font-bold">{{ settings.logo_type === 'text' ? 'Text Logo' : 'Image Logo' }}</p>
                <p class="text-slate-400 text-[10px]">Height: {{ settings.logo_height }}px</p>
                <p v-if="logoPreviewUrl" class="text-emerald-400 text-[10px] mt-1">✅ Preview ready</p>
                <p v-else class="text-amber-400 text-[10px] mt-1">⏳ Upload preview</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Card: Halaman Login Background -->
      <div class="bg-white rounded-lg border border-slate-200 p-5 space-y-4 shadow-sm">
        <div class="flex items-center gap-2 border-b border-slate-100 pb-2">
          <span class="text-retro-blue text-lg">■</span>
          <h3 class="text-sm font-semibold text-slate-700">Kustomisasi Background Latar Halaman Login</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- Background Type -->
          <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Tipe Background</label>
            <select
              v-model="settings.bg_type"
              class="w-full px-2.5 py-1.5 text-xs border border-slate-300 rounded bg-white focus:outline-none focus:ring-2 focus:ring-retro-blue focus:border-retro-blue"
            >
              <option value="default">Default (Slate-50 Clean)</option>
              <option value="color">Warna Solid</option>
              <option value="grid">Pola Retro Grid (Garis Kotak)</option>
              <option value="image">Gambar Khusus (Upload)</option>
            </select>
          </div>

          <!-- Background Color Input -->
          <div v-if="settings.bg_type === 'color'">
            <label class="block text-xs font-semibold text-slate-600 mb-1">Pilih Warna Background</label>
            <div class="flex gap-2">
              <input
                v-model="settings.bg_color"
                type="color"
                class="w-8 h-8 p-0 border border-slate-300 rounded cursor-pointer"
              />
              <input
                v-model="settings.bg_color"
                type="text"
                class="flex-1 px-3 py-1.5 text-xs border border-slate-300 rounded focus:outline-none focus:ring-2 focus:ring-retro-blue focus:border-retro-blue"
                placeholder="#f8fafc"
              />
            </div>
          </div>

          <!-- Background Image Upload -->
          <div v-if="settings.bg_type === 'image'">
            <label class="block text-xs font-semibold text-slate-600 mb-2">📤 Pilih Gambar Background</label>
            <div class="relative">
              <input
                @change="onBgFileChange"
                type="file"
                accept="image/png,image/jpeg"
                class="block w-full text-sm text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-retro-blue file:text-white hover:file:bg-blue-700 cursor-pointer"
              />
            </div>
            <p class="text-[10px] text-slate-500 mt-2 leading-relaxed">
              <strong class="text-retro-blue">💡 Rekomendasi:</strong> Gunakan gambar berkualitas tinggi dengan ukuran minimum 1920x1080px. Format: PNG atau JPG. Maksimal 4MB. Gambar akan di-stretch untuk fill layar login secara responsive.
            </p>
          </div>
        </div>

        <!-- Preview Background -->
        <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-lg border-2 border-retro-blue/30 p-4 mt-2">
          <p class="text-[10px] font-semibold uppercase tracking-wider text-retro-blue drop-shadow mb-3">✨ Preview Tampilan Latar Login</p>
          <div class="h-32 rounded-lg border-2 border-retro-blue/40 flex items-center justify-center overflow-hidden relative shadow-lg transition-all duration-300 hover:shadow-xl" :style="previewBgStyle">
            <!-- Grid Effect Overlay -->
            <div v-if="settings.bg_type === 'grid'" class="absolute inset-0 pointer-events-none bg-grid" />
            
            <!-- Background Image Preview -->
            <div v-if="settings.bg_type === 'image' && bgPreviewUrl" class="absolute inset-0 bg-cover bg-center opacity-80" :style="{ backgroundImage: `url(${bgPreviewUrl})` }" />
            
            <!-- Color Background Preview -->
            <div v-if="settings.bg_type === 'color'" class="absolute inset-0" :style="{ backgroundColor: settings.bg_color }" />
            
            <!-- Default Background Preview -->
            <div v-if="settings.bg_type === 'default'" class="absolute inset-0 bg-gradient-to-br from-slate-50 to-slate-100" />
            
            <div class="z-10 text-center px-4">
              <p class="text-sm font-mono font-bold drop-shadow-lg text-white">
                🖥️ Demo Latar Belakang Login
              </p>
              <p class="text-[10px] mt-1 drop-shadow text-slate-200">
                Tipe: <span class="font-bold text-retro-blue">{{ settings.bg_type.toUpperCase() }}</span>
              </p>
              <p v-if="settings.bg_type === 'color'" class="text-[10px] mt-1 drop-shadow text-slate-300 font-mono">
                {{ settings.bg_color }}
              </p>
              <p v-if="settings.bg_type === 'image' && bgPreviewUrl" class="text-[10px] mt-1 drop-shadow text-emerald-300">
                ✅ Background loaded
              </p>
              <p v-if="settings.bg_type === 'image' && !bgPreviewUrl" class="text-[10px] mt-1 drop-shadow text-amber-300">
                ⏳ Upload background preview
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Action Panel -->
      <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
        <button
          @click="resetToDefault"
          type="button"
          class="px-4 py-2.5 text-xs font-bold border-2 border-slate-300 text-slate-700 rounded-lg bg-white hover:bg-slate-50 hover:border-slate-400 active:bg-slate-100 transition-all shadow-sm"
        >
          🔄 Reset Default
        </button>
        <button
          :disabled="loading"
          type="submit"
          class="px-6 py-2.5 text-xs font-bold text-white bg-gradient-to-r from-retro-blue to-retro-blue-deep border border-retro-blue/20 rounded-lg hover:shadow-lg active:shadow-md transition-all shadow-md disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2"
        >
          <span v-if="loading" class="animate-spin">⏳</span>
          <span v-else>💾</span>
          {{ loading ? 'Menyimpan...' : 'Simpan Pengaturan' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { settingService } from '@/services'
import type { ActiveSettings } from '@/types'

const loading = ref(false)
const successMsg = ref('')
const errorMsg = ref('')

const settings = ref<ActiveSettings>({
  logo_type: 'text',
  logo_text: 'Retro Komputer',
  logo_url: '',
  logo_height: '32',
  bg_type: 'default',
  bg_color: '#f8fafc',
  bg_url: '',
})

const logoFile = ref<File | null>(null)
const bgFile = ref<File | null>(null)

const logoPreviewUrl = ref('')
const bgPreviewUrl = ref('')

const previewBgStyle = computed(() => {
  if (settings.value.bg_type === 'image' && bgPreviewUrl.value) {
    return {
      backgroundImage: `url(${bgPreviewUrl.value})`,
      backgroundSize: 'cover',
      backgroundPosition: 'center'
    }
  }
  return {}
})

onMounted(async () => {
  await fetchSettings()
})

async function fetchSettings() {
  try {
    const res = await settingService.getActive()
    settings.value = res.data
    logoPreviewUrl.value = res.data.logo_url
    bgPreviewUrl.value = res.data.bg_url
  } catch (err: any) {
    console.error('Gagal mengambil settings:', err)
  }
}

const onLogoFileChange = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files && target.files[0]) {
    const file = target.files[0]
    
    // Validasi file
    if (!file.type.match('image.*')) {
      errorMsg.value = 'File harus berupa gambar (PNG, JPEG, GIF).'
      return
    }
    
    if (file.size > 2 * 1024 * 1024) { // 2MB
      errorMsg.value = 'Ukuran file maksimal 2MB.'
      return
    }
    
    logoFile.value = file
    
    // Buat preview URL
    const reader = new FileReader()
    reader.onload = (e) => {
      if (e.target?.result) {
        logoPreviewUrl.value = e.target.result as string
      }
    }
    reader.readAsDataURL(file)
    
    // Clear error message
    errorMsg.value = ''
  }
}

const onBgFileChange = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files && target.files[0]) {
    const file = target.files[0]
    
    // Validasi file
    if (!file.type.match('image.*')) {
      errorMsg.value = 'File harus berupa gambar (PNG, JPEG, GIF).'
      return
    }
    
    if (file.size > 5 * 1024 * 1024) { // 5MB
      errorMsg.value = 'Ukuran file maksimal 5MB.'
      return
    }
    
    bgFile.value = file
    
    // Buat preview URL
    const reader = new FileReader()
    reader.onload = (e) => {
      if (e.target?.result) {
        bgPreviewUrl.value = e.target.result as string
      }
    }
    reader.readAsDataURL(file)
    
    // Clear error message
    errorMsg.value = ''
  }
}

const previewBgStyle = computed(() => {
  if (settings.value.bg_type === 'default') {
    return { backgroundColor: '#f8fafc' }
  } else if (settings.value.bg_type === 'color') {
    return { backgroundColor: settings.value.bg_color }
  } else if (settings.value.bg_type === 'grid') {
    return { backgroundColor: '#111827' }
  } else if (settings.value.bg_type === 'image') {
    return bgPreviewUrl.value
      ? { backgroundImage: `url(${bgPreviewUrl.value})`, backgroundSize: 'cover', backgroundPosition: 'center' }
      : { backgroundColor: '#cbd5e1' }
  }
  return { backgroundColor: '#f8fafc' }
})

async function saveSettings() {
  loading.value = true
  successMsg.value = ''
  errorMsg.value = ''

  try {
    const formData = new FormData()
    formData.append('logo_type', settings.value.logo_type)
    formData.append('logo_text', settings.value.logo_text)
    formData.append('logo_height', settings.value.logo_height)
    formData.append('bg_type', settings.value.bg_type)
    formData.append('bg_color', settings.value.bg_color)

    if (logoFile.value) {
      formData.append('logo_file', logoFile.value)
    }
    if (bgFile.value) {
      formData.append('background_file', bgFile.value)
    }

    const res = await settingService.update(formData)
    settings.value = res.data
    logoPreviewUrl.value = res.data.logo_url
    bgPreviewUrl.value = res.data.bg_url

    // Dispatch global event so header logo updating is synchronous without reload
    window.dispatchEvent(new CustomEvent('settings-updated', { detail: res.data }))

    successMsg.value = 'Pengaturan berhasil disimpan!'
    logoFile.value = null
    bgFile.value = null
  } catch (error) {
    const errorMessage = error instanceof Error ? error.message : 'Gagal menyimpan pengaturan.'
    errorMsg.value = errorMessage
  } finally {
    loading.value = false
  }
}

async function resetToDefault() {
  if (!confirm('Apakah Anda yakin ingin menyetel ulang pengaturan ke default?')) return

  loading.value = true
  successMsg.value = ''
  errorMsg.value = ''

  try {
    const formData = new FormData()
    formData.append('logo_type', 'text')
    formData.append('logo_text', 'Retro Komputer')
    formData.append('logo_height', '32')
    formData.append('bg_type', 'default')
    formData.append('bg_color', '#f8fafc')

    const res = await settingService.update(formData)
    settings.value = res.data
    logoPreviewUrl.value = ''
    bgPreviewUrl.value = ''

    window.dispatchEvent(new CustomEvent('settings-updated', { detail: res.data }))

    successMsg.value = 'Pengaturan disetel ulang ke default!'
  } catch {
    errorMsg.value = 'Gagal mengatur ulang ke default.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.bg-grid {
  background-size: 20px 20px;
  background-image: 
    linear-gradient(to right, rgba(255, 122, 0, 0.1) 1px, transparent 1px),
    linear-gradient(to bottom, rgba(255, 122, 0, 0.1) 1px, transparent 1px);
}
</style>
