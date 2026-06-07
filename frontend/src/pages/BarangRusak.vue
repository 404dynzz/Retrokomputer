<template>
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    <!-- LEFT PANEL: Form for Admins, Infographic Analysis for Owner -->
    <div class="lg:col-span-1 self-start">
      
      <!-- OWNER INFOGRAPHIC PANEL -->
      <div v-if="isOwner" style="background: linear-gradient(135deg, #131b2e 0%, #0f1623 100%); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; overflow: hidden;">
        <!-- Title Bar -->
        <div class="px-5 py-3 flex justify-between items-center" style="border-bottom: 1px solid rgba(255,255,255,0.04);">
          <span class="text-xs font-semibold tracking-wide" style="color: #94a3b8;">Analisis Kerugian</span>
          <span class="text-[10px] font-medium px-2 py-0.5 rounded-full" style="background: rgba(244, 63, 94, 0.08); color: #fb7185;">Loss</span>
        </div>
        
        <div class="p-5 space-y-5">
          <!-- Total Financial Loss Callout -->
          <div class="p-4 rounded-lg text-center" style="background: rgba(244, 63, 94, 0.06); border: 1px solid rgba(244, 63, 94, 0.1);">
            <span class="block text-[10px] font-semibold uppercase tracking-widest" style="color: #fb7185;">Total Kerugian Finansial</span>
            <div class="text-xl font-bold tracking-tight mt-1" style="color: #fda4af;">
              Rp {{ formatRupiah(totalFinancialLoss) }}
            </div>
            <span class="block text-[9px] mt-1" style="color: #475569;">Berdasarkan Harga Beli Terakhir</span>
          </div>

          <!-- Breakdown metrics -->
          <div class="space-y-3 text-xs">
            <div class="flex justify-between items-center pb-3" style="border-bottom: 1px solid rgba(255,255,255,0.04);">
              <span class="font-semibold uppercase tracking-wide flex items-center gap-2" style="color: #cbd5e1;">
                <span class="w-2 h-2 rounded-full" style="background: #c084fc;"></span>
                Barang Rusak
              </span>
              <div class="text-right">
                <span class="font-bold" style="color: #e2e8f0;">{{ rusakQty }} pcs</span>
                <span class="block text-[10px]" style="color: #475569;">Rp {{ formatRupiah(rusakLoss) }}</span>
              </div>
            </div>
            <div class="flex justify-between items-center pb-3" style="border-bottom: 1px solid rgba(255,255,255,0.04);">
              <span class="font-semibold uppercase tracking-wide flex items-center gap-2" style="color: #cbd5e1;">
                <span class="w-2 h-2 rounded-full" style="background: #fb7185;"></span>
                Barang Hilang
              </span>
              <div class="text-right">
                <span class="font-bold" style="color: #e2e8f0;">{{ hilangQty }} pcs</span>
                <span class="block text-[10px]" style="color: #475569;">Rp {{ formatRupiah(hilangLoss) }}</span>
              </div>
            </div>
          </div>

          <!-- Ratio Visual Bar -->
          <div class="space-y-2">
            <div class="flex justify-between text-[10px] font-semibold">
              <span style="color: #c084fc;">Rusak ({{ ratioRusak }}%)</span>
              <span style="color: #fb7185;">Hilang ({{ ratioHilang }}%)</span>
            </div>
            <div class="relative w-full h-2 rounded-full overflow-hidden flex" style="background: rgba(255,255,255,0.04);">
              <div
                class="h-full rounded-l-full transition-all duration-700 ease-out"
                :style="{ width: `${ratioRusak}%`, background: 'linear-gradient(90deg, rgba(192, 132, 252, 0.6) 0%, rgba(168, 85, 247, 0.4) 100%)' }"
              ></div>
              <div
                class="h-full rounded-r-full transition-all duration-700 ease-out"
                :style="{ width: `${ratioHilang}%`, background: 'linear-gradient(90deg, rgba(244, 63, 94, 0.5) 0%, rgba(251, 113, 133, 0.3) 100%)' }"
              ></div>
            </div>
          </div>
        </div>
      </div>

      <!-- ADMIN RECORD FORM -->
      <div v-else style="background: linear-gradient(135deg, #131b2e 0%, #0f1623 100%); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; overflow: hidden;">
        <div class="px-5 py-3 flex items-center gap-2" style="border-bottom: 1px solid rgba(255,255,255,0.04);">
          <div class="w-1 h-5 rounded-full" style="background: linear-gradient(to bottom, #c084fc, #818cf8);"></div>
          <h3 class="text-xs font-semibold uppercase tracking-wide" style="color: #94a3b8;">Catat Kerugian Inventaris</h3>
        </div>

        <form @submit.prevent="submitRecord" class="p-5 space-y-4">
          <!-- Product Dropdown -->
          <div>
            <label class="block text-[11px] font-semibold mb-1.5" style="color: #64748b;">Pilih Produk</label>
            <select
              v-model="form.produk_id"
              class="w-full px-3 py-2 text-xs rounded-lg"
              style="background: rgba(0,0,0,0.2); color: #e2e8f0; border: 1px solid rgba(255,255,255,0.06);"
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
            <label class="block text-[11px] font-semibold mb-1.5" style="color: #64748b;">Kategori Laporan</label>
            <div class="flex gap-4">
              <label class="inline-flex items-center text-xs cursor-pointer" style="color: #cbd5e1;">
                <input v-model="form.kategori" type="radio" value="rusak" class="mr-1.5" style="accent-color: #818cf8;" />
                Barang Rusak
              </label>
              <label class="inline-flex items-center text-xs cursor-pointer" style="color: #cbd5e1;">
                <input v-model="form.kategori" type="radio" value="hilang" class="mr-1.5" style="accent-color: #818cf8;" />
                Barang Hilang
              </label>
            </div>
          </div>

          <!-- Quantity -->
          <div>
            <label class="block text-[11px] font-semibold mb-1.5" style="color: #64748b;">Jumlah (Qty)</label>
            <input
              v-model.number="form.qty"
              type="number"
              min="1"
              class="w-full px-3 py-2 text-xs rounded-lg"
              style="background: rgba(0,0,0,0.2); color: #e2e8f0; border: 1px solid rgba(255,255,255,0.06);"
              placeholder="1"
              required
            />
          </div>

          <!-- Description -->
          <div>
            <label class="block text-[11px] font-semibold mb-1.5" style="color: #64748b;">Keterangan / Alasan</label>
            <textarea
              v-model="form.keterangan"
              rows="3"
              class="w-full px-3 py-2 text-xs rounded-lg"
              style="background: rgba(0,0,0,0.2); color: #e2e8f0; border: 1px solid rgba(255,255,255,0.06);"
              placeholder="Tulis alasan atau kronologi detail..."
            ></textarea>
          </div>

          <!-- Bukti Fisik File Upload (Damaged goods only) -->
          <div v-if="form.kategori === 'rusak'">
            <label class="block text-[11px] font-semibold mb-1.5" style="color: #64748b;">Bukti Fisik Kerusakan (Foto)</label>
            <input
              type="file"
              accept="image/*"
              @change="handleFileChange"
              class="w-full text-xs cursor-pointer"
              style="color: #64748b;"
            />
          </div>

          <button
            :disabled="submitting || products.length === 0"
            type="submit"
            class="w-full py-2.5 text-xs font-semibold rounded-lg transition-all duration-200 disabled:opacity-40"
            style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.3) 0%, rgba(129, 140, 248, 0.2) 100%); color: #c7d2fe; border: 1px solid rgba(99, 102, 241, 0.2);"
            @mouseenter="($event.currentTarget as HTMLElement).style.background = 'linear-gradient(135deg, rgba(99, 102, 241, 0.4) 0%, rgba(129, 140, 248, 0.3) 100%)'"
            @mouseleave="($event.currentTarget as HTMLElement).style.background = 'linear-gradient(135deg, rgba(99, 102, 241, 0.3) 0%, rgba(129, 140, 248, 0.2) 100%)'"
          >
            {{ submitting ? 'Memproses...' : 'Catat Kerugian' }}
          </button>
        </form>
      </div>

    </div>

    <!-- History Panel -->
    <div class="lg:col-span-2 flex flex-col" style="background: linear-gradient(135deg, #131b2e 0%, #0f1623 100%); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; overflow: hidden;">
      <div class="px-5 py-3 flex justify-between items-center" style="border-bottom: 1px solid rgba(255,255,255,0.04);">
        <h3 class="text-xs font-semibold uppercase tracking-wide" style="color: #94a3b8;">Riwayat Penyesuaian Kerugian</h3>
        <span class="text-[10px] font-medium" style="color: #475569;">Real-time inventory loss logs</span>
      </div>

      <div class="overflow-x-auto flex-1">
        <div v-if="loading" class="p-8 text-center text-xs" style="color: #475569;">
          Memuat data riwayat...
        </div>
        <table v-else class="w-full text-left text-xs border-collapse">
          <thead>
            <tr style="border-bottom: 1px solid rgba(255,255,255,0.06);">
              <th class="py-2.5 px-3 text-[11px] font-semibold uppercase tracking-wider" style="color: #64748b; background: rgba(0,0,0,0.15);">Tanggal</th>
              <th class="py-2.5 px-3 text-[11px] font-semibold uppercase tracking-wider" style="color: #64748b; background: rgba(0,0,0,0.15);">Produk</th>
              <th class="py-2.5 px-3 text-[11px] font-semibold uppercase tracking-wider text-center" style="color: #64748b; background: rgba(0,0,0,0.15);">Kategori</th>
              <th class="py-2.5 px-3 text-[11px] font-semibold uppercase tracking-wider text-center" style="color: #64748b; background: rgba(0,0,0,0.15);">Qty</th>
              <th class="py-2.5 px-3 text-[11px] font-semibold uppercase tracking-wider" style="color: #64748b; background: rgba(0,0,0,0.15);">Keterangan</th>
              <th class="py-2.5 px-3 text-[11px] font-semibold uppercase tracking-wider text-center" style="color: #64748b; background: rgba(0,0,0,0.15);">Bukti</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="history.length === 0">
              <td colspan="6" class="py-8 text-center text-sm" style="color: #475569;">Belum ada catatan kerugian barang.</td>
            </tr>
            <tr
              v-for="item in history"
              :key="item.id"
              class="transition-colors duration-200"
              style="border-bottom: 1px solid rgba(255,255,255,0.03);"
              @mouseenter="($event.currentTarget as HTMLElement).style.background = 'rgba(99, 102, 241, 0.04)'"
              @mouseleave="($event.currentTarget as HTMLElement).style.background = 'transparent'"
            >
              <td class="py-3 px-3 font-mono whitespace-nowrap" style="color: #64748b;">{{ formatDate(item.created_at) }}</td>
              <td class="py-3 px-3">
                <span class="block font-semibold" style="color: #cbd5e1;">{{ item.produk?.nama_produk }}</span>
                <span class="text-[10px] font-mono" style="color: #475569;">{{ item.produk?.kode_produk }}</span>
              </td>
              <td class="py-3 px-3 text-center whitespace-nowrap">
                <span
                  v-if="item.kategori === 'rusak'"
                  class="inline-block px-2 py-0.5 text-[9px] font-semibold uppercase tracking-wide rounded-md"
                  style="background: rgba(192, 132, 252, 0.1); color: #c084fc; border: 1px solid rgba(192, 132, 252, 0.15);"
                >
                  Rusak
                </span>

                <span
                  v-else
                  class="inline-block px-2 py-0.5 text-[9px] font-semibold uppercase tracking-wide rounded-md"
                  style="background: rgba(244, 63, 94, 0.08); color: #fb7185; border: 1px solid rgba(244, 63, 94, 0.15);"
                >
                  Hilang
                </span>
              </td>
              <td class="py-3 px-3 text-center font-bold" style="color: #e2e8f0;">{{ item.qty }}</td>
              <td class="py-3 px-3 max-w-[200px] truncate" style="color: #64748b;" :title="item.keterangan">{{ item.keterangan || '-' }}</td>
              <td class="py-3 px-3 text-center whitespace-nowrap">
                <button
                  v-if="item.bukti_foto"
                  @click="openLightbox(item.bukti_foto)"
                  class="text-[10px] font-semibold px-2.5 py-1 rounded-md transition-all duration-200"
                  style="background: rgba(99, 102, 241, 0.1); color: #818cf8; border: 1px solid rgba(99, 102, 241, 0.2);"
                  @mouseenter="($event.currentTarget as HTMLElement).style.background = 'rgba(99, 102, 241, 0.2)'"
                  @mouseleave="($event.currentTarget as HTMLElement).style.background = 'rgba(99, 102, 241, 0.1)'"
                >
                  Lihat Foto
                </button>
                <span v-else style="color: #334155;">-</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Lightbox Modal -->
    <div v-if="activeBukti" class="fixed inset-0 flex items-center justify-center p-4" style="z-index: 3000; background: rgba(4, 6, 10, 0.85); backdrop-filter: blur(12px);" @click="activeBukti = null">
      <div class="max-w-lg w-full overflow-hidden animate-slideUp" style="background: linear-gradient(145deg, #131b2e 0%, #0b0f17 100%); border: 1px solid rgba(255,255,255,0.08); border-radius: 14px; box-shadow: 0 25px 50px rgba(0,0,0,0.6);" @click.stop>
        <div class="px-5 py-3 flex items-center justify-between" style="border-bottom: 1px solid rgba(255,255,255,0.06);">
          <span class="text-xs font-semibold tracking-wide" style="color: #94a3b8;">Bukti Fisik Kerusakan</span>
          <button @click="activeBukti = null" class="w-7 h-7 rounded-lg flex items-center justify-center text-lg leading-none transition-all duration-200" style="color: #64748b; background: rgba(255,255,255,0.04);" @mouseenter="($event.currentTarget as HTMLElement).style.background = 'rgba(255,255,255,0.08)'" @mouseleave="($event.currentTarget as HTMLElement).style.background = 'rgba(255,255,255,0.04)'">×</button>
        </div>
        <div class="p-4 flex items-center justify-center min-h-[300px]" style="background: rgba(0,0,0,0.2);">
          <img :src="getCleanUrl(activeBukti)" class="max-h-[70vh] object-contain rounded-lg" style="border: 1px solid rgba(255,255,255,0.06); box-shadow: 0 10px 30px rgba(0,0,0,0.4);" alt="Bukti Fisik" />
        </div>
        <div class="px-5 py-3 flex justify-end" style="border-top: 1px solid rgba(255,255,255,0.04);">
          <button
            @click="activeBukti = null"
            class="text-xs font-semibold px-4 py-1.5 rounded-lg transition-all duration-200"
            style="background: rgba(255,255,255,0.04); color: #94a3b8; border: 1px solid rgba(255,255,255,0.06);"
            @mouseenter="($event.currentTarget as HTMLElement).style.background = 'rgba(255,255,255,0.08)'"
            @mouseleave="($event.currentTarget as HTMLElement).style.background = 'rgba(255,255,255,0.04)'"
          >
            Tutup
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
