<template>
  <div class="space-y-4">
    <h2 class="text-sm font-semibold text-slate-800">Laporan Penjualan</h2>
    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
      <div v-if="loading" class="p-8 text-center text-sm text-slate-400">Memuat...</div>
      <div v-else class="overflow-x-auto">
        <table class="w-full">
          <thead><tr class="border-b border-slate-100 bg-slate-50">
            <th class="text-left text-xs font-medium text-slate-500 px-4 py-2.5">Kode</th>
            <th class="text-left text-xs font-medium text-slate-500 px-4 py-2.5">Tanggal</th>
            <th class="text-left text-xs font-medium text-slate-500 px-4 py-2.5">Metode</th>
            <th class="text-right text-xs font-medium text-slate-500 px-4 py-2.5">Total</th>
          </tr></thead>
          <tbody>
            <tr v-for="t in list" :key="t.id" class="border-b border-slate-50 hover:bg-slate-50">
              <td class="px-4 py-2.5 text-xs font-mono text-blue-600">{{ t.kode_transaksi }}</td>
              <td class="px-4 py-2.5 text-xs text-slate-500">{{ formatDate(t.created_at) }}</td>
              <td class="px-4 py-2.5"><span class="text-[11px] px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">{{ t.metode_pembayaran }}</span></td>
              <td class="px-4 py-2.5 text-xs font-medium text-slate-800 text-right">{{ formatCurrency(t.total) }}</td>
            </tr>
            <tr v-if="list.length === 0"><td colspan="4" class="px-4 py-8 text-center text-sm text-slate-400">Belum ada data</td></tr>
          </tbody>
          <tfoot v-if="list.length > 0">
            <tr class="bg-slate-50 border-t border-slate-200">
              <td colspan="3" class="px-4 py-2.5 text-xs font-semibold text-slate-700">Total</td>
              <td class="px-4 py-2.5 text-xs font-bold text-slate-800 text-right">{{ formatCurrency(grandTotal) }}</td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import type { Transaksi } from '@/types'
import { transaksiService } from '@/services'

const loading = ref(true)
const list = ref<Transaksi[]>([])
const grandTotal = computed(() => list.value.reduce((s, t) => s + Number(t.total), 0))

onMounted(async () => {
  try { const res = await transaksiService.getAll(); list.value = res.data as Transaksi[] } catch {}
  finally { loading.value = false }
})

function formatCurrency(v: number) { return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(v) }
function formatDate(d: string) { return new Date(d).toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) }
</script>
