<template>
  <div class="space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
      <h2 class="text-sm font-semibold text-slate-800">Daftar Produk</h2>
      <router-link to="/produk/tambah" class="text-xs font-medium px-3 py-1.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">+ Tambah Produk</router-link>
    </div>

    <div class="bg-white rounded-lg border border-slate-200 p-3">
      <input v-model="search" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Cari produk..." />
    </div>

    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
      <div v-if="loading" class="p-8 text-center text-sm text-slate-400">Memuat...</div>
      <div v-else class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b border-slate-100 bg-slate-50">
              <th class="text-left text-xs font-medium text-slate-500 px-4 py-2.5">Kode</th>
              <th class="text-left text-xs font-medium text-slate-500 px-4 py-2.5">Nama</th>
              <th class="text-left text-xs font-medium text-slate-500 px-4 py-2.5">Kategori</th>
              <th class="text-right text-xs font-medium text-slate-500 px-4 py-2.5">Harga Jual</th>
              <th class="text-center text-xs font-medium text-slate-500 px-4 py-2.5">Stok</th>
              <th class="text-center text-xs font-medium text-slate-500 px-4 py-2.5">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="p in filteredProduk" :key="p.id" class="border-b border-slate-50 hover:bg-slate-50">
              <td class="px-4 py-2.5 text-xs font-mono text-blue-600">{{ p.kode_produk }}</td>
              <td class="px-4 py-2.5 text-sm text-slate-800">{{ p.nama_produk }}</td>
              <td class="px-4 py-2.5"><span class="text-[11px] px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">{{ p.kategori }}</span></td>
              <td class="px-4 py-2.5 text-xs text-slate-800 text-right">{{ formatCurrency(p.harga_jual) }}</td>
              <td class="px-4 py-2.5 text-center">
                <span class="text-xs font-medium" :class="p.stok <= 0 ? 'text-red-500' : p.stok <= p.stok_minimum ? 'text-amber-500' : 'text-green-600'">{{ p.stok }}</span>
              </td>
              <td class="px-4 py-2.5 text-center">
                <div class="flex items-center justify-center gap-1">
                  <router-link :to="`/produk/${p.id}/edit`" class="text-xs px-2 py-1 rounded border border-slate-200 text-slate-600 hover:bg-slate-100">Edit</router-link>
                  <button @click="confirmDelete(p)" class="text-xs px-2 py-1 rounded border border-slate-200 text-red-500 hover:bg-red-50">Hapus</button>
                </div>
              </td>
            </tr>
            <tr v-if="filteredProduk.length === 0">
              <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-400">Tidak ada produk</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Delete Modal -->
    <div v-if="deleteTarget" class="fixed inset-0 bg-black/20 z-50 flex items-center justify-center p-4" @click.self="deleteTarget = null">
      <div class="bg-white rounded-lg border border-slate-200 w-full max-w-sm p-5 animate-slideUp">
        <h3 class="text-sm font-semibold text-slate-800 mb-2">Hapus Produk?</h3>
        <p class="text-xs text-slate-500 mb-4">{{ deleteTarget.nama_produk }} akan dihapus permanen.</p>
        <div class="flex justify-end gap-2">
          <button @click="deleteTarget = null" class="text-xs px-3 py-1.5 rounded-md border border-slate-200 text-slate-600 hover:bg-slate-50">Batal</button>
          <button @click="doDelete" class="text-xs px-3 py-1.5 rounded-md bg-red-600 text-white hover:bg-red-700">Hapus</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import type { Produk } from '@/types'
import { produkService } from '@/services'

const search = ref('')
const loading = ref(true)
const produkList = ref<Produk[]>([])
const deleteTarget = ref<Produk | null>(null)

const filteredProduk = computed(() => {
  if (!search.value) return produkList.value
  const q = search.value.toLowerCase()
  return produkList.value.filter(p => p.nama_produk.toLowerCase().includes(q) || p.kode_produk.toLowerCase().includes(q))
})

onMounted(async () => {
  try {
    const res = await produkService.getAll()
    produkList.value = res.data as Produk[]
  } catch { /* silent */ }
  finally { loading.value = false }
})

function confirmDelete(p: Produk) { deleteTarget.value = p }

async function doDelete() {
  if (!deleteTarget.value) return
  try {
    await produkService.delete(deleteTarget.value.id)
    produkList.value = produkList.value.filter(p => p.id !== deleteTarget.value!.id)
  } catch { alert('Gagal menghapus produk') }
  deleteTarget.value = null
}

function formatCurrency(v: number) {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(v)
}
</script>
