<template>
  <div class="space-y-6">
    <!-- Header Section -->
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-slate-900 mb-1 flex items-center gap-2">
        <span v-if="authStore.isKasir" class="text-retro-orange">🛒</span>
        <span v-else class="text-retro-blue">📊</span>
        {{ authStore.isKasir ? 'Kasir POS' : 'Dashboard Admin' }}
      </h1>
      <p class="text-sm text-slate-500">
        {{ authStore.isKasir ? 'Kelola transaksi penjualan Anda' : 'Pantau operasional toko secara menyeluruh' }}
      </p>
    </div>

    <!-- Stats -->
    <div :class="authStore.isKasir ? 'grid-cols-2' : 'grid-cols-2 lg:grid-cols-4'" class="grid gap-4">
      <!-- Penjualan -->
      <div class="bg-gradient-to-br from-green-50 to-white rounded-lg border border-green-200 p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-2">
          <p class="text-xs text-green-600 font-semibold uppercase tracking-wide">Penjualan Bulan Ini</p>
          <span class="text-2xl">💰</span>
        </div>
        <p class="text-2xl font-bold text-green-700">{{ formatCurrency(stats.penjualan_bulan_ini) }}</p>
        <p class="text-[11px] text-green-500 mt-1">Total transaksi terakhir</p>
      </div>

      <!-- Total Transaksi -->
      <div class="bg-gradient-to-br from-blue-50 to-white rounded-lg border border-blue-200 p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-2">
          <p class="text-xs text-blue-600 font-semibold uppercase tracking-wide">Total Transaksi</p>
          <span class="text-2xl">📈</span>
        </div>
        <p class="text-2xl font-bold text-blue-700">{{ stats.total_transaksi }}</p>
        <p class="text-[11px] text-blue-500 mt-1">Transaksi bulan ini</p>
      </div>

      <!-- Pembelian (Admin Only) -->
      <div v-if="!authStore.isKasir" class="bg-gradient-to-br from-orange-50 to-white rounded-lg border border-orange-200 p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-2">
          <p class="text-xs text-orange-600 font-semibold uppercase tracking-wide">Pembelian Bulan Ini</p>
          <span class="text-2xl">📦</span>
        </div>
        <p class="text-2xl font-bold text-orange-700">{{ formatCurrency(stats.pembelian_bulan_ini) }}</p>
        <p class="text-[11px] text-orange-500 mt-1">Dari supplier</p>
      </div>

      <!-- Laba Bersih (Admin Only) -->
      <div v-if="!authStore.isKasir" :class="stats.laba_bersih >= 0 ? 'bg-gradient-to-br from-emerald-50 to-white border border-emerald-200 rounded-lg p-4 hover:shadow-md transition-shadow' : 'bg-gradient-to-br from-red-50 to-white border border-red-200 rounded-lg p-4 hover:shadow-md transition-shadow'">
        <div class="flex items-center justify-between mb-2">
          <p class="text-xs font-semibold uppercase tracking-wide" :class="stats.laba_bersih >= 0 ? 'text-emerald-600' : 'text-red-600'">Laba Bersih</p>
          <span class="text-2xl">{{ stats.laba_bersih >= 0 ? '✅' : '⚠️' }}</span>
        </div>
        <p class="text-2xl font-bold" :class="stats.laba_bersih >= 0 ? 'text-emerald-700' : 'text-red-700'">{{ formatCurrency(stats.laba_bersih) }}</p>
        <p class="text-[11px] mt-1" :class="stats.laba_bersih >= 0 ? 'text-emerald-500' : 'text-red-500'">Keuntungan bersih</p>
      </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white rounded-lg border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
      <div class="flex items-center justify-between p-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
        <div class="flex items-center gap-2">
          <span class="text-retro-blue text-xl">📋</span>
          <h3 class="text-sm font-semibold text-slate-800">{{ authStore.isKasir ? 'Transaksi Saya' : 'Transaksi Terbaru' }}</h3>
        </div>
        <router-link to="/transaksi" class="text-xs text-retro-blue hover:text-retro-blue-deep font-semibold hover:underline">Lihat semua →</router-link>
      </div>
      <div v-if="loading" class="p-8 text-center text-sm text-slate-400">
        <span class="animate-spin inline-block">⏳</span> Memuat...
      </div>
      <div v-else-if="recentTrx.length === 0" class="p-8 text-center text-sm text-slate-400">
        Belum ada transaksi
      </div>
      <table v-else class="w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-200">
          <tr>
            <th class="text-left text-xs font-semibold text-slate-600 px-4 py-3">Kode Transaksi</th>
            <th class="text-left text-xs font-semibold text-slate-600 px-4 py-3">Waktu</th>
            <th class="text-left text-xs font-semibold text-slate-600 px-4 py-3">Metode</th>
            <th class="text-right text-xs font-semibold text-slate-600 px-4 py-3">Total</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-for="trx in recentTrx" :key="trx.id" class="hover:bg-slate-50 transition-colors">
            <td class="px-4 py-3 text-xs font-mono text-retro-blue font-semibold">{{ trx.kode_transaksi }}</td>
            <td class="px-4 py-3 text-xs text-slate-600">{{ formatTime(trx.created_at) }}</td>
            <td class="px-4 py-3">
              <span class="text-[11px] px-2.5 py-1 rounded-full font-semibold" :class="trx.metode_pembayaran === 'tunai' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700'">
                {{ trx.metode_pembayaran === 'tunai' ? '💵 Tunai' : '💳 Transfer' }}
              </span>
            </td>
            <td class="px-4 py-3 text-xs font-bold text-slate-900 text-right">{{ formatCurrency(trx.total) }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Quick Actions - Berbeda untuk Admin dan Kasir -->
    <div :class="authStore.isKasir ? 'grid-cols-2' : 'grid-cols-2 sm:grid-cols-4'" class="grid gap-3">
      <!-- Buka Kasir (Kasir Only) -->
      <router-link v-if="authStore.isKasir" to="/pos" class="group bg-gradient-to-br from-retro-orange/10 to-white rounded-lg border-2 border-retro-orange/30 p-4 text-center hover:border-retro-orange hover:bg-retro-orange/5 hover:shadow-lg transition-all">
        <p class="text-2xl mb-1">🛒</p>
        <p class="text-xs font-bold text-retro-orange group-hover:text-retro-orange-dark transition-colors">Buka Kasir</p>
      </router-link>

      <!-- Tambah Produk (Admin Only) -->
      <router-link v-if="!authStore.isKasir" to="/produk/tambah" class="group bg-gradient-to-br from-retro-blue/10 to-white rounded-lg border-2 border-retro-blue/30 p-4 text-center hover:border-retro-blue hover:bg-retro-blue/5 hover:shadow-lg transition-all">
        <p class="text-2xl mb-1">➕</p>
        <p class="text-xs font-bold text-retro-blue group-hover:text-retro-blue-deep transition-colors">Tambah Produk</p>
      </router-link>

      <!-- Input Pembelian (Admin Only) -->
      <router-link v-if="!authStore.isKasir" to="/pembelian/tambah" class="group bg-gradient-to-br from-yellow-100/50 to-white rounded-lg border-2 border-yellow-200 p-4 text-center hover:border-yellow-400 hover:bg-yellow-50 hover:shadow-lg transition-all">
        <p class="text-2xl mb-1">📥</p>
        <p class="text-xs font-bold text-yellow-700 group-hover:text-yellow-800 transition-colors">Input Pembelian</p>
      </router-link>

      <!-- Laporan Penjualan (untuk semua) -->
      <router-link to="/laporan/penjualan" class="group bg-gradient-to-br from-purple-100/50 to-white rounded-lg border-2 border-purple-200 p-4 text-center hover:border-purple-400 hover:bg-purple-50 hover:shadow-lg transition-all">
        <p class="text-2xl mb-1">📊</p>
        <p class="text-xs font-bold text-purple-700 group-hover:text-purple-800 transition-colors">Laporan</p>
      </router-link>

      <!-- Pengaturan (Admin Only) -->
      <router-link v-if="!authStore.isKasir" to="/settings" class="group bg-gradient-to-br from-slate-100/50 to-white rounded-lg border-2 border-slate-300 p-4 text-center hover:border-slate-400 hover:bg-slate-100 hover:shadow-lg transition-all">
        <p class="text-2xl mb-1">⚙️</p>
        <p class="text-xs font-bold text-slate-700 group-hover:text-slate-900 transition-colors">Pengaturan</p>
      </router-link>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import type { DashboardStats, Transaksi } from '@/types'
import { laporanService, transaksiService } from '@/services'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
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
