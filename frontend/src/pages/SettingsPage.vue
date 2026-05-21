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
            <label class="block text-xs font-semibold text-slate-600 mb-1">Pilih Gambar Logo</label>
            <input
              @change="onLogoFileChange"
              type="file"
              accept="image/*"
              class="w-full text-xs text-slate-500 file:mr-3 file:py-1 file:px-2.5 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200"
            />
            <p class="text-[10px] text-slate-400 mt-1">
              <strong class="text-retro-orange">Format: PNG (Sangat Direkomendasikan dengan Latar Belakang Transparan)</strong>, JPG, GIF. Maksimal 2MB. Menggunakan latar transparan memastikan logo menyatu sempurna dengan skema warna Retro Dark aplikasi tanpa tepian kotak putih yang mengganggu.
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
        <div class="bg-slate-50 rounded border border-slate-200 p-3 mt-2">
          <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Preview Logo</p>
          <div class="flex items-center gap-2">
            <div class="px-3 bg-retro-dark flex items-center justify-center rounded border border-retro-orange/30 transition-all duration-150"
                 :style="{ height: (parseInt(settings.logo_height) + 12) + 'px' }">
              <span v-if="settings.logo_type === 'text'" class="text-retro-orange font-mono font-bold tracking-wide"
                    :style="{ fontSize: (parseInt(settings.logo_height) * 0.4) + 'px' }">
                &gt;_ {{ settings.logo_text }}
              </span>
              <img v-else-if="logoPreviewUrl" :src="logoPreviewUrl" class="object-contain" 
                   :style="{ height: settings.logo_height + 'px' }" alt="Logo Preview" />
              <span v-else class="text-slate-500 text-xs">Belum ada logo gambar</span>
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
            <label class="block text-xs font-semibold text-slate-600 mb-1">Pilih Gambar Background</label>
            <input
              @change="onBgFileChange"
              type="file"
              accept="image/*"
              class="w-full text-xs text-slate-500 file:mr-3 file:py-1 file:px-2.5 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200"
            />
            <p class="text-[10px] text-slate-400 mt-1">Format: PNG, JPG. Maksimal 4MB.</p>
          </div>
        </div>

        <!-- Preview Background -->
        <div class="bg-slate-50 rounded border border-slate-200 p-3 mt-2">
          <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Preview Tampilan Latar</p>
          <div class="h-24 rounded border border-slate-300 flex items-center justify-center overflow-hidden relative" :style="previewBgStyle">
            <!-- Grid Effect Overlay -->
            <div v-if="settings.bg_type === 'grid'" class="absolute inset-0 pointer-events-none bg-grid" />
            <div class="z-10 text-center">
              <p class="text-xs font-mono font-bold" :class="settings.bg_type === 'grid' || settings.bg_type === 'image' ? 'text-white drop-shadow' : 'text-slate-700'">
                Demo Latar Belakang Login
              </p>
              <p class="text-[9px]" :class="settings.bg_type === 'grid' || settings.bg_type === 'image' ? 'text-slate-200 drop-shadow' : 'text-slate-400'">
                Tipe: {{ settings.bg_type.toUpperCase() }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Action Panel -->
      <div class="flex items-center justify-end gap-3 pt-2">
        <button
          @click="resetToDefault"
          type="button"
          class="px-4 py-2 text-xs font-semibold border border-slate-300 text-slate-600 rounded bg-white hover:bg-slate-50 active:bg-slate-100 transition-colors"
        >
          Reset Default
        </button>
        <button
          :disabled="loading"
          type="submit"
          class="px-5 py-2 text-xs font-bold text-white bg-retro-blue border border-retro-blue/20 hover:bg-blue-700 rounded transition-all shadow-sm flex items-center gap-1.5"
        >
          <span v-if="loading">Menyimpan...</span>
          <span v-else>Simpan Pengaturan</span>
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

function onLogoFileChange(e: Event) {
  const file = (e.target as HTMLInputElement).files?.[0]
  if (file) {
    logoFile.value = file
    logoPreviewUrl.value = URL.createObjectURL(file)
  }
}

function onBgFileChange(e: Event) {
  const file = (e.target as HTMLInputElement).files?.[0]
  if (file) {
    bgFile.value = file
    bgPreviewUrl.value = URL.createObjectURL(file)
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
  } catch (err: any) {
    errorMsg.value = err.response?.data?.message || 'Gagal menyimpan pengaturan.'
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
  } catch (err: any) {
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
