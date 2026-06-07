<template>
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 font-mono">
    <!-- LEFT PANEL: Form for Admins, Infographic Analysis for Owner -->
    <div class="lg:col-span-1 self-start">
      
      <!-- OWNER INFOGRAPHIC PANEL -->
      <div v-if="isOwner" class="bg-white rounded-lg border-2 border-retro-orange overflow-hidden shadow-sm space-y-0">
        <!-- Title Bar -->
        <div class="bg-retro-orange text-white px-3 py-1.5 text-xs font-bold flex justify-between">
          <span>ANALISIS KERUGIAN</span>
          <span>[LOSS]</span>
        </div>
        
        <div class="p-5 space-y-5 font-sans">
          <!-- Total Financial Loss Callout -->
          <div class="space-y-1 bg-red-50/50 p-4 border border-red-200 rounded text-center">
            <span class="block text-[10px] font-bold text-red-500 uppercase tracking-widest font-mono">Total Kerugian Finansial</span>
            <div class="text-xl font-black text-red-600 font-mono tracking-tight">
              Rp {{ formatRupiah(totalFinancialLoss) }}
            </div>
            <span class="block text-[9px] text-slate-400 italic font-mono">Berdasarkan Harga Beli Terakhir</span>
          </div>

          <!-- Breakdown metrics -->
          <div class="space-y-3 font-mono text-xs">
            <div class="flex justify-between border-b border-slate-100 pb-1.5">
              <span class="text-amber-650 font-bold uppercase">■ BARANG RUSAK</span>
              <div class="text-right font-mono">
                <span class="font-bold text-slate-800">{{ rusakQty }} pcs</span>
                <span class="block text-[10px] text-slate-400">Rp {{ formatRupiah(rusakLoss) }}</span>
              </div>
            </div>
            <div class="flex justify-between border-b border-slate-100 pb-1.5">
              <span class="text-red-600 font-bold uppercase">■ BARANG HILANG</span>
              <div class="text-right font-mono">
                <span class="font-bold text-slate-800">{{ hilangQty }} pcs</span>
                <span class="block text-[10px] text-slate-400">Rp {{ formatRupiah(hilangLoss) }}</span>
              </div>
            </div>
          </div>

          <!-- Ratio Visual Bar -->
          <div class="space-y-2">
            <div class="flex justify-between text-[10px] font-bold font-mono">
              <span class="text-amber-600">RUSAK ({{ ratioRusak }}%)</span>
              <span class="text-red-600">HILANG ({{ ratioHilang }}%)</span>
            </div>
            <div class="relative w-full h-3 bg-slate-100 rounded overflow-hidden flex border border-slate-200">
              <div
                class="h-full bg-gradient-to-r from-amber-400 to-amber-500 transition-all duration-500"
                :style="{ width: `${ratioRusak}%` }"
              ></div>
              <div
                class="h-full bg-gradient-to-r from-red-500 to-red-600 transition-all duration-500"
                :style="{ width: `${ratioHilang}%` }"
              ></div>
            </div>
          </div>
        </div>
      </div>

      <!-- ADMIN RECORD FORM -->
      <div v-else class="bg-white rounded-lg border border-slate-200 p-5 space-y-4 shadow-sm">
        <div class="flex items-center gap-2 border-b border-slate-100 pb-2">
          <span class="text-retro-orange text-lg">■</span>
          <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Catat Kerugian Inventaris</h3>
        </div>

        <form @submit.prevent="submitRecord" class="space-y-4">
          <!-- Product Dropdown -->
          <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Pilih Produk</label>
            <select
              v-model="form.produk_id"
              class="w-full px-2.5 py-1.5 text-xs border border-slate-300 rounded bg-white focus:outline-none focus:ring-2 focus:ring-retro-orange focus:border-retro-orange"
              required
            >
              <option value="" disabled>-- Pilih Produk --</option>
              <option v-for="p in products" :key="p.id" :value="p.id" :disabled="p.stok <= 0">
                {{ p.kode_produk }} - {{ p.nama_produk }} (Stok: {{ p.stok }})
              </option>
            </select>
          </div>

          <!-- Category -->
          <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Kategori Laporan</label>
            <div class="flex gap-4">
              <label class="inline-flex items-center text-xs text-slate-700 cursor-pointer">
                <input v-model="form.kategori" type="radio" value="rusak" class="mr-1.5 text-retro-orange focus:ring-retro-orange" />
                Barang Rusak
              </label>
              <label class="inline-flex items-center text-xs text-slate-700 cursor-pointer">
                <input v-model="form.kategori" type="radio" value="hilang" class="mr-1.5 text-retro-orange focus:ring-retro-orange" />
                Barang Hilang
              </label>
            </div>
          </div>

          <!-- Quantity -->
          <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Jumlah (Qty)</label>
            <input
              v-model.number="form.qty"
              type="number"
              min="1"
              class="w-full px-3 py-1.5 text-xs border border-slate-300 rounded focus:outline-none focus:ring-2 focus:ring-retro-orange focus:border-retro-orange"
              placeholder="1"
              required
            />
          </div>

          <!-- Description -->
          <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Keterangan / Alasan</label>
            <textarea
              v-model="form.keterangan"
              rows="3"
              class="w-full px-3 py-1.5 text-xs border border-slate-300 rounded focus:outline-none focus:ring-2 focus:ring-retro-orange focus:border-retro-orange"
              placeholder="Tulis alasan atau kronologi detail..."
            ></textarea>
          </div>

          <!-- Bukti Fisik File Upload (Damaged goods only) -->
          <div v-if="form.kategori === 'rusak'">
            <label class="block text-xs font-semibold text-slate-650 mb-1">Bukti Fisik Kerusakan (Foto)</label>
            <input
              type="file"
              accept="image/*"
              @change="handleFileChange"
              class="w-full text-xs text-slate-500 file:mr-3 file:py-1 file:px-2.5 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-retro-orange/10 file:text-retro-orange hover:file:bg-retro-orange/20 cursor-pointer"
            />
          </div>

          <button
            :disabled="submitting || products.length === 0"
            type="submit"
            class="w-full py-2 text-xs font-bold text-white bg-retro-orange hover:bg-orange-600 rounded transition-colors shadow-sm disabled:opacity-50"
          >
            {{ submitting ? 'Memproses...' : 'Catat Kerugian' }}
          </button>
        </form>
      </div>

    </div>

    <!-- History Panel -->
    <div class="lg:col-span-2 bg-white rounded-lg border border-slate-200 overflow-hidden shadow-sm flex flex-col">
      <div class="p-4 border-b border-slate-200 flex justify-between items-center bg-slate-50">
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Riwayat Penyesuaian Kerugian</h3>
        <span class="text-[10px] text-slate-400 font-mono">Real-time inventory loss logs</span>
      </div>

      <div class="overflow-x-auto flex-1">
        <div v-if="loading" class="p-8 text-center text-xs text-slate-400">
          Memuat data riwayat...
        </div>
        <table v-else class="w-full text-left text-xs border-collapse">
          <thead>
            <tr class="bg-slate-50/50 text-slate-500 border-b border-slate-200">
              <th class="py-2.5 px-3 font-semibold">Tanggal</th>
              <th class="py-2.5 px-3 font-semibold">Produk</th>
              <th class="py-2.5 px-3 font-semibold text-center">Kategori</th>
              <th class="py-2.5 px-3 font-semibold text-center">Qty</th>
              <th class="py-2.5 px-3 font-semibold">Keterangan</th>
              <th class="py-2.5 px-3 font-semibold text-center">Bukti</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-slate-700">
            <tr v-if="history.length === 0">
              <td colspan="6" class="py-8 text-center text-slate-400 font-medium">Belum ada catatan kerugian barang.</td>
            </tr>
            <tr v-for="item in history" :key="item.id" class="hover:bg-slate-50/50 transition-colors">
              <td class="py-3 px-3 text-slate-500 font-mono whitespace-nowrap">{{ formatDate(item.created_at) }}</td>
              <td class="py-3 px-3 font-medium">
                <span class="block text-slate-800">{{ item.produk?.nama_produk }}</span>
                <span class="text-[10px] text-slate-400 font-mono">{{ item.produk?.kode_produk }}</span>
              </td>
              <td class="py-3 px-3 text-center whitespace-nowrap">
                <span
                  v-if="item.kategori === 'rusak'"
                  class="inline-block px-2 py-0.5 text-[9px] font-semibold uppercase tracking-wide bg-amber-50 text-amber-600 rounded border border-amber-200"
                >
                  Rusak
                </span>

                <span
                  v-else
                  class="inline-block px-2 py-0.5 text-[9px] font-semibold uppercase tracking-wide bg-red-50 text-red-600 rounded border border-red-200"
                >
                  Hilang
                </span>
              </td>
              <td class="py-3 px-3 text-center font-bold text-slate-800">{{ item.qty }}</td>
              <td class="py-3 px-3 text-slate-500 italic max-w-[200px] truncate" :title="item.keterangan">{{ item.keterangan || '-' }}</td>
              <td class="py-3 px-3 text-center whitespace-nowrap font-mono">
                <button
                  v-if="item.bukti_foto"
                  @click="openLightbox(item.bukti_foto)"
                  class="text-[10px] font-bold px-2 py-0.5 bg-retro-orange/10 text-retro-orange-dark border border-retro-orange/20 rounded hover:bg-retro-orange/20 transition-colors animate-pulse"
                >
                  [LIHAT FOTO]
                </button>
                <span v-else class="text-slate-350">-</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Lightbox Modal -->
    <div v-if="activeBukti" class="fixed inset-0 bg-retro-dark/80 backdrop-blur-sm flex items-center justify-center p-4" style="z-index: 3000;" @click="activeBukti = null">
      <div class="bg-white border-2 border-retro-orange rounded-lg max-w-lg w-full overflow-hidden shadow-2xl font-mono animate-slideUp" @click.stop>
        <div class="bg-retro-orange text-white px-4 py-2 flex items-center justify-between">
          <span class="font-bold text-xs">■ BUKTI FISIK KERUSAKAN</span>
          <button @click="activeBukti = null" class="text-white hover:text-retro-yellow font-bold text-lg leading-none">×</button>
        </div>
        <div class="p-4 flex items-center justify-center bg-slate-900 border-b border-slate-200 min-h-[300px]">
          <img :src="getCleanUrl(activeBukti)" class="max-h-[70vh] object-contain rounded border-2 border-retro-orange shadow-lg" alt="Bukti Fisik" />
        </div>
        <div class="bg-slate-50 px-4 py-2.5 flex justify-end">
          <button
            @click="activeBukti = null"
            class="text-xs font-bold px-4 py-1.5 border-2 border-slate-300 rounded hover:bg-slate-100 transition-colors"
          >
            TUTUP
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { produkService, barangRusakService } from '@/services'
import type { Produk, BarangRusak, BarangRusakPayload } from '@/types'
import { useAuthStore } from '@/stores/auth'
import { customDialog } from '@/utils/dialog'

const authStore = useAuthStore()
const products = ref<Produk[]>([])
const history = ref<BarangRusak[]>([])

const loading = ref(true)
const submitting = ref(false)

const isOwner = computed(() => authStore.isOwner)
const selectedFile = ref<File | null>(null)
const activeBukti = ref<string | null>(null)

const form = ref<BarangRusakPayload>({
  produk_id: 0,
  qty: 1,
  kategori: 'rusak',
  keterangan: '',
})

onMounted(async () => {
  await Promise.all([fetchProducts(), fetchHistory()])
})

// Infographic Calculations
const totalFinancialLoss = computed(() => {
  return history.value.reduce((acc, item) => acc + (Number(item.qty) * (Number(item.produk?.harga_beli) || 0)), 0)
})

const rusakQty = computed(() => {
  return history.value.filter(item => item.kategori === 'rusak').reduce((acc, item) => acc + Number(item.qty), 0)
})

const hilangQty = computed(() => {
  return history.value.filter(item => item.kategori === 'hilang').reduce((acc, item) => acc + Number(item.qty), 0)
})

const rusakLoss = computed(() => {
  return history.value.filter(item => item.kategori === 'rusak').reduce((acc, item) => acc + (Number(item.qty) * (Number(item.produk?.harga_beli) || 0)), 0)
})

const hilangLoss = computed(() => {
  return history.value.filter(item => item.kategori === 'hilang').reduce((acc, item) => acc + (Number(item.qty) * (Number(item.produk?.harga_beli) || 0)), 0)
})

const totalQty = computed(() => rusakQty.value + hilangQty.value)

const ratioRusak = computed(() => {
  if (totalQty.value === 0) return 0
  return Math.round((rusakQty.value / totalQty.value) * 100)
})

const ratioHilang = computed(() => {
  if (totalQty.value === 0) return 0
  return Math.round((hilangQty.value / totalQty.value) * 100)
})

async function fetchProducts() {
  try {
    const res = await produkService.getAll()
    products.value = res.data
  } catch (err) {
    console.error('Gagal mengambil data produk:', err)
  }
}

async function fetchHistory() {
  loading.value = true
  try {
    const res = await barangRusakService.getAll()
    history.value = res.data
  } catch (err) {
    console.error('Gagal mengambil data riwayat:', err)
  } finally {
    loading.value = false
  }
}

async function submitRecord() {
  if (form.value.produk_id === 0) {
    customDialog.warning('Silakan pilih produk terlebih dahulu!')
    return
  }

  const selectedProduct = products.value.find(p => p.id === form.value.produk_id)
  if (selectedProduct && selectedProduct.stok < form.value.qty) {
    customDialog.warning(`Stok produk tidak mencukupi! Stok saat ini: ${selectedProduct.stok}`)
    return
  }

  submitting.value = true
  try {
    const formData = new FormData()
    formData.append('produk_id', form.value.produk_id.toString())
    formData.append('qty', form.value.qty.toString())
    formData.append('kategori', form.value.kategori)
    formData.append('keterangan', form.value.keterangan || '')
    if (form.value.kategori === 'rusak' && selectedFile.value) {
      formData.append('bukti_file', selectedFile.value)
    }

    await barangRusakService.create(formData)
    customDialog.success('Kerugian inventaris berhasil dicatat!')
    
    // Reset Form
    form.value = {
      produk_id: 0,
      qty: 1,
      kategori: 'rusak',
      keterangan: '',
    }
    selectedFile.value = null
    
    // Reset file input element if found
    const fileInput = document.querySelector('input[type="file"]') as HTMLInputElement
    if (fileInput) fileInput.value = ''

    // Refresh Data
    await Promise.all([fetchProducts(), fetchHistory()])
  } catch (err: any) {
    const msg = err.response?.data?.message || 'Gagal menyimpan catatan kerugian.'
    customDialog.error(msg)
  } finally {
    submitting.value = false
  }
}

function handleFileChange(e: Event) {
  const target = e.target as HTMLInputElement
  if (target.files && target.files[0]) {
    selectedFile.value = target.files[0]
  }
}

function openLightbox(url: string) {
  activeBukti.value = url
}

function getCleanUrl(url: string | null) {
  if (!url) return ''
  if (url.startsWith('http://localhost:8000/')) {
    return url.replace('http://localhost:8000/', '/')
  }
  if (url.startsWith('http://localhost/')) {
    return url.replace('http://localhost/', '/')
  }
  return url
}

function formatRupiah(val: number | string): string {
  if (val === undefined || val === null) return '0'
  return new Intl.NumberFormat('id-ID').format(Number(val))
}

function formatDate(dateStr: string) {
  const date = new Date(dateStr)
  return date.toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'short',
    year: '2-digit',
    hour: '2-digit',
    minute: '2-digit',
  })
}
</script>
