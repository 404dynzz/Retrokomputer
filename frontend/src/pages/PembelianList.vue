<template>
  <div class="space-y-4">
    <div class="flex items-center justify-between">
      <h2 class="text-sm font-semibold text-slate-800">Pembelian</h2>
      <router-link to="/pembelian/tambah" class="text-xs font-medium px-3 py-1.5 bg-blue-600 text-white rounded-md hover:bg-blue-700">+ Tambah</router-link>
    </div>
    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
      <div v-if="loading" class="p-8 text-center text-sm text-slate-400">Memuat...</div>
      <div v-else class="overflow-x-auto">
        <table class="w-full">
          <thead><tr class="border-b border-slate-100 bg-slate-50">
            <th class="text-left text-xs font-medium text-slate-500 px-4 py-2.5">Invoice</th>
            <th class="text-left text-xs font-medium text-slate-500 px-4 py-2.5">Supplier</th>
            <th class="text-left text-xs font-medium text-slate-500 px-4 py-2.5">Tanggal</th>
            <th class="text-right text-xs font-medium text-slate-500 px-4 py-2.5">Total</th>
          </tr></thead>
          <tbody>
            <tr v-for="p in list" :key="p.id" class="border-b border-slate-50 hover:bg-slate-50">
              <td class="px-4 py-2.5 text-xs font-mono text-blue-600">{{ p.invoice }}</td>
              <td class="px-4 py-2.5 text-sm text-slate-700">{{ p.supplier }}</td>
              <td class="px-4 py-2.5 text-xs text-slate-500">{{ formatDate(p.created_at) }}</td>
              <td class="px-4 py-2.5 text-xs font-medium text-slate-800 text-right">{{ formatCurrency(p.total) }}</td>
            </tr>
            <tr v-if="list.length === 0"><td colspan="4" class="px-4 py-8 text-center text-sm text-slate-400">Belum ada data</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import type { Pembelian } from '@/types'
import { pembelianService } from '@/services'

const loading = ref(true)
const list = ref<Pembelian[]>([])

onMounted(async () => {
  try { const res = await pembelianService.getAll(); list.value = res.data as Pembelian[] } catch {}
  finally { loading.value = false }
})

function formatCurrency(v: number) { return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(v) }
function formatDate(d: string) { return new Date(d).toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) }
</script>
