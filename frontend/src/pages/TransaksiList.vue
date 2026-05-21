<template>
  <div class="space-y-6 font-mono">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-2">
        <span class="text-xl font-bold text-retro-blue">&gt;_</span>
        <h2 class="text-base font-bold text-slate-800 uppercase tracking-wider">
          {{ isOwner ? 'DASBOR ANALISIS PENJUALAN' : 'RIWAYAT TRANSAKSI' }}
        </h2>
      </div>
      <div v-if="isOwner" class="text-xs text-slate-400 font-mono italic">
        [Tingkat Akun: OWNER]
      </div>
    </div>

    <!-- OWNER VIEW: Rich Visual Infographics & Dashboards -->
    <div v-if="isOwner && !loading" class="space-y-6">
      <!-- KPI Cards Row -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Card 1: Total Omzet -->
        <div class="bg-white border-2 border-retro-blue rounded-lg overflow-hidden shadow-sm">
          <div class="bg-retro-blue text-white px-3 py-1.5 text-xs font-bold flex justify-between">
            <span>&gt;_ TOTAL OMZET</span>
            <span>[Rp]</span>
          </div>
          <div class="p-4 space-y-1">
            <div class="text-2xl font-black text-slate-800 tracking-tight">
              Rp {{ formatRupiah(totalRevenue) }}
            </div>
            <div class="text-[10px] text-slate-400">Total volume penjualan terkumpul</div>
          </div>
        </div>

        <!-- Card 2: Total Transaksi -->
        <div class="bg-white border-2 border-retro-orange rounded-lg overflow-hidden shadow-sm">
          <div class="bg-retro-orange text-white px-3 py-1.5 text-xs font-bold flex justify-between">
            <span>&gt;_ TRANSAKSI</span>
            <span>[Qy]</span>
          </div>
          <div class="p-4 space-y-1">
            <div class="text-2xl font-black text-slate-800 tracking-tight">
              {{ transaksiList.length }} <span class="text-xs font-normal text-slate-400">Trx</span>
            </div>
            <div class="text-[10px] text-slate-400">Jumlah nota kasir tercetak</div>
          </div>
        </div>

        <!-- Card 3: Rata-Rata Transaksi -->
        <div class="bg-white border-2 border-retro-yellow rounded-lg overflow-hidden shadow-sm">
          <div class="bg-retro-yellow text-slate-900 px-3 py-1.5 text-xs font-bold flex justify-between">
            <span>&gt;_ RATA-RATA TRX</span>
            <span>[Avg]</span>
          </div>
          <div class="p-4 space-y-1">
            <div class="text-2xl font-black text-slate-800 tracking-tight">
              Rp {{ formatRupiah(avgTransaction) }}
            </div>
            <div class="text-[10px] text-slate-400">Nilai rata-rata per transaksi</div>
          </div>
        </div>
      </div>

      <!-- Charts Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- SVG Daily Revenue Trend -->
        <div class="bg-white border-2 border-slate-200 rounded-lg overflow-hidden shadow-sm flex flex-col">
          <div class="bg-slate-100 border-b border-slate-200 px-4 py-2.5 text-xs font-bold text-slate-700 flex justify-between items-center">
            <span>&gt;_ TREN PENJUALAN HARIAN</span>
            <span class="text-[10px] text-retro-blue font-normal">[Chart: SVG Neon Bar]</span>
          </div>
          <div class="p-4 flex-1 flex flex-col justify-between min-h-[250px]">
            <div v-if="trendData.length === 0" class="flex-1 flex items-center justify-center text-xs text-slate-400 italic">
              Tidak cukup data penjualan
            </div>
            <div v-else class="relative w-full flex-1 flex items-end justify-between pt-6 px-2">
              <!-- Y-Axis Gridlines -->
              <div class="absolute inset-0 flex flex-col justify-between pointer-events-none pb-8 text-[9px] text-slate-300">
                <div class="border-b border-slate-100 w-full pt-1"></div>
                <div class="border-b border-slate-100 w-full"></div>
                <div class="border-b border-slate-100 w-full"></div>
                <div class="border-b border-slate-100 w-full"></div>
              </div>

              <!-- Neon Bars -->
              <div
                v-for="(day, idx) in trendData"
                :key="idx"
                class="flex-1 flex flex-col items-center group relative z-10"
              >
                <!-- Tooltip -->
                <div class="absolute -top-6 bg-slate-800 text-white text-[9px] px-1.5 py-0.5 rounded opacity-0 group-hover:opacity-100 transition-opacity font-bold pointer-events-none whitespace-nowrap shadow">
                  Rp {{ formatRupiah(day.total) }}
                </div>
                
                <!-- Bar -->
                <div class="w-8 sm:w-10 rounded-t bg-gradient-to-t from-retro-blue/90 to-blue-500 hover:to-retro-orange transition-all duration-300 relative shadow-sm border-t border-blue-400" :style="{ height: `${day.percentage}%` }">
                  <div class="absolute inset-0 bg-[linear-gradient(rgba(18,16,16,0)_50%,rgba(0,0,0,0.15)_50%)] bg-[length:100%_4px]"></div>
                </div>

                <!-- Date Label -->
                <span class="text-[9px] text-slate-400 mt-2 font-mono">
                  {{ day.label }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Payment Method Breakdowns -->
        <div class="bg-white border-2 border-slate-200 rounded-lg overflow-hidden shadow-sm flex flex-col">
          <div class="bg-slate-100 border-b border-slate-200 px-4 py-2.5 text-xs font-bold text-slate-700 flex justify-between items-center">
            <span>&gt;_ METODE PEMBAYARAN POPULER</span>
            <span class="text-[10px] text-retro-orange font-normal">[Breakdown: CSS Gauge]</span>
          </div>
          <div class="p-6 flex-1 flex flex-col justify-center space-y-4">
            <div v-for="method in paymentMethods" :key="method.name" class="space-y-1">
              <div class="flex justify-between text-xs font-bold">
                <span class="uppercase text-slate-700 flex items-center gap-1.5">
                  <span class="w-2.5 h-2.5 rounded-full" :class="method.bgClass"></span>
                  {{ method.name }}
                </span>
                <span class="text-slate-800">
                  {{ method.count }}x <span class="text-slate-400 font-normal">({{ method.pct }}%)</span>
                </span>
              </div>
              <div class="relative w-full h-4 bg-slate-150 border border-slate-200 rounded overflow-hidden shadow-inner">
                <!-- Color fill -->
                <div
                  class="h-full bg-gradient-to-r transition-all duration-500 border-r-2"
                  :class="[method.gradient, method.border]"
                  :style="{ width: `${method.pct}%` }"
                >
                  <div class="w-full h-full bg-[linear-gradient(rgba(18,16,16,0)_50%,rgba(0,0,0,0.15)_50%)] bg-[length:100%_4px]"></div>
                </div>
              </div>
              <div class="text-[10px] text-slate-400 flex justify-between font-mono">
                <span>Omzet: Rp {{ formatRupiah(method.total) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- STANDARD VIEW: Data Table (For non-owners or backup) -->
    <div v-else class="bg-white rounded-lg border-2 border-slate-200 overflow-hidden shadow-sm">
      <div v-if="loading" class="p-8 text-center text-sm text-slate-400 font-mono">
        Memuat riwayat transaksi...
      </div>
      
      <div v-else class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="border-b-2 border-slate-200 bg-slate-50 font-bold text-xs text-slate-700">
              <th class="px-4 py-3">KODE TRANSAKSI</th>
              <th class="px-4 py-3">TANGGAL</th>
              <th class="px-4 py-3">OPERATOR KASIR</th>
              <th class="px-4 py-3">METODE</th>
              <th class="px-4 py-3 text-right">TOTAL BELANJA</th>
              <th class="px-4 py-3 text-center">AKSI</th>
            </tr>
          </thead>
          <tbody class="text-xs text-slate-600">
            <tr
              v-for="t in transaksiList"
              :key="t.id"
              class="border-b border-slate-100 hover:bg-slate-50/50 transition-colors"
            >
              <td class="px-4 py-3 font-mono font-bold text-retro-blue">
                {{ t.kode_transaksi }}
              </td>
              <td class="px-4 py-3 text-slate-500">
                {{ formatDate(t.created_at) }}
              </td>
              <td class="px-4 py-3 text-slate-700 font-bold">
                {{ t.kasir?.name || '-' }}
              </td>
              <td class="px-4 py-3">
                <span class="px-2 py-0.5 rounded font-bold uppercase text-[10px] bg-slate-100 border border-slate-350 text-slate-700">
                  {{ t.metode_pembayaran }}
                </span>
              </td>
              <td class="px-4 py-3 font-bold text-slate-800 text-right">
                Rp {{ formatRupiah(t.total) }}
              </td>
              <td class="px-4 py-3 text-center">
                <router-link
                  :to="`/transaksi/${t.id}`"
                  class="px-2 py-1 text-[10px] font-bold border-2 border-retro-blue text-retro-blue rounded hover:bg-retro-blue hover:text-white transition-colors"
                >
                  [DETAIL]
                </router-link>
              </td>
            </tr>
            <tr v-if="transaksiList.length === 0">
              <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-400 font-mono">
                Belum ada transaksi terekam.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import type { Transaksi } from '@/types'
import { transaksiService } from '@/services'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const loading = ref(true)
const transaksiList = ref<Transaksi[]>([])

const isOwner = computed(() => authStore.isOwner)

onMounted(async () => {
  try {
    const res = await transaksiService.getAll()
    transaksiList.value = res.data as Transaksi[]
  } catch (err) {
    console.error(err)
  } finally {
    loading.value = false
  }
})

// KPI Calculations
const totalRevenue = computed(() => {
  return transaksiList.value.reduce((acc, t) => acc + Number(t.total), 0)
})

const avgTransaction = computed(() => {
  if (transaksiList.value.length === 0) return 0
  return totalRevenue.value / transaksiList.value.length
})

// Payment Methods breakdown calculation
const paymentMethods = computed(() => {
  const methods = [
    { name: 'tunai', count: 0, total: 0, bgClass: 'bg-emerald-500', gradient: 'from-emerald-400 to-emerald-600', border: 'border-emerald-600' },
    { name: 'debit', count: 0, total: 0, bgClass: 'bg-blue-500', gradient: 'from-blue-400 to-blue-600', border: 'border-blue-600' },
    { name: 'transfer', count: 0, total: 0, bgClass: 'bg-amber-500', gradient: 'from-amber-400 to-amber-600', border: 'border-amber-600' }
  ]

  transaksiList.value.forEach(t => {
    const match = methods.find(m => m.name === t.metode_pembayaran.toLowerCase())
    if (match) {
      match.count++
      match.total += Number(t.total)
    }
  })

  const totalTrx = transaksiList.value.length || 1

  return methods.map(m => ({
    ...m,
    pct: Math.round((m.count / totalTrx) * 100)
  }))
})

// Group by Date for SVG Daily Revenue trend chart (max 7 days)
const trendData = computed(() => {
  if (transaksiList.value.length === 0) return []
  
  // Sort and group
  const grouped: Record<string, number> = {}
  
  // Clone and sort transactions ascending by date
  const sorted = [...transaksiList.value].sort(
    (a, b) => new Date(a.created_at).getTime() - new Date(b.created_at).getTime()
  )

  sorted.forEach(t => {
    const dateStr = new Date(t.created_at).toLocaleDateString('id-ID', {
      day: 'numeric',
      month: 'short'
    })
    grouped[dateStr] = (grouped[dateStr] || 0) + Number(t.total)
  })

  // Take the last 7 dates
  const keys = Object.keys(grouped).slice(-7)
  const maxTotal = Math.max(...keys.map(k => grouped[k] ?? 0), 1)

  return keys.map(k => {
    const total = grouped[k] ?? 0
    return {
      label: k,
      total,
      percentage: Math.max(Math.round((total / maxTotal) * 100), 5) // ensure at least small visible line
    }
  })
})

function formatRupiah(val: number | string): string {
  if (val === undefined || val === null) return '0'
  return new Intl.NumberFormat('id-ID').format(Number(val))
}

function formatDate(d: string) {
  return new Date(d).toLocaleString('id-ID', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>

