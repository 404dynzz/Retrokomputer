<template>
  <div class="space-y-4">
    <h2 class="text-sm font-semibold text-slate-800">Laporan Stok</h2>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
      <div class="bg-white rounded-lg border border-slate-200 p-4">
        <p class="text-xs text-slate-500">Jenis Produk</p>
        <p class="text-xl font-bold text-slate-800 mt-1">{{ stokStats.total_jenis_produk }}</p>
      </div>
      <div class="bg-white rounded-lg border border-slate-200 p-4">
        <p class="text-xs text-slate-500">Stok Aman</p>
        <p class="text-xl font-bold text-green-600 mt-1">{{ stokStats.stok_aman }}</p>
      </div>
      <div class="bg-white rounded-lg border border-slate-200 p-4">
        <p class="text-xs text-slate-500">Stok Rendah</p>
        <p class="text-xl font-bold text-amber-500 mt-1">{{ stokStats.stok_rendah }}</p>
      </div>
      <div class="bg-white rounded-lg border border-slate-200 p-4">
        <p class="text-xs text-slate-500">Stok Habis</p>
        <p class="text-xl font-bold text-red-500 mt-1">{{ stokStats.stok_habis }}</p>
      </div>
    </div>

    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
      <div v-if="loading" class="p-8 text-center text-sm text-slate-400">Memuat...</div>
      <div v-else class="overflow-x-auto">
        <table class="w-full">
          <thead><tr class="border-b border-slate-100 bg-slate-50">
            <th class="text-left text-xs font-medium text-slate-500 px-4 py-2.5">Kode</th>
            <th class="text-left text-xs font-medium text-slate-500 px-4 py-2.5">Produk</th>
            <th class="text-left text-xs font-medium text-slate-500 px-4 py-2.5">Kategori</th>
            <th class="text-center text-xs font-medium text-slate-500 px-4 py-2.5">Stok</th>
            <th class="text-center text-xs font-medium text-slate-500 px-4 py-2.5">Minimum</th>
            <th class="text-left text-xs font-medium text-slate-500 px-4 py-2.5">Status</th>
          </tr></thead>
          <tbody>
            <tr v-for="p in produkList" :key="p.id" class="border-b border-slate-50 hover:bg-slate-50">
              <td class="px-4 py-2.5 text-xs font-mono text-blue-600">{{ p.kode_produk }}</td>
              <td class="px-4 py-2.5 text-sm text-slate-800">{{ p.nama_produk }}</td>
              <td class="px-4 py-2.5 text-xs text-slate-600">{{ p.kategori }}</td>
              <td class="px-4 py-2.5 text-center font-medium text-sm" :class="p.stok <= 0 ? 'text-red-500' : p.stok <= p.stok_minimum ? 'text-amber-500' : 'text-green-600'">{{ p.stok }}</td>
              <td class="px-4 py-2.5 text-center text-xs text-slate-500">{{ p.stok_minimum }}</td>
              <td class="px-4 py-2.5">
                <span class="text-[11px] px-2 py-0.5 rounded-full" :class="p.stok <= 0 ? 'bg-red-50 text-red-600' : p.stok <= p.stok_minimum ? 'bg-amber-50 text-amber-600' : 'bg-green-50 text-green-600'">
                  {{ p.stok <= 0 ? 'Habis' : p.stok <= p.stok_minimum ? 'Rendah' : 'Aman' }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import type { Produk, StokStats } from '@/types'
import { produkService, laporanService } from '@/services'

const loading = ref(true)
const produkList = ref<Produk[]>([])
const stokStats = ref<StokStats>({ total_jenis_produk: 0, stok_aman: 0, stok_rendah: 0, stok_habis: 0 })

onMounted(async () => {
  try {
    const [pRes, sRes] = await Promise.all([produkService.getAll(), laporanService.getStok()])
    produkList.value = pRes.data as Produk[]
    stokStats.value = sRes.data
  } catch {}
  finally { loading.value = false }
})
</script>
