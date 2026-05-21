<template>
  <div class="space-y-4">
    <h2 class="text-sm font-semibold text-slate-800">Riwayat Transaksi</h2>
    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
      <div v-if="loading" class="p-8 text-center text-sm text-slate-400">Memuat...</div>
      <div v-else class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b border-slate-100 bg-slate-50">
              <th class="text-left text-xs font-medium text-slate-500 px-4 py-2.5">Kode</th>
              <th class="text-left text-xs font-medium text-slate-500 px-4 py-2.5">Tanggal</th>
              <th class="text-left text-xs font-medium text-slate-500 px-4 py-2.5">Kasir</th>
              <th class="text-left text-xs font-medium text-slate-500 px-4 py-2.5">Metode</th>
              <th class="text-right text-xs font-medium text-slate-500 px-4 py-2.5">Total</th>
              <th class="text-center text-xs font-medium text-slate-500 px-4 py-2.5">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="t in transaksiList" :key="t.id" class="border-b border-slate-50 hover:bg-slate-50">
              <td class="px-4 py-2.5 text-xs font-mono text-blue-600">{{ t.kode_transaksi }}</td>
              <td class="px-4 py-2.5 text-xs text-slate-500">{{ formatDate(t.created_at) }}</td>
              <td class="px-4 py-2.5 text-xs text-slate-700">{{ t.kasir?.name || '-' }}</td>
              <td class="px-4 py-2.5"><span class="text-[11px] px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">{{ t.metode_pembayaran }}</span></td>
              <td class="px-4 py-2.5 text-xs font-medium text-slate-800 text-right">{{ formatCurrency(t.total) }}</td>
              <td class="px-4 py-2.5 text-center">
                <router-link :to="`/transaksi/${t.id}`" class="text-xs text-blue-600 hover:underline">Detail</router-link>
              </td>
            </tr>
            <tr v-if="transaksiList.length === 0"><td colspan="6" class="px-4 py-8 text-center text-sm text-slate-400">Belum ada transaksi</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import type { Transaksi } from '@/types'
import { transaksiService } from '@/services'

const loading = ref(true)
const transaksiList = ref<Transaksi[]>([])

onMounted(async () => {
  try {
    const res = await transaksiService.getAll()
    transaksiList.value = res.data as Transaksi[]
  } catch { /* silent */ }
  finally { loading.value = false }
})

function formatCurrency(v: number) { return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(v) }
function formatDate(d: string) { return new Date(d).toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) }
</script>
