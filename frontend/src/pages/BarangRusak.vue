<template>
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Form Panel -->
    <div class="lg:col-span-1 bg-white rounded-lg border border-slate-200 p-5 space-y-4 shadow-sm self-start">
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

        <button
          :disabled="submitting || products.length === 0"
          type="submit"
          class="w-full py-2 text-xs font-bold text-white bg-retro-orange hover:bg-orange-600 rounded transition-colors shadow-sm disabled:opacity-50"
        >
          {{ submitting ? 'Memproses...' : 'Catat Kerugian' }}
        </button>
      </form>
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
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-slate-700">
            <tr v-if="history.length === 0">
              <td colspan="5" class="py-8 text-center text-slate-400 font-medium">Belum ada catatan kerugian barang.</td>
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
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { produkService, barangRusakService } from '@/services'
import type { Produk, BarangRusak, BarangRusakPayload } from '@/types'

const products = ref<Produk[]>([])
const history = ref<BarangRusak[]>([])

const loading = ref(true)
const submitting = ref(false)

const form = ref<BarangRusakPayload>({
  produk_id: 0,
  qty: 1,
  kategori: 'rusak',
  keterangan: '',
})

onMounted(async () => {
  await Promise.all([fetchProducts(), fetchHistory()])
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
    alert('Silakan pilih produk terlebih dahulu!')
    return
  }

  const selectedProduct = products.value.find(p => p.id === form.value.produk_id)
  if (selectedProduct && selectedProduct.stok < form.value.qty) {
    alert(`Stok produk tidak mencukupi! Stok saat ini: ${selectedProduct.stok}`)
    return
  }

  submitting.value = true
  try {
    await barangRusakService.create(form.value)
    alert('Kerugian inventaris berhasil dicatat!')
    
    // Reset Form
    form.value = {
      produk_id: 0,
      qty: 1,
      kategori: 'rusak',
      keterangan: '',
    }

    // Refresh Data
    await Promise.all([fetchProducts(), fetchHistory()])
  } catch (err: any) {
    const msg = err.response?.data?.message || 'Gagal menyimpan catatan kerugian.'
    alert(msg)
  } finally {
    submitting.value = false
  }
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
