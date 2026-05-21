<template>
  <div class="space-y-5">
    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
      <div class="bg-white rounded-lg border border-slate-200 p-4">
        <p class="text-xs text-slate-500">Penjualan Bulan Ini</p>
        <p class="text-xl font-bold text-slate-800 mt-1">{{ formatCurrency(stats.penjualan_bulan_ini) }}</p>
      </div>
      <div class="bg-white rounded-lg border border-slate-200 p-4">
        <p class="text-xs text-slate-500">Total Transaksi</p>
        <p class="text-xl font-bold text-slate-800 mt-1">{{ stats.total_transaksi }}</p>
      </div>
      <div class="bg-white rounded-lg border border-slate-200 p-4">
        <p class="text-xs text-slate-500">Pembelian Bulan Ini</p>
        <p class="text-xl font-bold text-slate-800 mt-1">{{ formatCurrency(stats.pembelian_bulan_ini) }}</p>
      </div>
      <div class="bg-white rounded-lg border border-slate-200 p-4">
        <p class="text-xs text-slate-500">Laba Bersih</p>
        <p class="text-xl font-bold" :class="stats.laba_bersih >= 0 ? 'text-green-600' : 'text-red-600'">{{ formatCurrency(stats.laba_bersih) }}</p>
      </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white rounded-lg border border-slate-200">
      <div class="flex items-center justify-between p-4 border-b border-slate-200">
        <h3 class="text-sm font-semibold text-slate-800">Transaksi Terbaru</h3>
        <router-link to="/transaksi" class="text-xs text-blue-600 hover:underline">Lihat semua</router-link>
      </div>
      <div v-if="loading" class="p-8 text-center text-sm text-slate-400">Memuat...</div>
      <table v-else class="w-full">
        <thead>
          <tr class="border-b border-slate-100">
            <th class="text-left text-xs font-medium text-slate-500 px-4 py-2.5">Kode</th>
            <th class="text-left text-xs font-medium text-slate-500 px-4 py-2.5">Waktu</th>
            <th class="text-left text-xs font-medium text-slate-500 px-4 py-2.5">Metode</th>
            <th class="text-right text-xs font-medium text-slate-500 px-4 py-2.5">Total</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="trx in recentTrx" :key="trx.id" class="border-b border-slate-50 hover:bg-slate-50">
            <td class="px-4 py-2.5 text-xs font-mono text-blue-600">{{ trx.kode_transaksi }}</td>
            <td class="px-4 py-2.5 text-xs text-slate-500">{{ formatTime(trx.created_at) }}</td>
            <td class="px-4 py-2.5">
              <span class="text-[11px] px-2 py-0.5 rounded-full" :class="trx.metode_pembayaran === 'tunai' ? 'bg-green-50 text-green-700' : 'bg-blue-50 text-blue-700'">
                {{ trx.metode_pembayaran }}
              </span>
            </td>
            <td class="px-4 py-2.5 text-xs font-medium text-slate-800 text-right">{{ formatCurrency(trx.total) }}</td>
          </tr>
          <tr v-if="recentTrx.length === 0">
            <td colspan="4" class="px-4 py-8 text-center text-sm text-slate-400">Belum ada transaksi</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
      <router-link to="/pos" class="bg-white rounded-lg border border-slate-200 p-4 text-center hover:border-blue-300 hover:bg-blue-50 transition-colors">
        <p class="text-sm font-medium text-slate-700">Buka Kasir</p>
      </router-link>
      <router-link to="/produk/tambah" class="bg-white rounded-lg border border-slate-200 p-4 text-center hover:border-blue-300 hover:bg-blue-50 transition-colors">
        <p class="text-sm font-medium text-slate-700">Tambah Produk</p>
      </router-link>
      <router-link to="/pembelian/tambah" class="bg-white rounded-lg border border-slate-200 p-4 text-center hover:border-blue-300 hover:bg-blue-50 transition-colors">
        <p class="text-sm font-medium text-slate-700">Input Pembelian</p>
      </router-link>
      <router-link to="/laporan/penjualan" class="bg-white rounded-lg border border-slate-200 p-4 text-center hover:border-blue-300 hover:bg-blue-50 transition-colors">
        <p class="text-sm font-medium text-slate-700">Laporan</p>
      </router-link>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import type { DashboardStats, Transaksi } from '@/types'
import { laporanService, transaksiService } from '@/services'

const loading = ref(true)
const stats = ref<DashboardStats>({ penjualan_bulan_ini: 0, pembelian_bulan_ini: 0, laba_bersih: 0, total_transaksi: 0, kerugian_inventaris: 0 })
const recentTrx = ref<Transaksi[]>([])

onMounted(async () => {
  try {
    const [dashRes, trxRes] = await Promise.all([
      laporanService.getDashboard(),
      transaksiService.getAll()
    ])
    stats.value = dashRes.data
    recentTrx.value = (trxRes.data as Transaksi[]).slice(0, 5)
  } catch { /* silent */ }
  finally { loading.value = false }
})

function formatCurrency(v: number) {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(v)
}
function formatTime(d: string) {
  return new Date(d).toLocaleString('id-ID', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' })
}
</script>
