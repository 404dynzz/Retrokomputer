<template>
  <div class="max-w-xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-1 h-6 rounded-full bg-gradient-to-b from-indigo-400 to-teal-400"></div>
        <h2 class="text-sm font-semibold tracking-wide uppercase" style="color: #e2e8f0; letter-spacing: 0.08em;">
          Laporan Laba Rugi
        </h2>
      </div>
    </div>

    <!-- Container -->
    <div style="background: linear-gradient(135deg, #131b2e 0%, #0f1623 100%); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; overflow: hidden;">
      <div v-if="loading" class="p-8 text-center text-sm" style="color: #475569;">
        Memuat data laporan...
      </div>
      <div v-else class="p-6 space-y-4 text-xs font-mono">
        <div class="flex justify-between items-center py-2.5" style="border-bottom: 1px solid rgba(255,255,255,0.03);">
          <span style="color: #64748b;">Total Penjualan</span>
          <span class="font-bold" style="color: #cbd5e1;">{{ formatCurrency(stats.penjualan_bulan_ini) }}</span>
        </div>
        <div class="flex justify-between items-center py-2.5" style="border-bottom: 1px solid rgba(255,255,255,0.03);">
          <span style="color: #64748b;">Total Pembelian (HPP)</span>
          <span class="font-bold" style="color: #cbd5e1;">{{ formatCurrency(stats.pembelian_bulan_ini) }}</span>
        </div>
        <div class="flex justify-between items-center py-2.5" style="border-bottom: 1px solid rgba(255,255,255,0.03);">
          <span style="color: #64748b;">Kerugian Inventaris</span>
          <span class="font-bold" style="color: #fb7185;">{{ formatCurrency(stats.kerugian_inventaris) }}</span>
        </div>
        <div class="pt-4 flex justify-between items-center">
          <span class="font-bold text-sm uppercase tracking-wide" style="color: #e2e8f0;">Laba Bersih</span>
          <span class="font-bold text-lg" :style="{ color: stats.laba_bersih >= 0 ? '#34d399' : '#f87171' }">
            {{ formatCurrency(stats.laba_bersih) }}
          </span>
        </div>
        <div class="pt-3 text-[10px] text-center" style="color: #475569; border-top: 1px solid rgba(255,255,255,0.04);">
          Periode: Bulan ini
        </div>
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
