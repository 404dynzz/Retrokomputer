<template>
  <div class="max-w-xl mx-auto space-y-4">
    <h2 class="text-sm font-semibold text-slate-800">Laporan Laba Rugi</h2>
    <div class="bg-white rounded-lg border border-slate-200">
      <div v-if="loading" class="p-8 text-center text-sm text-slate-400">Memuat...</div>
      <div v-else class="p-5 space-y-3 text-sm">
        <div class="flex justify-between"><span class="text-slate-500">Total Penjualan</span><span class="font-medium text-slate-800">{{ formatCurrency(stats.penjualan_bulan_ini) }}</span></div>
        <div class="flex justify-between"><span class="text-slate-500">Total Pembelian (HPP)</span><span class="font-medium text-slate-800">{{ formatCurrency(stats.pembelian_bulan_ini) }}</span></div>
        <div class="flex justify-between"><span class="text-slate-500">Kerugian Inventaris</span><span class="font-medium text-red-500">{{ formatCurrency(stats.kerugian_inventaris) }}</span></div>
        <div class="border-t border-slate-200 pt-3 flex justify-between">
          <span class="font-semibold text-slate-800">Laba Bersih</span>
          <span class="font-bold text-lg" :class="stats.laba_bersih >= 0 ? 'text-green-600' : 'text-red-600'">{{ formatCurrency(stats.laba_bersih) }}</span>
        </div>
        <p class="text-[11px] text-slate-400 pt-2">Periode: Bulan ini</p>
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
  try { const res = await laporanService.getDashboard(); stats.value = res.data } catch {}
  finally { loading.value = false }
})

function formatCurrency(v: number) { return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(v) }
</script>
