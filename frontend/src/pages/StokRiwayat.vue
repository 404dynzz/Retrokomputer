<template>
  <div class="space-y-4">
    <h2 class="text-sm font-semibold text-slate-800">Riwayat Stok</h2>
    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
      <div v-if="loading" class="p-8 text-center text-sm text-slate-400">Memuat...</div>
      <div v-else class="overflow-x-auto">
        <table class="w-full">
          <thead><tr class="border-b border-slate-100 bg-slate-50">
            <th class="text-left text-xs font-medium text-slate-500 px-4 py-2.5">Produk</th>
            <th class="text-left text-xs font-medium text-slate-500 px-4 py-2.5">Tipe</th>
            <th class="text-center text-xs font-medium text-slate-500 px-4 py-2.5">Qty</th>
            <th class="text-left text-xs font-medium text-slate-500 px-4 py-2.5">Sumber</th>
            <th class="text-left text-xs font-medium text-slate-500 px-4 py-2.5">Tanggal</th>
          </tr></thead>
          <tbody>
            <tr v-for="r in list" :key="r.id" class="border-b border-slate-50 hover:bg-slate-50">
              <td class="px-4 py-2.5 text-sm text-slate-800">{{ r.produk?.nama_produk || '-' }}</td>
              <td class="px-4 py-2.5">
                <span class="text-[11px] px-2 py-0.5 rounded-full" :class="r.tipe === 'masuk' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600'">{{ r.tipe }}</span>
              </td>
              <td class="px-4 py-2.5 text-sm text-center font-medium">{{ r.qty }}</td>
              <td class="px-4 py-2.5 text-xs text-slate-600">{{ r.sumber }}</td>
              <td class="px-4 py-2.5 text-xs text-slate-500">{{ formatDate(r.created_at) }}</td>
            </tr>
            <tr v-if="list.length === 0"><td colspan="5" class="px-4 py-8 text-center text-sm text-slate-400">Belum ada riwayat</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import type { RiwayatStok } from '@/types'
import { laporanService } from '@/services'

const loading = ref(true)
const list = ref<RiwayatStok[]>([])

onMounted(async () => {
  try { const res = await laporanService.getRiwayatStok(); list.value = res.data as RiwayatStok[] } catch {}
  finally { loading.value = false }
})

function formatDate(d: string) { return new Date(d).toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) }
</script>
