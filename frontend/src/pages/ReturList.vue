<template>
  <div class="space-y-4">
    <div class="flex items-center justify-between">
      <h2 class="text-sm font-semibold text-slate-800">Retur</h2>
      <router-link to="/retur/tambah" class="text-xs font-medium px-3 py-1.5 bg-blue-600 text-white rounded-md hover:bg-blue-700">+ Tambah</router-link>
    </div>
    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
      <div v-if="loading" class="p-8 text-center text-sm text-slate-400">Memuat...</div>
      <div v-else class="overflow-x-auto">
        <table class="w-full">
          <thead><tr class="border-b border-slate-100 bg-slate-50">
            <th class="text-left text-xs font-medium text-slate-500 px-4 py-2.5">Jenis</th>
            <th class="text-left text-xs font-medium text-slate-500 px-4 py-2.5">Alasan</th>
            <th class="text-left text-xs font-medium text-slate-500 px-4 py-2.5">Tanggal</th>
          </tr></thead>
          <tbody>
            <tr v-for="r in list" :key="r.id" class="border-b border-slate-50 hover:bg-slate-50">
              <td class="px-4 py-2.5"><span class="text-[11px] px-2 py-0.5 rounded-full" :class="r.jenis_retur === 'penjualan' ? 'bg-amber-50 text-amber-700' : 'bg-blue-50 text-blue-700'">{{ r.jenis_retur }}</span></td>
              <td class="px-4 py-2.5 text-sm text-slate-700">{{ r.alasan }}</td>
              <td class="px-4 py-2.5 text-xs text-slate-500">{{ formatDate(r.created_at) }}</td>
            </tr>
            <tr v-if="list.length === 0"><td colspan="3" class="px-4 py-8 text-center text-sm text-slate-400">Belum ada retur</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import type { Retur } from '@/types'
import { returService } from '@/services'

const loading = ref(true)
const list = ref<Retur[]>([])

onMounted(async () => {
  try { const res = await returService.getAll(); list.value = res.data as Retur[] } catch {}
  finally { loading.value = false }
})

function formatDate(d: string) { return new Date(d).toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) }
</script>
