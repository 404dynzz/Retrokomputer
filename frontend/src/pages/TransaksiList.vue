<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-1 h-6 rounded-full bg-gradient-to-b from-indigo-400 to-teal-400"></div>
        <h2 class="text-sm font-semibold tracking-wide uppercase" style="color: #e2e8f0; letter-spacing: 0.08em;">
          {{ isOwner ? 'Analisis Penjualan' : 'Riwayat Transaksi' }}
        </h2>
      </div>
      <div v-if="isOwner" class="text-[11px] font-medium px-2.5 py-1 rounded-full" style="background: rgba(99, 102, 241, 0.08); color: #818cf8;">
        Owner
      </div>
    </div>

    <!-- OWNER VIEW: Premium Dashboard -->
    <div v-if="isOwner && !loading" class="space-y-5">
      <!-- KPI Cards Row -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Card 1: Total Omzet -->
        <div class="kpi-card group" style="background: linear-gradient(135deg, #131b2e 0%, #0f1623 100%); border: 1px solid rgba(99, 102, 241, 0.12); border-radius: 12px; padding: 1.25rem; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);">
          <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: rgba(99, 102, 241, 0.1);">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color: #818cf8;">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
              <span class="text-[11px] font-medium uppercase tracking-wider" style="color: #64748b;">Total Omzet</span>
            </div>
          </div>
          <div class="text-xl font-bold tracking-tight" style="color: #e2e8f0;">
            Rp {{ formatRupiah(totalRevenue) }}
          </div>
          <div class="text-[10px] mt-1.5" style="color: #475569;">Volume penjualan terkumpul</div>
          <!-- Subtle accent line -->
          <div class="mt-3 h-[2px] rounded-full" style="background: linear-gradient(90deg, #6366f1 0%, transparent 100%); opacity: 0.4;"></div>
        </div>

        <!-- Card 2: Total Transaksi -->
        <div class="kpi-card group" style="background: linear-gradient(135deg, #131b2e 0%, #0f1623 100%); border: 1px solid rgba(20, 184, 166, 0.12); border-radius: 12px; padding: 1.25rem; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);">
          <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: rgba(20, 184, 166, 0.1);">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color: #5eead4;">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
              </div>
              <span class="text-[11px] font-medium uppercase tracking-wider" style="color: #64748b;">Transaksi</span>
            </div>
          </div>
          <div class="text-xl font-bold tracking-tight" style="color: #e2e8f0;">
            {{ transaksiList.length }} <span class="text-sm font-normal" style="color: #64748b;">trx</span>
          </div>
          <div class="text-[10px] mt-1.5" style="color: #475569;">Jumlah nota kasir tercetak</div>
          <div class="mt-3 h-[2px] rounded-full" style="background: linear-gradient(90deg, #14b8a6 0%, transparent 100%); opacity: 0.4;"></div>
        </div>

        <!-- Card 3: Rata-Rata Transaksi -->
        <div class="kpi-card group" style="background: linear-gradient(135deg, #131b2e 0%, #0f1623 100%); border: 1px solid rgba(168, 85, 247, 0.12); border-radius: 12px; padding: 1.25rem; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);">
          <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: rgba(168, 85, 247, 0.1);">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color: #c084fc;">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
              </div>
              <span class="text-[11px] font-medium uppercase tracking-wider" style="color: #64748b;">Rata-Rata</span>
            </div>
          </div>
          <div class="text-xl font-bold tracking-tight" style="color: #e2e8f0;">
            Rp {{ formatRupiah(avgTransaction) }}
          </div>
          <div class="text-[10px] mt-1.5" style="color: #475569;">Nilai rata-rata per transaksi</div>
          <div class="mt-3 h-[2px] rounded-full" style="background: linear-gradient(90deg, #a855f7 0%, transparent 100%); opacity: 0.4;"></div>
        </div>
      </div>

      <!-- Charts Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <!-- Daily Revenue Trend -->
        <div style="background: linear-gradient(135deg, #131b2e 0%, #0f1623 100%); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; overflow: hidden;">
          <div class="px-5 py-3 flex justify-between items-center" style="border-bottom: 1px solid rgba(255,255,255,0.04);">
            <span class="text-xs font-semibold tracking-wide" style="color: #94a3b8;">Tren Penjualan Harian</span>
            <span class="text-[10px] font-medium px-2 py-0.5 rounded-full" style="background: rgba(99, 102, 241, 0.08); color: #818cf8;">7 Hari</span>
          </div>
          <div class="p-5 min-h-[260px] flex flex-col justify-between">
            <div v-if="trendData.length === 0" class="flex-1 flex items-center justify-center text-xs italic" style="color: #475569;">
              Tidak cukup data penjualan
            </div>
            <div v-else class="relative w-full flex-1 flex items-end justify-between pt-6 px-1 gap-1">
              <!-- Gridlines -->
              <div class="absolute inset-0 flex flex-col justify-between pointer-events-none pb-8">
                <div style="border-bottom: 1px solid rgba(255,255,255,0.03);" class="w-full pt-1"></div>
                <div style="border-bottom: 1px solid rgba(255,255,255,0.03);" class="w-full"></div>
                <div style="border-bottom: 1px solid rgba(255,255,255,0.03);" class="w-full"></div>
                <div style="border-bottom: 1px solid rgba(255,255,255,0.03);" class="w-full"></div>
              </div>

              <!-- Bars -->
              <div
                v-for="(day, idx) in trendData"
                :key="idx"
                class="flex-1 flex flex-col items-center group relative z-10"
              >
                <!-- Tooltip -->
                <div class="absolute -top-8 px-2.5 py-1 rounded-md text-[10px] font-semibold opacity-0 group-hover:opacity-100 transition-all duration-200 pointer-events-none whitespace-nowrap z-20" style="background: rgba(15, 23, 42, 0.95); color: #e2e8f0; border: 1px solid rgba(255,255,255,0.08); box-shadow: 0 8px 25px rgba(0,0,0,0.4);">
                  Rp {{ formatRupiah(day.total) }}
                </div>
                
                <!-- Bar -->
                <div
                  class="w-7 sm:w-9 rounded-md transition-all duration-500 ease-out relative group-hover:scale-x-110"
                  :style="{
                    height: `${day.percentage}%`,
                    background: `linear-gradient(to top, rgba(99, 102, 241, 0.7) 0%, rgba(129, 140, 248, 0.4) 100%)`,
                    boxShadow: `0 0 12px rgba(99, 102, 241, 0.08)`,
                    border: `1px solid rgba(99, 102, 241, 0.15)`
                  }"
                >
                </div>

                <!-- Date Label -->
                <span class="text-[9px] mt-2.5 font-medium" style="color: #475569;">
                  {{ day.label }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Payment Method Breakdowns -->
        <div style="background: linear-gradient(135deg, #131b2e 0%, #0f1623 100%); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; overflow: hidden;">
          <div class="px-5 py-3 flex justify-between items-center" style="border-bottom: 1px solid rgba(255,255,255,0.04);">
            <span class="text-xs font-semibold tracking-wide" style="color: #94a3b8;">Metode Pembayaran</span>
            <span class="text-[10px] font-medium px-2 py-0.5 rounded-full" style="background: rgba(20, 184, 166, 0.08); color: #5eead4;">Breakdown</span>
          </div>
          <div class="p-5 flex-1 flex flex-col justify-center space-y-5">
            <div v-for="method in paymentMethods" :key="method.name" class="space-y-2">
              <div class="flex justify-between items-center">
                <span class="text-xs font-semibold uppercase tracking-wide flex items-center gap-2" style="color: #cbd5e1;">
                  <span class="w-2 h-2 rounded-full" :style="{ background: method.dotColor }"></span>
                  {{ method.name }}
                </span>
                <div class="flex items-center gap-2">
                  <span class="text-xs font-bold" style="color: #e2e8f0;">{{ method.count }}x</span>
                  <span class="text-[10px] font-medium px-1.5 py-0.5 rounded" :style="{ background: method.badgeBg, color: method.badgeText }">
                    {{ method.pct }}%
                  </span>
                </div>
              </div>
              <!-- Progress Bar -->
              <div class="relative w-full h-2 rounded-full overflow-hidden" style="background: rgba(255,255,255,0.04);">
                <div
                  class="h-full rounded-full transition-all duration-700 ease-out"
                  :style="{ width: `${method.pct}%`, background: method.barGradient }"
                >
                </div>
              </div>
              <div class="text-[10px] font-medium" style="color: #475569;">
                Omzet: Rp {{ formatRupiah(method.total) }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- STANDARD VIEW: Data Table (For non-owners or backup) -->
    <div v-else style="background: linear-gradient(135deg, #131b2e 0%, #0f1623 100%); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; overflow: hidden;">
      <div v-if="loading" class="p-8 text-center text-sm" style="color: #475569;">
        Memuat riwayat transaksi...
      </div>
      
      <div v-else class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr style="border-bottom: 1px solid rgba(255,255,255,0.06);">
              <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-wider" style="color: #64748b; background: rgba(0,0,0,0.15);">Kode Transaksi</th>
              <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-wider" style="color: #64748b; background: rgba(0,0,0,0.15);">Tanggal</th>
              <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-wider" style="color: #64748b; background: rgba(0,0,0,0.15);">Operator Kasir</th>
              <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-wider" style="color: #64748b; background: rgba(0,0,0,0.15);">Metode</th>
              <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-wider text-right" style="color: #64748b; background: rgba(0,0,0,0.15);">Total Belanja</th>
              <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-wider text-center" style="color: #64748b; background: rgba(0,0,0,0.15);">Aksi</th>
            </tr>
          </thead>
          <tbody class="text-xs">
            <tr
              v-for="t in transaksiList"
              :key="t.id"
              class="transition-colors duration-200"
              style="border-bottom: 1px solid rgba(255,255,255,0.03);"
              @mouseenter="($event.currentTarget as HTMLElement).style.background = 'rgba(99, 102, 241, 0.04)'"
              @mouseleave="($event.currentTarget as HTMLElement).style.background = 'transparent'"
            >
              <td class="px-4 py-3 font-mono font-bold" style="color: #818cf8;">
                {{ t.kode_transaksi }}
              </td>
              <td class="px-4 py-3" style="color: #64748b;">
                {{ formatDate(t.created_at) }}
              </td>
              <td class="px-4 py-3 font-semibold" style="color: #cbd5e1;">
                {{ t.nama_kasir || t.kasir?.name || '-' }}
              </td>
              <td class="px-4 py-3">
                <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold uppercase" style="background: rgba(255,255,255,0.04); color: #94a3b8; border: 1px solid rgba(255,255,255,0.06);">
                  {{ t.metode_pembayaran }}
                </span>
              </td>
              <td class="px-4 py-3 font-bold text-right" style="color: #e2e8f0;">
                Rp {{ formatRupiah(t.total) }}
              </td>
              <td class="px-4 py-3 text-center flex items-center justify-center gap-1.5">
                <router-link
                  :to="`/transaksi/${t.id}`"
                  class="px-2.5 py-1 text-[10px] font-semibold rounded-md transition-all duration-200"
                  style="background: rgba(99, 102, 241, 0.1); color: #818cf8; border: 1px solid rgba(99, 102, 241, 0.2);"
                  @mouseenter="($event.currentTarget as HTMLElement).style.background = 'rgba(99, 102, 241, 0.2)'"
                  @mouseleave="($event.currentTarget as HTMLElement).style.background = 'rgba(99, 102, 241, 0.1)'"
                >
                  Detail
                </router-link>
                <button
                  @click="printTransaction(t.id)"
                  :disabled="printingId === t.id"
                  class="px-2.5 py-1 text-[10px] font-semibold rounded-md transition-all duration-200 disabled:opacity-40"
                  style="background: rgba(20, 184, 166, 0.1); color: #5eead4; border: 1px solid rgba(20, 184, 166, 0.2);"
                  @mouseenter="($event.currentTarget as HTMLElement).style.background = 'rgba(20, 184, 166, 0.2)'"
                  @mouseleave="($event.currentTarget as HTMLElement).style.background = 'rgba(20, 184, 166, 0.1)'"
                >
                  {{ printingId === t.id ? 'Loading...' : 'Cetak' }}
                </button>
                <button
                  v-if="isAdmin"
                  @click="deleteTransaction(t)"
                  class="px-2.5 py-1 text-[10px] font-semibold rounded-md transition-all duration-200"
                  style="background: rgba(244, 63, 94, 0.08); color: #fb7185; border: 1px solid rgba(244, 63, 94, 0.15);"
                  @mouseenter="($event.currentTarget as HTMLElement).style.background = 'rgba(244, 63, 94, 0.15)'"
                  @mouseleave="($event.currentTarget as HTMLElement).style.background = 'rgba(244, 63, 94, 0.08)'"
                >
                  Hapus
                </button>
              </td>
            </tr>
            <tr v-if="transaksiList.length === 0">
              <td colspan="6" class="px-4 py-8 text-center text-sm" style="color: #475569;">
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
import { transaksiService, settingService } from '@/services'
import { useAuthStore } from '@/stores/auth'
import { printReceipt } from '@/utils/printReceipt'
import { customDialog } from '@/utils/dialog'

const authStore = useAuthStore()
const loading = ref(true)
const transaksiList = ref<Transaksi[]>([])
const printingId = ref<number | null>(null)
const logoText = ref('Retro Komputer')

const isOwner = computed(() => authStore.isOwner)
const isAdmin = computed(() => authStore.isAdmin)

async function fetchTransactions() {
  loading.value = true
  try {
    const res = await transaksiService.getAll()
    transaksiList.value = res.data as Transaksi[]
  } catch (err) {
    console.error(err)
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  await fetchTransactions()
  try {
    const settingRes = await settingService.getActive()
    if (settingRes.data && settingRes.data.logo_text) {
      logoText.value = settingRes.data.logo_text
    }
  } catch {
    // silent
  }
})

async function printTransaction(id: number) {
  printingId.value = id
  try {
    const res = await transaksiService.getById(id)
    if (res.data) {
      printReceipt(res.data, null, logoText.value)
    }
  } catch (err) {
    customDialog.error('Gagal mengambil detail transaksi untuk dicetak.')
  } finally {
    printingId.value = null
  }
}

async function deleteTransaction(t: Transaksi) {
  const confirmed = await customDialog.confirm(`Apakah Anda yakin ingin menghapus transaksi ${t.kode_transaksi}? Tindakan ini akan mengembalikan stok produk dan membatalkan catatan keuangan!`)
  if (!confirmed) {
    return
  }

  try {
    await transaksiService.delete(t.id)
    customDialog.success('Transaksi berhasil dihapus.')
    await fetchTransactions()
  } catch (err: any) {
    const msg = err.response?.data?.message || 'Gagal menghapus transaksi.'
    customDialog.error(msg)
  }
}

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
    { 
      name: 'tunai', count: 0, total: 0, 
      dotColor: '#34d399',
      barGradient: 'linear-gradient(90deg, rgba(52, 211, 153, 0.6) 0%, rgba(16, 185, 129, 0.3) 100%)',
      badgeBg: 'rgba(52, 211, 153, 0.1)',
      badgeText: '#34d399'
    },
    { 
      name: 'debit', count: 0, total: 0, 
      dotColor: '#818cf8',
      barGradient: 'linear-gradient(90deg, rgba(129, 140, 248, 0.6) 0%, rgba(99, 102, 241, 0.3) 100%)',
      badgeBg: 'rgba(129, 140, 248, 0.1)',
      badgeText: '#818cf8'
    },
    { 
      name: 'transfer', count: 0, total: 0, 
      dotColor: '#c084fc',
      barGradient: 'linear-gradient(90deg, rgba(192, 132, 252, 0.6) 0%, rgba(168, 85, 247, 0.3) 100%)',
      badgeBg: 'rgba(192, 132, 252, 0.1)',
      badgeText: '#c084fc'
    }
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

