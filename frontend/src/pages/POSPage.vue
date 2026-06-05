<template>
  <div class="h-full flex flex-col lg:flex-row gap-6 font-mono">
    <!-- Products Panel -->
    <div class="lg:w-[55%] flex flex-col gap-4">
      <div class="bg-white rounded-lg border-2 border-retro-blue p-4 shadow-sm">
        <div class="flex items-center justify-between mb-2">
          <label class="text-xs font-bold text-slate-700 uppercase">CARI BARANG</label>
          <span class="text-[10px] text-slate-400 font-sans">Tekan nama barang untuk memasukkan ke keranjang</span>
        </div>
        <input
          ref="searchInput"
          v-model="searchQuery"
          type="text"
          class="w-full px-3 py-2 text-sm border-2 border-slate-200 rounded focus:outline-none focus:border-retro-blue font-sans"
          placeholder="Cari berdasarkan nama atau kode barang..."
        />
      </div>

      <div class="flex-1 overflow-y-auto min-h-[350px]">
        <div v-if="loadingProducts" class="py-12 text-center text-sm text-slate-400 font-mono">
          MEMUAT PRODUK...
        </div>
        <div v-else class="grid grid-cols-2 sm:grid-cols-3 gap-3">
          <button
            v-for="p in filteredProducts"
            :key="p.id"
            @click="addToCart(p)"
            :disabled="p.stok <= 0"
            class="bg-white rounded-lg border-2 border-slate-200 p-3 text-left hover:border-retro-blue hover:bg-slate-50 transition-all disabled:opacity-40 disabled:cursor-not-allowed group relative overflow-hidden shadow-sm"
          >
            <div class="flex justify-between items-start mb-2">
              <span class="text-[10px] font-mono font-bold text-retro-blue bg-retro-blue/5 px-1.5 py-0.5 rounded border border-retro-blue/10">
                {{ p.kode_produk }}
              </span>
              <span
                class="text-[9px] font-bold px-1.5 py-0.5 rounded font-mono uppercase"
                :class="p.stok > 0 ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : 'bg-red-50 text-red-600 border border-red-200'"
              >
                {{ p.stok > 0 ? `Stok: ${p.stok}` : 'Habis' }}
              </span>
            </div>
            <p class="text-xs text-slate-800 font-bold truncate font-sans group-hover:text-retro-blue transition-colors">
              {{ p.nama_produk }}
            </p>
            <p class="text-xs font-bold text-retro-orange-dark mt-2 font-mono">
              {{ formatCurrency(p.harga_jual) }}
            </p>
          </button>
        </div>
        <div v-if="!loadingProducts && filteredProducts.length === 0" class="py-12 text-center text-sm text-slate-400 font-mono">
          BARANG TIDAK DITEMUKAN
        </div>
      </div>
    </div>

    <!-- Cart Panel -->
    <div class="lg:w-[45%] flex flex-col">
      <div class="bg-white rounded-lg border-2 border-retro-blue flex-1 flex flex-col overflow-hidden shadow-sm">
        <!-- Cart Header -->
        <div class="bg-retro-blue text-white px-4 py-3 flex items-center justify-between">
          <span class="font-bold text-xs flex items-center gap-2">
            <span>⊞</span>
            <span>KERANJANG BELANJA</span>
            <span v-if="cart.totalItems > 0" class="bg-white text-retro-blue text-[9px] font-bold px-1.5 py-0.5 rounded-full font-mono">
              {{ cart.totalItems }}
            </span>
          </span>
          <button
            v-if="cart.items.length > 0"
            @click="cart.clearCart()"
            class="text-[10px] font-bold text-retro-yellow hover:underline uppercase transition-colors"
          >
            [Kosongkan]
          </button>
        </div>

        <!-- Cart Items -->
        <div class="flex-1 overflow-y-auto p-4 space-y-3">
          <div
            v-for="item in cart.items"
            :key="item.produk.id"
            class="flex items-center gap-3 p-3 rounded border border-slate-100 bg-slate-50 hover:bg-slate-100/50 transition-colors group"
          >
            <div class="flex-1 min-w-0">
              <p class="text-xs font-bold text-slate-800 truncate font-sans">{{ item.produk.nama_produk }}</p>
              <p class="text-[10px] text-slate-400 font-mono mt-0.5">
                {{ formatCurrency(item.produk.harga_jual) }}
              </p>
            </div>
            
            <!-- Interactive Qty Controller -->
            <div class="flex items-center gap-1 shrink-0">
              <button
                @click="updateCartQty(item.produk.id, item.qty - 1)"
                class="w-6 h-6 rounded border border-slate-300 text-slate-600 bg-white hover:bg-slate-100 flex items-center justify-center text-xs font-bold transition-colors"
              >
                −
              </button>
              
              <!-- Editable Quantity State -->
              <span
                v-if="editingItemId !== item.produk.id"
                @click="startEditQty(item)"
                class="text-xs min-w-[28px] text-center font-bold font-mono cursor-pointer hover:bg-white hover:text-retro-blue hover:border border border-transparent rounded py-0.5 transition-all"
                title="Klik untuk mengubah jumlah secara manual"
              >
                {{ item.qty }}
              </span>
              
              <input
                v-else
                v-model.number="editQtyValue"
                type="number"
                min="1"
                :max="item.produk.stok"
                @blur="saveQty(item)"
                @keydown.enter="saveQty(item)"
                class="w-12 text-center text-xs border-2 border-retro-blue rounded focus:outline-none font-mono py-0.5 bg-white font-bold"
              />

              <button
                @click="updateCartQty(item.produk.id, item.qty + 1)"
                class="w-6 h-6 rounded border border-slate-300 text-slate-600 bg-white hover:bg-slate-100 flex items-center justify-center text-xs font-bold transition-colors"
              >
                +
              </button>
            </div>

            <!-- Subtotal -->
            <p class="text-xs font-bold text-slate-800 w-24 text-right font-mono shrink-0">
              {{ formatCurrency(item.subtotal) }}
            </p>

            <button
              @click="cart.removeItem(item.produk.id)"
              class="text-slate-300 hover:text-red-500 shrink-0 opacity-0 group-hover:opacity-100 transition-opacity font-bold text-xs"
              title="Hapus"
            >
              ✕
            </button>
          </div>
          
          <div v-if="cart.items.length === 0" class="py-12 text-center text-sm text-slate-400 font-mono">
            KERANJANG KOSONG
          </div>
        </div>

        <!-- Checkout Section -->
        <div class="border-t-2 border-slate-200 p-4 space-y-4 bg-slate-50">
          <!-- Nama Pembeli Input -->
          <div class="space-y-1.5">
            <label class="text-[10px] font-bold text-slate-500 uppercase block">NAMA PEMBELI</label>
            <input
              v-model="namaPembeli"
              type="text"
              class="w-full px-3 py-2 text-xs border-2 border-slate-200 rounded focus:outline-none focus:border-retro-blue font-sans bg-white font-bold"
              placeholder="Masukkan nama pembeli..."
            />
          </div>

          <div class="flex items-center justify-between border-b border-slate-200 pb-2">
            <span class="text-xs font-bold text-slate-600 uppercase">TOTAL PEMBAYARAN</span>
            <span class="text-lg font-bold text-retro-blue font-mono">{{ formatCurrency(cart.grandTotal) }}</span>
          </div>

          <!-- Payment Method -->
          <div>
            <label class="text-[10px] font-bold text-slate-500 uppercase mb-1.5 block">METODE PEMBAYARAN</label>
            <div class="grid grid-cols-3 gap-2">
              <button
                v-for="m in methods"
                :key="m.value"
                @click="cart.metode_pembayaran = m.value"
                :class="[
                  'py-2 px-2 rounded text-xs border-2 transition-all font-bold uppercase',
                  cart.metode_pembayaran === m.value
                    ? 'bg-retro-blue text-white border-retro-blue shadow-sm'
                    : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-100'
                ]"
              >
                {{ m.label }}
              </button>
            </div>
          </div>

          <!-- Checkout Button -->
          <button
            @click="processPayment"
            :disabled="cart.items.length === 0 || processing"
            class="w-full py-2.5 text-sm font-bold text-white bg-retro-blue hover:bg-blue-700 rounded disabled:opacity-50 disabled:cursor-not-allowed transition-colors shadow-sm uppercase font-mono"
          >
            {{ processing ? 'MEMPROSES...' : 'BAYAR & SELESAI' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Success Modal -->
    <div v-if="showSuccess" class="fixed inset-0 bg-retro-dark/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div class="bg-white border-2 border-retro-blue rounded-lg w-full max-w-xs overflow-hidden shadow-2xl animate-slideUp font-mono">
        <!-- Title bar -->
        <div class="bg-retro-blue text-white px-4 py-2 flex items-center justify-between">
          <span class="font-bold text-xs">TRANSAKSI BERHASIL</span>
          <button @click="showSuccess = false" class="text-white hover:text-retro-yellow font-bold text-lg leading-none">×</button>
        </div>
        <div class="p-6 text-center">
          <div class="w-12 h-12 rounded-full bg-emerald-50 border-2 border-emerald-300 flex items-center justify-center mx-auto mb-3 text-emerald-500 text-2xl font-bold">✓</div>
          <h3 class="text-sm font-bold text-slate-800 mb-1">Pembayaran Sukses!</h3>
          <p class="text-[10px] text-slate-400 font-bold uppercase font-mono mb-2">{{ lastCode }}</p>
          <p class="text-lg font-bold text-retro-orange-dark font-mono mb-4">{{ formatCurrency(lastTotal) }}</p>
          <button
            @click="showSuccess = false"
            class="text-xs font-bold px-5 py-2 bg-retro-blue hover:bg-blue-700 text-white rounded transition-colors uppercase shadow-sm"
          >
            [Selesai]
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '@/stores/cart'
import { useAuthStore } from '@/stores/auth'
import type { Produk } from '@/types'
import { produkService, transaksiService } from '@/services'

const cart = useCartStore()
const authStore = useAuthStore()
const router = useRouter()

const searchQuery = ref('')
const searchInput = ref<HTMLInputElement>()
const showSuccess = ref(false)
const processing = ref(false)
const lastCode = ref('')
const lastTotal = ref(0)
const loadingProducts = ref(true)
const products = ref<Produk[]>([])
const namaPembeli = ref('')

const editingItemId = ref<number | null>(null)
const editQtyValue = ref<number>(0)

const methods = [
  { value: 'tunai', label: 'Tunai' },
  { value: 'debit', label: 'Debit' },
  { value: 'transfer', label: 'Transfer' },
]

onMounted(async () => {
  if (authStore.isKasir) {
    try {
      await authStore.fetchActiveKasirProfile()
      if (!authStore.activeKasirProfile) {
        alert('Harap aktifkan profil kasir terlebih dahulu pada menu Profil Kasir sebelum melakukan transaksi.')
        router.push('/profil-kasir')
        return
      }
    } catch {
      // ignore
    }
  }

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

function addToCart(p: Produk) {
  try {
    cart.addItem(p)
  } catch (e: any) {
    alert(e.message)
  }
}

function updateCartQty(produkId: number, qty: number) {
  try {
    cart.updateQty(produkId, qty)
  } catch (e: any) {
    alert(e.message)
  }
}

function startEditQty(item: any) {
  editingItemId.value = item.produk.id
  editQtyValue.value = item.qty
  nextTick(() => {
    const inputs = document.querySelectorAll('input[type="number"]')
    const lastInput = inputs[inputs.length - 1] as HTMLInputElement
    if (lastInput) {
      lastInput.focus()
      lastInput.select()
    }
  })
}

function saveQty(item: any) {
  if (editingItemId.value !== item.produk.id) return

  const val = Number(editQtyValue.value)
  if (isNaN(val) || val <= 0) {
    alert('Jumlah barang harus minimal 1.')
    editingItemId.value = null
    return
  }

  try {
    cart.updateQty(item.produk.id, val)
  } catch (e: any) {
    alert(e.message)
  } finally {
    editingItemId.value = null
  }
}

async function processPayment() {
  if (cart.items.length === 0) return
  processing.value = true
  try {
    const res = await transaksiService.create({
      ...cart.getPayload(),
      nama_pembeli: namaPembeli.value.trim() || undefined
    })
    lastCode.value = res.data.kode_transaksi
    lastTotal.value = res.data.total
    // Update local stock
    cart.items.forEach(item => {
      const prod = products.value.find(p => p.id === item.produk.id)
      if (prod) prod.stok -= item.qty
    })
    cart.clearCart()
    namaPembeli.value = ''
    showSuccess.value = true
  } catch (e: any) {
    alert(e.response?.data?.message || 'Gagal memproses transaksi')
  } finally {
    processing.value = false
  }
}

function formatCurrency(v: number) {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(v)
}
</script>

