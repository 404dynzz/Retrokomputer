<template>
  <div class="h-full flex flex-col lg:flex-row gap-4">
    <!-- Products -->
    <div class="lg:w-[55%] flex flex-col gap-3">
      <div class="bg-white rounded-lg border border-slate-200 p-3">
        <input ref="searchInput" v-model="searchQuery" type="text" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Cari produk..." @input="handleSearch" />
      </div>
      <div v-if="loadingProducts" class="py-12 text-center text-sm text-slate-400">Memuat produk...</div>
      <div v-else class="flex-1 overflow-y-auto">
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
          <button v-for="p in filteredProducts" :key="p.id" @click="addToCart(p)" :disabled="p.stok <= 0"
            class="bg-white rounded-lg border border-slate-200 p-3 text-left hover:border-blue-300 hover:bg-blue-50 transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
          >
            <div class="flex justify-between items-start mb-1">
              <span class="text-[10px] font-mono text-blue-600">{{ p.kode_produk }}</span>
              <span class="text-[10px] font-medium" :class="p.stok > 0 ? 'text-green-600' : 'text-red-500'">{{ p.stok > 0 ? `Stok: ${p.stok}` : 'Habis' }}</span>
            </div>
            <p class="text-sm text-slate-800 font-medium truncate">{{ p.nama_produk }}</p>
            <p class="text-sm font-semibold text-blue-600 mt-1">{{ formatCurrency(p.harga_jual) }}</p>
          </button>
        </div>
        <div v-if="filteredProducts.length === 0" class="py-12 text-center text-sm text-slate-400">Produk tidak ditemukan</div>
      </div>
    </div>

    <!-- Cart -->
    <div class="lg:w-[45%] flex flex-col">
      <div class="bg-white rounded-lg border border-slate-200 flex-1 flex flex-col overflow-hidden">
        <div class="p-3 border-b border-slate-200 flex items-center justify-between">
          <h3 class="text-sm font-semibold text-slate-800">Keranjang <span v-if="cart.totalItems > 0" class="text-xs text-slate-500">({{ cart.totalItems }})</span></h3>
          <button v-if="cart.items.length > 0" @click="cart.clearCart()" class="text-xs text-red-500 hover:underline">Kosongkan</button>
        </div>

        <div class="flex-1 overflow-y-auto p-3 space-y-2">
          <div v-for="item in cart.items" :key="item.produk.id" class="flex items-center gap-2 p-2 rounded-md bg-slate-50 group">
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-slate-800 truncate">{{ item.produk.nama_produk }}</p>
              <p class="text-[11px] text-slate-500">{{ formatCurrency(item.produk.harga_jual) }} × {{ item.qty }}</p>
            </div>
            <div class="flex items-center gap-1">
              <button @click="cart.updateQty(item.produk.id, item.qty - 1)" class="w-6 h-6 rounded border border-slate-300 text-slate-500 hover:bg-slate-200 flex items-center justify-center text-xs">−</button>
              <span class="text-xs w-6 text-center font-medium">{{ item.qty }}</span>
              <button @click="cart.updateQty(item.produk.id, item.qty + 1)" class="w-6 h-6 rounded border border-slate-300 text-slate-500 hover:bg-slate-200 flex items-center justify-center text-xs">+</button>
            </div>
            <p class="text-xs font-semibold text-slate-800 w-20 text-right">{{ formatCurrency(item.subtotal) }}</p>
            <button @click="cart.removeItem(item.produk.id)" class="text-slate-300 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity text-xs">✕</button>
          </div>
          <div v-if="cart.items.length === 0" class="py-12 text-center text-sm text-slate-400">Keranjang kosong</div>
        </div>

        <div class="border-t border-slate-200 p-3 space-y-3 bg-slate-50">
          <div class="flex items-center justify-between">
            <span class="text-sm font-semibold text-slate-800">Total</span>
            <span class="text-lg font-bold text-blue-600">{{ formatCurrency(cart.grandTotal) }}</span>
          </div>
          <div>
            <label class="text-xs text-slate-500 mb-1 block">Pembayaran</label>
            <div class="grid grid-cols-3 gap-2">
              <button v-for="m in methods" :key="m.value" @click="cart.metode_pembayaran = m.value"
                :class="['py-1.5 px-2 rounded-md text-xs border transition-colors', cart.metode_pembayaran === m.value ? 'bg-blue-600 text-white border-blue-600' : 'border-slate-300 text-slate-600 hover:bg-slate-100']"
              >{{ m.label }}</button>
            </div>
          </div>
          <button @click="processPayment" :disabled="cart.items.length === 0 || processing"
            class="w-full py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 disabled:opacity-50 transition-colors"
          >{{ processing ? 'Proses...' : 'Bayar' }}</button>
        </div>
      </div>
    </div>

    <!-- Success -->
    <div v-if="showSuccess" class="fixed inset-0 bg-black/20 z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-lg border border-slate-200 w-full max-w-xs p-6 text-center animate-slideUp">
        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-3">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
        </div>
        <h3 class="text-sm font-semibold text-slate-800 mb-1">Transaksi Berhasil</h3>
        <p class="text-xs text-slate-500 mb-1">{{ lastCode }}</p>
        <p class="text-lg font-bold text-blue-600 mb-4">{{ formatCurrency(lastTotal) }}</p>
        <button @click="showSuccess = false" class="text-xs px-4 py-1.5 rounded-md bg-blue-600 text-white hover:bg-blue-700">OK</button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useCartStore } from '@/stores/cart'
import type { Produk } from '@/types'
import { produkService, transaksiService } from '@/services'

const cart = useCartStore()
const searchQuery = ref('')
const searchInput = ref<HTMLInputElement>()
const showSuccess = ref(false)
const processing = ref(false)
const lastCode = ref('')
const lastTotal = ref(0)
const loadingProducts = ref(true)
const products = ref<Produk[]>([])

const methods = [
  { value: 'tunai', label: 'Tunai' },
  { value: 'debit', label: 'Debit' },
  { value: 'transfer', label: 'Transfer' },
]

onMounted(async () => {
  try {
    const res = await produkService.getAll()
    products.value = (res.data as Produk[]).filter(p => p.status === 'aktif')
  } catch { /* silent */ }
  finally { loadingProducts.value = false }
})

const filteredProducts = computed(() => {
  if (!searchQuery.value) return products.value
  const q = searchQuery.value.toLowerCase()
  return products.value.filter(p => p.nama_produk.toLowerCase().includes(q) || p.kode_produk.toLowerCase().includes(q))
})

function handleSearch() {}

function addToCart(p: Produk) {
  try { cart.addItem(p) } catch (e: any) { alert(e.message) }
}

async function processPayment() {
  if (cart.items.length === 0) return
  processing.value = true
  try {
    const res = await transaksiService.create(cart.getPayload())
    lastCode.value = res.data.kode_transaksi
    lastTotal.value = res.data.total
    // Update local stock
    cart.items.forEach(item => {
      const prod = products.value.find(p => p.id === item.produk.id)
      if (prod) prod.stok -= item.qty
    })
    cart.clearCart()
    showSuccess.value = true
  } catch (e: any) {
    alert(e.response?.data?.message || 'Gagal memproses transaksi')
  } finally { processing.value = false }
}

function formatCurrency(v: number) {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(v)
}
</script>
