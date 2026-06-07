<template>
  <div class="space-y-6">
    <!-- Header Section -->
    <div class="mb-8">
      <div class="flex items-center gap-3 mb-2">
        <div
          class="w-1 h-8 bg-gradient-to-b from-retro-orange to-retro-orange/50 rounded-full"
        ></div>
        <h1 class="text-3xl font-bold text-slate-900 dark:text-white font-display">
          {{ authStore.isKasir ? 'Dashboard Kasir' : 'Dashboard Admin' }}
        </h1>
      </div>
      <p class="text-sm text-slate-500 dark:text-slate-400 ml-4">
        {{
          authStore.isKasir
            ? 'Kelola dan pantau transaksi penjualan Anda'
            : 'Pantau operasional toko secara menyeluruh'
        }}
      </p>
    </div>

    <!-- ============ FILTER BAR ============ -->
    <div class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 shadow-sm p-5">
      <h3
        class="text-sm font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wide mb-4 flex items-center gap-2"
      >
        <span class="inline-block w-2 h-2 bg-retro-orange rounded-full"></span>
        Filter Data
      </h3>
      <div class="flex flex-wrap items-end gap-3">
        <!-- Mode Filter -->
        <div class="flex flex-col gap-1">
          <label class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase">Mode</label>
          <select
            v-model="filterMode"
            @change="onFilterModeChange"
            class="text-xs rounded-md border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-200 px-3 py-2 min-w-[140px] focus:ring-2 focus:ring-retro-orange/30"
          >
            <option value="">Bulan Ini (Default)</option>
            <option value="harian">Harian</option>
            <option value="mingguan">Mingguan</option>
            <option value="bulanan">Bulanan</option>
            <option value="tanggal">Berdasarkan Tanggal</option>
          </select>
        </div>

        <!-- Sub-filter: Harian (day of week) -->
        <div v-if="filterMode === 'harian'" class="flex flex-col gap-1">
          <label class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase">Pilih Hari</label>
          <select
            v-model="filterValue"
            @change="applyFilter"
            class="text-xs rounded-md border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-200 px-3 py-2 min-w-[130px] focus:ring-2 focus:ring-retro-orange/30"
          >
            <option value="senin">Senin</option>
            <option value="selasa">Selasa</option>
            <option value="rabu">Rabu</option>
            <option value="kamis">Kamis</option>
            <option value="jumat">Jumat</option>
            <option value="sabtu">Sabtu</option>
            <option value="minggu">Minggu</option>
          </select>
        </div>

        <!-- Sub-filter: Mingguan (week number) -->
        <div v-if="filterMode === 'mingguan'" class="flex flex-col gap-1">
          <label class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase">Pilih Minggu</label>
          <select
            v-model="filterValue"
            @change="applyFilter"
            class="text-xs rounded-md border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-200 px-3 py-2 min-w-[150px] focus:ring-2 focus:ring-retro-orange/30"
          >
            <option value="1">Minggu ke-1</option>
            <option value="2">Minggu ke-2</option>
            <option value="3">Minggu ke-3</option>
            <option value="4">Minggu ke-4</option>
          </select>
        </div>

        <!-- Sub-filter: Bulanan (month) -->
        <div v-if="filterMode === 'bulanan'" class="flex flex-col gap-1">
          <label class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase">Pilih Bulan</label>
          <select
            v-model="filterValue"
            @change="applyFilter"
            class="text-xs rounded-md border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-200 px-3 py-2 min-w-[140px] focus:ring-2 focus:ring-retro-orange/30"
          >
            <option v-for="(name, idx) in monthNames" :key="idx" :value="String(idx + 1)">
              {{ name }}
            </option>
          </select>
        </div>

        <!-- Sub-filter: Tanggal (date picker) -->
        <div v-if="filterMode === 'tanggal'" class="flex flex-col gap-1">
          <label class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase">Pilih Tanggal</label>
          <input
            v-model="filterValue"
            type="date"
            @change="applyFilter"
            class="text-xs rounded-md border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-200 px-3 py-2 min-w-[160px] focus:ring-2 focus:ring-retro-orange/30"
          />
        </div>

        <!-- Reset Button -->
        <button
          v-if="filterMode"
          @click="resetFilter"
          class="text-[11px] px-3 py-2 rounded-md border-2 border-red-300 dark:border-red-500/50 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 hover:border-red-400 font-semibold transition-all"
        >
          ✕ Reset
        </button>
      </div>

      <!-- Active Filter Badge -->
      <div v-if="filterMode" class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700 flex items-center gap-2">
        <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Filter Aktif:</span>
        <span
          class="text-xs px-3 py-1.5 rounded-full bg-retro-orange/10 text-retro-orange font-semibold border border-retro-orange/30"
        >
          {{ activeFilterLabel }}
        </span>
      </div>
    </div>

    <!-- ============ KPI STATS CARDS ============ -->
    <div
      :class="authStore.isKasir ? 'grid-cols-2' : 'grid-cols-2 lg:grid-cols-4'"
      class="grid gap-3 md:gap-4"
    >
      <!-- Penjualan -->
      <div
        class="bg-gradient-to-br from-green-50 to-white dark:from-green-900/20 dark:to-slate-800 rounded-lg border border-green-200/50 dark:border-green-800/50 p-3 md:p-5 hover:shadow-lg transition-all hover:border-green-300 dark:hover:border-green-700 min-h-[120px] md:min-h-[140px] flex flex-col justify-between"
      >
        <div>
          <p
            class="text-[10px] md:text-xs text-green-600 dark:text-green-400 font-bold uppercase tracking-wider mb-1 md:mb-2 line-clamp-1"
          >
            {{ kpiPenjualanLabel }}
          </p>
          <p class="text-xl md:text-3xl font-bold text-green-700 dark:text-green-300 mb-1 line-clamp-2 break-words">
            {{ formatCurrency(stats.penjualan_bulan_ini) }}
          </p>
        </div>
        <p class="text-[10px] md:text-xs text-green-500 dark:text-green-500/80 font-medium">{{ kpiSublabel }}</p>
      </div>

      <!-- Total Transaksi -->
      <div
        class="bg-gradient-to-br from-blue-50 to-white dark:from-blue-900/20 dark:to-slate-800 rounded-lg border border-blue-200/50 dark:border-blue-800/50 p-3 md:p-5 hover:shadow-lg transition-all hover:border-blue-300 dark:hover:border-blue-700 min-h-[120px] md:min-h-[140px] flex flex-col justify-between"
      >
        <div>
          <p
            class="text-[10px] md:text-xs text-blue-600 dark:text-blue-400 font-bold uppercase tracking-wider mb-1 md:mb-2 line-clamp-1"
          >
            Total Transaksi
          </p>
          <p class="text-xl md:text-3xl font-bold text-blue-700 dark:text-blue-300 mb-1 line-clamp-2">
            {{ stats.total_transaksi }}
          </p>
        </div>
        <p class="text-[10px] md:text-xs text-blue-500 dark:text-blue-500/80 font-medium">{{ kpiSublabel }}</p>
      </div>

      <!-- Pembelian (Admin Only) -->
      <div
        v-if="!authStore.isKasir"
        class="bg-gradient-to-br from-orange-50 to-white dark:from-orange-900/20 dark:to-slate-800 rounded-lg border border-orange-200/50 dark:border-orange-800/50 p-3 md:p-5 hover:shadow-lg transition-all hover:border-orange-300 dark:hover:border-orange-700 min-h-[120px] md:min-h-[140px] flex flex-col justify-between"
      >
        <div>
          <p
            class="text-[10px] md:text-xs text-orange-600 dark:text-orange-400 font-bold uppercase tracking-wider mb-1 md:mb-2 line-clamp-1"
          >
            Pembelian Bulan Ini
          </p>
          <p class="text-xl md:text-3xl font-bold text-orange-700 dark:text-orange-300 mb-1 line-clamp-2 break-words">
            {{ formatCurrency(stats.pembelian_bulan_ini) }}
          </p>
        </div>
        <p class="text-[10px] md:text-xs text-orange-500 dark:text-orange-500/80 font-medium">Dari supplier</p>
      </div>

      <!-- Laba Bersih (Admin Only) -->
      <div
        v-if="!authStore.isKasir"
        :class="[
          stats.laba_bersih >= 0
            ? 'bg-gradient-to-br from-emerald-50 to-white dark:from-emerald-900/20 dark:to-slate-800 border border-emerald-200/50 dark:border-emerald-800/50 hover:border-emerald-300 dark:hover:border-emerald-700'
            : 'bg-gradient-to-br from-red-50 to-white dark:from-red-900/20 dark:to-slate-800 border border-red-200/50 dark:border-red-800/50 hover:border-red-300 dark:hover:border-red-700',
          'rounded-lg p-3 md:p-5 hover:shadow-lg transition-all min-h-[120px] md:min-h-[140px] flex flex-col justify-between',
        ]"
      >
        <div>
          <p
            class="text-[10px] md:text-xs font-bold uppercase tracking-wider mb-1 md:mb-2 line-clamp-1"
            :class="stats.laba_bersih >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400'"
          >
            Laba Bersih
          </p>
          <p
            class="text-xl md:text-3xl font-bold mb-1 line-clamp-2 break-words"
            :class="stats.laba_bersih >= 0 ? 'text-emerald-700 dark:text-emerald-300' : 'text-red-700 dark:text-red-300'"
          >
            {{ formatCurrency(stats.laba_bersih) }}
          </p>
        </div>
        <p
          class="text-[10px] md:text-xs font-medium"
          :class="stats.laba_bersih >= 0 ? 'text-emerald-500 dark:text-emerald-500/80' : 'text-red-500 dark:text-red-500/80'"
        >
          Keuntungan bersih
        </p>
      </div>
    </div>

    <!-- ============ RECENT TRANSACTIONS LIST ============ -->
    <div
      class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-md transition-shadow"
    >
      <!-- List Header -->
      <div
        class="flex items-center justify-between p-5 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-slate-50 to-white dark:from-slate-800 dark:to-slate-800"
      >
        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 flex items-center gap-2.5">
          <span class="inline-block w-1 h-1 bg-retro-blue rounded-full"></span>
          {{ authStore.isKasir ? 'Transaksi Saya' : 'Transaksi Terbaru' }}
        </h3>
        <div class="flex items-center gap-3">
          <!-- List Filter Dropdown -->
          <select
            v-model="listFilter"
            @change="onListFilterChange"
            class="text-[11px] rounded-md border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-200 px-2 py-1.5 focus:ring-2 focus:ring-retro-blue/30"
          >
            <option value="minggu_ini">Minggu Ini</option>
            <option value="1_minggu">Seminggu yang lalu</option>
            <option value="2_minggu">2 Minggu yang lalu</option>
            <option value="3_minggu">3 Minggu yang lalu</option>
            <option value="1_bulan">Sebulan yang lalu</option>
            <option value="2_bulan">2 Bulan yang lalu</option>
          </select>
          <router-link
            to="/transaksi"
            class="text-xs text-retro-blue hover:text-retro-blue-deep font-semibold hover:underline"
            >Lihat semua →</router-link
          >
        </div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="p-8 text-center text-sm text-slate-400">
        <span class="inline-block animate-spin mr-2">●</span> Memuat...
      </div>

      <!-- Empty State -->
      <div v-else-if="filteredTransactions.length === 0" class="p-8 text-center">
        <p class="text-sm text-slate-400">Belum ada transaksi pada periode ini</p>
      </div>

      <!-- Transaction Table with Group Separators -->
      <div v-else class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-slate-50 dark:bg-slate-700/50 border-b border-slate-200 dark:border-slate-700">
            <tr>
              <th class="text-left text-xs font-semibold text-slate-600 dark:text-slate-300 px-4 py-3">
                Kode Transaksi
              </th>
              <th class="text-left text-xs font-semibold text-slate-600 dark:text-slate-300 px-4 py-3">Waktu</th>
              <th class="text-left text-xs font-semibold text-slate-600 dark:text-slate-300 px-4 py-3">Metode</th>
              <th class="text-right text-xs font-semibold text-slate-600 dark:text-slate-300 px-4 py-3">Total</th>
              <th class="text-center text-xs font-semibold text-slate-600 dark:text-slate-300 px-4 py-3">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
            <template v-for="(group, gIdx) in groupedTransactions" :key="gIdx">
              <!-- Group Separator Row -->
              <tr class="bg-slate-50 dark:bg-slate-700/30">
                <td colspan="5" class="px-4 py-2.5 border-b border-t border-slate-200 dark:border-slate-700">
                  <div class="flex items-center gap-3">
                    <div
                      class="h-px flex-1 bg-gradient-to-r from-retro-orange/40 to-transparent"
                    ></div>
                    <span
                      class="text-[11px] font-bold text-retro-orange uppercase tracking-wider whitespace-nowrap"
                    >
                      {{ group.label }}
                    </span>
                    <div
                      class="h-px flex-1 bg-gradient-to-l from-retro-orange/40 to-transparent"
                    ></div>
                    <span
                      class="text-[10px] font-semibold text-slate-400 bg-slate-100 dark:bg-slate-700 dark:text-slate-300 px-2 py-0.5 rounded-full"
                      >{{ group.transactions.length }}</span
                    >
                  </div>
                </td>
              </tr>

              <!-- Transaction Rows -->
              <tr
                v-for="trx in group.transactions"
                :key="trx.id"
                class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors"
              >
                <td class="px-4 py-3 text-xs font-mono text-retro-blue font-semibold">
                  {{ trx.kode_transaksi }}
                </td>
                <td class="px-4 py-3 text-xs text-slate-600 dark:text-slate-400">{{ formatTime(trx.created_at) }}</td>
                <td class="px-4 py-3">
                  <span
                    class="text-[11px] px-2.5 py-1 rounded-full font-semibold"
                    :class="
                      trx.metode_pembayaran === 'tunai'
                        ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                        : trx.metode_pembayaran === 'transfer'
                          ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400'
                          : 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400'
                    "
                  >
                    {{
                      trx.metode_pembayaran === 'tunai'
                        ? 'Tunai'
                        : trx.metode_pembayaran === 'transfer'
                          ? 'Transfer'
                          : 'Debit'
                    }}
                  </span>
                </td>
                <td class="px-4 py-3 text-xs font-bold text-slate-900 dark:text-slate-100 text-right">
                  {{ formatCurrency(trx.total) }}
                </td>
                <td class="px-4 py-3 text-center">
                  <router-link
                    :to="`/transaksi/${trx.id}`"
                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-[10px] font-bold border-2 border-retro-blue text-retro-blue rounded-md hover:bg-retro-blue hover:text-white transition-all"
                  >
                    Detail
                  </router-link>
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ============ QUICK ACTIONS (Admin Only) ============ -->
    <div v-if="!authStore.isKasir" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <!-- Tambah Produk (Admin Only) -->
      <router-link
        to="/produk/tambah"
        class="group bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 p-5 hover:shadow-lg hover:border-retro-blue/50 transition-all"
      >
        <div class="flex items-start justify-between mb-3">
          <div
            class="w-12 h-12 rounded-lg bg-retro-blue/10 flex items-center justify-center group-hover:bg-retro-blue/20 transition-colors"
          >
            <svg
              class="w-6 h-6 text-retro-blue"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 4v16m8-8H4"
              />
            </svg>
          </div>
          <span
            class="text-xs font-semibold text-slate-400 group-hover:text-retro-blue transition-colors"
            >→</span
          >
        </div>
        <h4 class="text-sm font-bold text-slate-900 dark:text-slate-100 mb-1">Tambah Produk</h4>
        <p class="text-xs text-slate-500 dark:text-slate-400">Tambahkan produk baru ke katalog</p>
      </router-link>

      <!-- Input Pembelian (Admin Only) -->
      <router-link
        to="/pembelian/tambah"
        class="group bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 p-5 hover:shadow-lg hover:border-retro-orange/50 transition-all"
      >
        <div class="flex items-start justify-between mb-3">
          <div
            class="w-12 h-12 rounded-lg bg-retro-orange/10 flex items-center justify-center group-hover:bg-retro-orange/20 transition-colors"
          >
            <svg
              class="w-6 h-6 text-retro-orange"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"
              />
            </svg>
          </div>
          <span
            class="text-xs font-semibold text-slate-400 group-hover:text-retro-orange transition-colors"
            >→</span
          >
        </div>
        <h4 class="text-sm font-bold text-slate-900 dark:text-slate-100 mb-1">Input Pembelian</h4>
        <p class="text-xs text-slate-500 dark:text-slate-400">Catat pembelian dari supplier</p>
      </router-link>

      <!-- Laporan Penjualan (Admin Only) -->
      <router-link
        to="/laporan/penjualan"
        class="group bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 p-5 hover:shadow-lg hover:border-slate-400/50 transition-all"
      >
        <div class="flex items-start justify-between mb-3">
          <div
            class="w-12 h-12 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center group-hover:bg-slate-200 dark:group-hover:bg-slate-600 transition-colors"
          >
            <svg
              class="w-6 h-6 text-slate-600 dark:text-slate-300"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
              />
            </svg>
          </div>
          <span
            class="text-xs font-semibold text-slate-400 group-hover:text-slate-600 dark:group-hover:text-slate-300 transition-colors"
            >→</span
          >
        </div>
        <h4 class="text-sm font-bold text-slate-900 dark:text-slate-100 mb-1">Laporan</h4>
        <p class="text-xs text-slate-500 dark:text-slate-400">Lihat laporan penjualan lengkap</p>
      </router-link>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import type { DashboardStats, Transaksi, DashboardFilterParams } from '@/types'
import { laporanService, transaksiService } from '@/services'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const loading = ref(true)
const stats = ref<DashboardStats>({
  penjualan_bulan_ini: 0,
  pembelian_bulan_ini: 0,
  laba_bersih: 0,
  total_transaksi: 0,
  kerugian_inventaris: 0,
})
const allTransactions = ref<Transaksi[]>([])

// ===== Filter State =====
const filterMode = ref<string>('')
const filterValue = ref<string>('')
const listFilter = ref<string>('minggu_ini')

const monthNames = [
  'Januari',
  'Februari',
  'Maret',
  'April',
  'Mei',
  'Juni',
  'Juli',
  'Agustus',
  'September',
  'Oktober',
  'November',
  'Desember',
]

const dayNames: Record<string, string> = {
  senin: 'Senin',
  selasa: 'Selasa',
  rabu: 'Rabu',
  kamis: 'Kamis',
  jumat: 'Jumat',
  sabtu: 'Sabtu',
  minggu: 'Minggu',
}

// ===== Computed: active filter label =====
const activeFilterLabel = computed(() => {
  if (!filterMode.value) return 'Bulan Ini'
  switch (filterMode.value) {
    case 'harian':
      return `Hari ${dayNames[filterValue.value] || filterValue.value}`
    case 'mingguan':
      return `Minggu ke-${filterValue.value}`
    case 'bulanan':
      return monthNames[parseInt(filterValue.value) - 1] || filterValue.value
    case 'tanggal':
      if (filterValue.value) {
        return new Date(filterValue.value).toLocaleDateString('id-ID', {
          day: '2-digit',
          month: 'long',
          year: 'numeric',
        })
      }
      return 'Pilih tanggal'
    default:
      return 'Bulan Ini'
  }
})

// ===== KPI Labels =====
const kpiPenjualanLabel = computed(() => {
  if (!filterMode.value) return 'Penjualan Bulan Ini'
  switch (filterMode.value) {
    case 'harian':
      return `Penjualan Hari ${dayNames[filterValue.value] || ''}`
    case 'mingguan':
      return `Penjualan Minggu ke-${filterValue.value}`
    case 'bulanan':
      return `Penjualan ${monthNames[parseInt(filterValue.value) - 1] || ''}`
    case 'tanggal':
      if (filterValue.value) {
        return `Penjualan ${new Date(filterValue.value).toLocaleDateString('id-ID', { day: '2-digit', month: 'short' })}`
      }
      return 'Penjualan Tanggal'
    default:
      return 'Penjualan Bulan Ini'
  }
})

const kpiSublabel = computed(() => {
  if (!filterMode.value) return 'Data bulan ini'
  switch (filterMode.value) {
    case 'harian':
      return `Setiap hari ${dayNames[filterValue.value] || ''} bulan ini`
    case 'mingguan':
      return `Minggu ke-${filterValue.value} bulan ini`
    case 'bulanan':
      return `Bulan ${monthNames[parseInt(filterValue.value) - 1] || ''}`
    case 'tanggal':
      return `Tanggal ${filterValue.value}`
    default:
      return 'Data bulan ini'
  }
})

// ===== Filter Logic =====
function buildFilterParams(): DashboardFilterParams | undefined {
  if (!filterMode.value || !filterValue.value) return undefined
  return {
    filter_mode: filterMode.value as DashboardFilterParams['filter_mode'],
    filter_value: filterValue.value,
    filter_year: new Date().getFullYear(),
  }
}

function onFilterModeChange() {
  // Set default value for sub-filter
  switch (filterMode.value) {
    case 'harian':
      filterValue.value = 'senin'
      break
    case 'mingguan':
      filterValue.value = '1'
      break
    case 'bulanan':
      filterValue.value = String(new Date().getMonth() + 1)
      break
    case 'tanggal':
      filterValue.value = new Date().toISOString().split('T')[0] || ''
      break
    default:
      filterValue.value = ''
      break
  }
  listFilter.value = 'minggu_ini' // reset list filter when KPI filter changes
  applyFilter()
}

function resetFilter() {
  filterMode.value = ''
  filterValue.value = ''
  listFilter.value = 'minggu_ini'
  applyFilter()
}

async function applyFilter() {
  loading.value = true
  try {
    const params = buildFilterParams()
    const [dashRes, trxRes] = await Promise.all([
      laporanService.getDashboard(params),
      transaksiService.getAll(params),
    ])
    stats.value = dashRes.data
    allTransactions.value = trxRes.data as Transaksi[]
  } catch {
    /* silent */
  } finally {
    loading.value = false
  }
}

// ===== List Filter (week-based) =====
function onListFilterChange() {
  // When list filter changes, sync KPI to match the selected period
  const now = new Date()

  switch (listFilter.value) {
    case 'minggu_ini':
      // Reset to default (current month)
      filterMode.value = ''
      filterValue.value = ''
      break
    case '1_minggu': {
      const oneWeekAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000)
      filterMode.value = 'tanggal'
      filterValue.value = oneWeekAgo.toISOString().split('T')[0] || ''
      break
    }
    case '2_minggu': {
      const twoWeeksAgo = new Date(now.getTime() - 14 * 24 * 60 * 60 * 1000)
      filterMode.value = 'tanggal'
      filterValue.value = twoWeeksAgo.toISOString().split('T')[0] || ''
      break
    }
    case '3_minggu': {
      const threeWeeksAgo = new Date(now.getTime() - 21 * 24 * 60 * 60 * 1000)
      filterMode.value = 'tanggal'
      filterValue.value = threeWeeksAgo.toISOString().split('T')[0] || ''
      break
    }
    case '1_bulan': {
      const prevMonth = new Date(now.getFullYear(), now.getMonth() - 1, 1)
      filterMode.value = 'bulanan'
      filterValue.value = String(prevMonth.getMonth() + 1)
      break
    }
    case '2_bulan': {
      const twoMonthsAgo = new Date(now.getFullYear(), now.getMonth() - 2, 1)
      filterMode.value = 'bulanan'
      filterValue.value = String(twoMonthsAgo.getMonth() + 1)
      break
    }
  }
  applyFilter()
}

// ===== Client-side filtering for list display =====
const filteredTransactions = computed(() => {
  if (!allTransactions.value.length) return []
  const now = new Date()

  // If a KPI filter is active, show all transactions from the API response
  if (filterMode.value) {
    return allTransactions.value
  }

  // Default: show only this week's transactions
  const startOfWeek = getStartOfWeek(now)
  return allTransactions.value.filter((trx) => {
    const trxDate = new Date(trx.created_at)
    return trxDate >= startOfWeek
  })
})

// ===== Group Transactions by Time Period =====
interface TransactionGroup {
  label: string
  transactions: Transaksi[]
}

const groupedTransactions = computed<TransactionGroup[]>(() => {
  const transactions = filteredTransactions.value
  if (!transactions.length) return []

  // If a filter is active, group all under one label
  if (filterMode.value) {
    return [
      {
        label: activeFilterLabel.value,
        transactions: transactions,
      },
    ]
  }

  // Default view: group by day within the week
  const now = new Date()
  const today = new Date(now.getFullYear(), now.getMonth(), now.getDate())
  const yesterday = new Date(today.getTime() - 24 * 60 * 60 * 1000)

  const groups: Record<string, Transaksi[]> = {}

  transactions.forEach((trx) => {
    const trxDate = new Date(trx.created_at)
    const trxDay = new Date(trxDate.getFullYear(), trxDate.getMonth(), trxDate.getDate())

    let label: string
    if (trxDay.getTime() === today.getTime()) {
      label = 'Hari Ini'
    } else if (trxDay.getTime() === yesterday.getTime()) {
      label = 'Kemarin'
    } else {
      label = trxDay.toLocaleDateString('id-ID', {
        weekday: 'long',
        day: '2-digit',
        month: 'short',
      })
    }

    if (!groups[label]) {
      groups[label] = []
    }
    groups[label]!.push(trx)
  })

  return Object.entries(groups).map(([label, txs]) => ({
    label,
    transactions: txs,
  }))
})

// ===== Helper: get start of current week (Monday) =====
function getStartOfWeek(date: Date): Date {
  const d = new Date(date)
  const day = d.getDay()
  const diff = d.getDate() - day + (day === 0 ? -6 : 1) // Monday
  d.setDate(diff)
  d.setHours(0, 0, 0, 0)
  return d
}

// ===== Lifecycle =====
onMounted(async () => {
  await applyFilter()
})

// ===== Formatters =====
function formatCurrency(v: number) {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(v)
}
function formatTime(d: string) {
  return new Date(d).toLocaleString('id-ID', {
    day: '2-digit',
    month: 'short',
    hour: '2-digit',
    minute: '2-digit',
  })
}
</script>
