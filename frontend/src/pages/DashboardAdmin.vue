<template>
  <div class="space-y-6">
    <!-- Header Section -->
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-slate-900 mb-1 flex items-center gap-2">
        <span v-if="authStore.isKasir" class="text-retro-orange"></span>
        <span v-else class="text-retro-blue"></span>
        {{ authStore.isKasir ? 'Dashboard Kasir' : 'Dashboard Admin' }}
      </h1>
      <p class="text-sm text-slate-500">
        {{ authStore.isKasir ? 'Kelola dan pantau transaksi penjualan Anda' : 'Pantau operasional toko secara menyeluruh' }}
      </p>
    </div>

    <!-- ============ FILTER BAR ============ -->
    <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-4">
      <div class="flex items-center gap-2 mb-3">
        <span class="text-lg"></span>
        <h3 class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Filter Data</h3>
      </div>
      <div class="flex flex-wrap items-end gap-3">
        <!-- Mode Filter -->
        <div class="flex flex-col gap-1">
          <label class="text-[11px] font-semibold text-slate-500 uppercase">Mode</label>
          <select
            v-model="filterMode"
            @change="onFilterModeChange"
            class="text-xs rounded-md border border-slate-300 px-3 py-2 min-w-[140px] focus:ring-2 focus:ring-retro-orange/30"
          >
            <option value="">Bulan Ini (Default)</option>
            <option value="harian"> Harian</option>
            <option value="mingguan"> Mingguan</option>
            <option value="bulanan"> Bulanan</option>
            <option value="tanggal"> Berdasarkan Tanggal</option>
          </select>
        </div>

        <!-- Sub-filter: Harian (day of week) -->
        <div v-if="filterMode === 'harian'" class="flex flex-col gap-1">
          <label class="text-[11px] font-semibold text-slate-500 uppercase">Pilih Hari</label>
          <select
            v-model="filterValue"
            @change="applyFilter"
            class="text-xs rounded-md border border-slate-300 px-3 py-2 min-w-[130px] focus:ring-2 focus:ring-retro-orange/30"
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
          <label class="text-[11px] font-semibold text-slate-500 uppercase">Pilih Minggu</label>
          <select
            v-model="filterValue"
            @change="applyFilter"
            class="text-xs rounded-md border border-slate-300 px-3 py-2 min-w-[150px] focus:ring-2 focus:ring-retro-orange/30"
          >
            <option value="1">Minggu ke-1</option>
            <option value="2">Minggu ke-2</option>
            <option value="3">Minggu ke-3</option>
            <option value="4">Minggu ke-4</option>
          </select>
        </div>

        <!-- Sub-filter: Bulanan (month) -->
        <div v-if="filterMode === 'bulanan'" class="flex flex-col gap-1">
          <label class="text-[11px] font-semibold text-slate-500 uppercase">Pilih Bulan</label>
          <select
            v-model="filterValue"
            @change="applyFilter"
            class="text-xs rounded-md border border-slate-300 px-3 py-2 min-w-[140px] focus:ring-2 focus:ring-retro-orange/30"
          >
            <option v-for="(name, idx) in monthNames" :key="idx" :value="String(idx + 1)">{{ name }}</option>
          </select>
        </div>

        <!-- Sub-filter: Tanggal (date picker) -->
        <div v-if="filterMode === 'tanggal'" class="flex flex-col gap-1">
          <label class="text-[11px] font-semibold text-slate-500 uppercase">Pilih Tanggal</label>
          <input
            v-model="filterValue"
            type="date"
            @change="applyFilter"
            class="text-xs rounded-md border border-slate-300 px-3 py-2 min-w-[160px] focus:ring-2 focus:ring-retro-orange/30"
          />
        </div>

        <!-- Reset Button -->
        <button
          v-if="filterMode"
          @click="resetFilter"
          class="text-[11px] px-3 py-2 rounded-md border-2 border-red-300 text-red-500 hover:bg-red-50 hover:border-red-400 font-semibold transition-all"
        >
          ✕ Reset
        </button>
      </div>

      <!-- Active Filter Badge -->
      <div v-if="filterMode" class="mt-3 flex items-center gap-2">
        <span class="text-[11px] px-2.5 py-1 rounded-full bg-retro-orange/15 text-retro-orange font-semibold border border-retro-orange/30">
          {{ activeFilterLabel }}
        </span>
      </div>
    </div>

    <!-- ============ KPI STATS CARDS ============ -->
    <div :class="authStore.isKasir ? 'grid-cols-2' : 'grid-cols-2 lg:grid-cols-4'" class="grid gap-4">
      <!-- Penjualan -->
      <div class="bg-gradient-to-br from-green-50 to-white rounded-lg border border-green-200 p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-2">
          <p class="text-xs text-green-600 font-semibold uppercase tracking-wide">{{ kpiPenjualanLabel }}</p>
          <span class="text-2xl"></span>
        </div>
        <p class="text-2xl font-bold text-green-700">{{ formatCurrency(stats.penjualan_bulan_ini) }}</p>
        <p class="text-[11px] text-green-500 mt-1">{{ kpiSublabel }}</p>
      </div>

      <!-- Total Transaksi -->
      <div class="bg-gradient-to-br from-blue-50 to-white rounded-lg border border-blue-200 p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-2">
          <p class="text-xs text-blue-600 font-semibold uppercase tracking-wide">Total Transaksi</p>
          <span class="text-2xl"></span>
        </div>
        <p class="text-2xl font-bold text-blue-700">{{ stats.total_transaksi }}</p>
        <p class="text-[11px] text-blue-500 mt-1">{{ kpiSublabel }}</p>
      </div>

      <!-- Pembelian (Admin Only) -->
      <div v-if="!authStore.isKasir" class="bg-gradient-to-br from-orange-50 to-white rounded-lg border border-orange-200 p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-2">
          <p class="text-xs text-orange-600 font-semibold uppercase tracking-wide">Pembelian Bulan Ini</p>
          <span class="text-2xl"></span>
        </div>
        <p class="text-2xl font-bold text-orange-700">{{ formatCurrency(stats.pembelian_bulan_ini) }}</p>
        <p class="text-[11px] text-orange-500 mt-1">Dari supplier</p>
      </div>

      <!-- Laba Bersih (Admin Only) -->
      <div v-if="!authStore.isKasir" :class="stats.laba_bersih >= 0 ? 'bg-gradient-to-br from-emerald-50 to-white border border-emerald-200 rounded-lg p-4 hover:shadow-md transition-shadow' : 'bg-gradient-to-br from-red-50 to-white border border-red-200 rounded-lg p-4 hover:shadow-md transition-shadow'">
        <div class="flex items-center justify-between mb-2">
          <p class="text-xs font-semibold uppercase tracking-wide" :class="stats.laba_bersih >= 0 ? 'text-emerald-600' : 'text-red-600'">Laba Bersih</p>
          <span class="text-2xl">{{ stats.laba_bersih >= 0 ? '' : '' }}</span>
        </div>
        <p class="text-2xl font-bold" :class="stats.laba_bersih >= 0 ? 'text-emerald-700' : 'text-red-700'">{{ formatCurrency(stats.laba_bersih) }}</p>
        <p class="text-[11px] mt-1" :class="stats.laba_bersih >= 0 ? 'text-emerald-500' : 'text-red-500'">Keuntungan bersih</p>
      </div>
    </div>

    <!-- ============ RECENT TRANSACTIONS LIST ============ -->
    <div class="bg-white rounded-lg border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
      <!-- List Header -->
      <div class="flex items-center justify-between p-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
        <div class="flex items-center gap-2">
          <span class="text-retro-blue text-xl"></span>
          <h3 class="text-sm font-semibold text-slate-800">{{ authStore.isKasir ? 'Transaksi Saya' : 'Transaksi Terbaru' }}</h3>
        </div>
        <div class="flex items-center gap-3">
          <!-- List Filter Dropdown -->
          <select
            v-model="listFilter"
            @change="onListFilterChange"
            class="text-[11px] rounded-md border border-slate-300 px-2 py-1.5 focus:ring-2 focus:ring-retro-blue/30"
          >
            <option value="minggu_ini">Minggu Ini</option>
            <option value="1_minggu">Seminggu yang lalu</option>
            <option value="2_minggu">2 Minggu yang lalu</option>
            <option value="3_minggu">3 Minggu yang lalu</option>
            <option value="1_bulan">Sebulan yang lalu</option>
            <option value="2_bulan">2 Bulan yang lalu</option>
          </select>
          <router-link to="/transaksi" class="text-xs text-retro-blue hover:text-retro-blue-deep font-semibold hover:underline">Lihat semua →</router-link>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="p-8 text-center text-sm text-slate-400">
        <span class="animate-spin inline-block"></span> Memuat...
      </div>

      <!-- Empty State -->
      <div v-else-if="filteredTransactions.length === 0" class="p-8 text-center">
        <p class="text-3xl mb-2"></p>
        <p class="text-sm text-slate-400">Belum ada transaksi pada periode ini</p>
      </div>

      <!-- Transaction Table with Group Separators -->
      <div v-else class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-slate-50 border-b border-slate-200">
            <tr>
              <th class="text-left text-xs font-semibold text-slate-600 px-4 py-3">Kode Transaksi</th>
              <th class="text-left text-xs font-semibold text-slate-600 px-4 py-3">Waktu</th>
              <th class="text-left text-xs font-semibold text-slate-600 px-4 py-3">Metode</th>
              <th class="text-right text-xs font-semibold text-slate-600 px-4 py-3">Total</th>
              <th class="text-center text-xs font-semibold text-slate-600 px-4 py-3">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <template v-for="(group, gIdx) in groupedTransactions" :key="gIdx">
              <!-- Group Separator Row -->
              <tr class="bg-slate-50">
                <td colspan="5" class="px-4 py-2.5 border-b border-t border-slate-200">
                  <div class="flex items-center gap-3">
                    <div class="h-px flex-1 bg-gradient-to-r from-retro-orange/40 to-transparent"></div>
                    <span class="text-[11px] font-bold text-retro-orange uppercase tracking-wider whitespace-nowrap flex items-center gap-1.5">
                      <span></span> {{ group.label }}
                    </span>
                    <div class="h-px flex-1 bg-gradient-to-l from-retro-orange/40 to-transparent"></div>
                    <span class="text-[10px] font-semibold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">{{ group.transactions.length }}</span>
                  </div>
                </td>
              </tr>

              <!-- Transaction Rows -->
              <tr v-for="trx in group.transactions" :key="trx.id" class="hover:bg-slate-50 transition-colors">
                <td class="px-4 py-3 text-xs font-mono text-retro-blue font-semibold">{{ trx.kode_transaksi }}</td>
                <td class="px-4 py-3 text-xs text-slate-600">{{ formatTime(trx.created_at) }}</td>
                <td class="px-4 py-3">
                  <span class="text-[11px] px-2.5 py-1 rounded-full font-semibold" :class="trx.metode_pembayaran === 'tunai' ? 'bg-green-100 text-green-700' : trx.metode_pembayaran === 'transfer' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700'">
                    {{ trx.metode_pembayaran === 'tunai' ? ' Tunai' : trx.metode_pembayaran === 'transfer' ? '💳 Transfer' : '💳 Debit' }}
                  </span>
                </td>
                <td class="px-4 py-3 text-xs font-bold text-slate-900 text-right">{{ formatCurrency(trx.total) }}</td>
                <td class="px-4 py-3 text-center">
                  <router-link
                    :to="`/transaksi/${trx.id}`"
                    class="inline-flex items-center gap-1 px-2.5 py-1.5 text-[10px] font-bold border-2 border-retro-blue text-retro-blue rounded-md hover:bg-retro-blue hover:text-white transition-all"
                  >
                    <span></span> Detail
                  </router-link>
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ============ QUICK ACTIONS (Admin Only) ============ -->
    <div v-if="!authStore.isKasir" class="grid grid-cols-2 sm:grid-cols-4 gap-3">
      <!-- Tambah Produk (Admin Only) -->
      <router-link to="/produk/tambah" class="group bg-gradient-to-br from-retro-blue/10 to-white rounded-lg border-2 border-retro-blue/30 p-4 text-center hover:border-retro-blue hover:bg-retro-blue/5 hover:shadow-lg transition-all">
        <p class="text-2xl mb-1"></p>
        <p class="text-xs font-bold text-retro-blue group-hover:text-retro-blue-deep transition-colors">Tambah Produk</p>
      </router-link>

      <!-- Input Pembelian (Admin Only) -->
      <router-link to="/pembelian/tambah" class="group bg-gradient-to-br from-yellow-100/50 to-white rounded-lg border-2 border-yellow-200 p-4 text-center hover:border-yellow-400 hover:bg-yellow-50 hover:shadow-lg transition-all">
        <p class="text-2xl mb-1"></p>
        <p class="text-xs font-bold text-yellow-700 group-hover:text-yellow-800 transition-colors">Input Pembelian</p>
      </router-link>

      <!-- Laporan Penjualan (Admin Only) -->
      <router-link to="/laporan/penjualan" class="group bg-gradient-to-br from-purple-100/50 to-white rounded-lg border-2 border-purple-200 p-4 text-center hover:border-purple-400 hover:bg-purple-50 hover:shadow-lg transition-all">
        <p class="text-2xl mb-1"></p>
        <p class="text-xs font-bold text-purple-700 group-hover:text-purple-800 transition-colors">Laporan</p>
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
const stats = ref<DashboardStats>({ penjualan_bulan_ini: 0, pembelian_bulan_ini: 0, laba_bersih: 0, total_transaksi: 0, kerugian_inventaris: 0 })
const allTransactions = ref<Transaksi[]>([])

// ===== Filter State =====
const filterMode = ref<string>('')
const filterValue = ref<string>('')
const listFilter = ref<string>('minggu_ini')

const monthNames = [
  'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
  'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
]

const dayNames: Record<string, string> = {
  senin: 'Senin', selasa: 'Selasa', rabu: 'Rabu', kamis: 'Kamis',
  jumat: 'Jumat', sabtu: 'Sabtu', minggu: 'Minggu'
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
        return new Date(filterValue.value).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' })
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
    allTransactions.value = (trxRes.data as Transaksi[])
  } catch { /* silent */ }
  finally { loading.value = false }
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
  return allTransactions.value.filter(trx => {
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
    return [{
      label: activeFilterLabel.value,
      transactions: transactions
    }]
  }

  // Default view: group by day within the week
  const now = new Date()
  const today = new Date(now.getFullYear(), now.getMonth(), now.getDate())
  const yesterday = new Date(today.getTime() - 24 * 60 * 60 * 1000)

  const groups: Record<string, Transaksi[]> = {}

  transactions.forEach(trx => {
    const trxDate = new Date(trx.created_at)
    const trxDay = new Date(trxDate.getFullYear(), trxDate.getMonth(), trxDate.getDate())

    let label: string
    if (trxDay.getTime() === today.getTime()) {
      label = 'Hari Ini'
    } else if (trxDay.getTime() === yesterday.getTime()) {
      label = 'Kemarin'
    } else {
      label = trxDay.toLocaleDateString('id-ID', { weekday: 'long', day: '2-digit', month: 'short' })
    }

    if (!groups[label]) {
      groups[label] = []
    }
    groups[label]!.push(trx)
  })

  return Object.entries(groups).map(([label, txs]) => ({
    label,
    transactions: txs
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
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(v)
}
function formatTime(d: string) {
  return new Date(d).toLocaleString('id-ID', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' })
}
</script>
