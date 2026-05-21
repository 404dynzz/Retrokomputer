<template>
  <div class="space-y-5">
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
      <div class="bg-white rounded-lg border border-slate-200 p-4">
        <p class="text-xs text-slate-500">Penjualan</p>
        <p class="text-xl font-bold text-slate-800 mt-1">{{ formatCurrency(stats.penjualan_bulan_ini) }}</p>
      </div>
      <div class="bg-white rounded-lg border border-slate-200 p-4">
        <p class="text-xs text-slate-500">Pembelian</p>
        <p class="text-xl font-bold text-slate-800 mt-1">{{ formatCurrency(stats.pembelian_bulan_ini) }}</p>
      </div>
      <div class="bg-white rounded-lg border border-slate-200 p-4">
        <p class="text-xs text-slate-500">Transaksi</p>
        <p class="text-xl font-bold text-slate-800 mt-1">{{ stats.total_transaksi }}</p>
      </div>
      <div class="bg-white rounded-lg border border-slate-200 p-4">
        <p class="text-xs text-slate-500">Laba Bersih</p>
        <p class="text-xl font-bold" :class="stats.laba_bersih >= 0 ? 'text-green-600' : 'text-red-600'">{{ formatCurrency(stats.laba_bersih) }}</p>
      </div>
    </div>

    <div class="bg-white rounded-lg border border-slate-200">
      <div class="p-4 border-b border-slate-200">
        <h3 class="text-sm font-semibold text-slate-800">Ringkasan Bulan Ini</h3>
      </div>
      <div v-if="loading" class="p-8 text-center text-sm text-slate-400">Memuat...</div>
      <div v-else class="p-4 space-y-3 text-sm">
        <div class="flex justify-between"><span class="text-slate-500">Total Penjualan</span><span class="font-medium text-slate-800">{{ formatCurrency(stats.penjualan_bulan_ini) }}</span></div>
        <div class="flex justify-between"><span class="text-slate-500">Total Pembelian</span><span class="font-medium text-slate-800">{{ formatCurrency(stats.pembelian_bulan_ini) }}</span></div>
        <div class="flex justify-between"><span class="text-slate-500">Kerugian Inventaris</span><span class="font-medium text-red-500">{{ formatCurrency(stats.kerugian_inventaris) }}</span></div>
        <div class="flex justify-between pt-3 border-t border-slate-200"><span class="font-semibold text-slate-800">Laba Bersih</span><span class="font-bold" :class="stats.laba_bersih >= 0 ? 'text-green-600' : 'text-red-600'">{{ formatCurrency(stats.laba_bersih) }}</span></div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import type { DashboardStats } from '@/types'
import { laporanService } from '@/services'

const loading = ref(true)
const stats = ref<DashboardStats>({ penjualan_bulan_ini: 0, pembelian_bulan_ini: 0, laba_bersih: 0, total_transaksi: 0, kerugian_inventaris: 0 })

onMounted(async () => {
  try {
    const res = await laporanService.getDashboard()
    stats.value = res.data
  } catch { /* silent */ }
  finally { loading.value = false }
})

function formatCurrency(v: number) {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(v)
}
</script>
