<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-1 h-6 rounded-full bg-gradient-to-b from-indigo-400 to-teal-400"></div>
        <h2 class="text-sm font-semibold tracking-wide uppercase" style="color: #e2e8f0; letter-spacing: 0.08em;">
          Laporan Stok
        </h2>
      </div>
    </div>

    <!-- Stats Cards Row -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <!-- Card 1: Jenis Produk -->
      <div class="kpi-card group" style="background: linear-gradient(135deg, #131b2e 0%, #0f1623 100%); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 12px; padding: 1.25rem;">
        <p class="text-[11px] font-medium uppercase tracking-wider" style="color: #64748b;">Jenis Produk</p>
        <p class="text-xl font-bold tracking-tight mt-1.5" style="color: #cbd5e1;">{{ stokStats.total_jenis_produk }}</p>
      </div>
      <!-- Card 2: Stok Aman -->
      <div class="kpi-card group" style="background: linear-gradient(135deg, #131b2e 0%, #0f1623 100%); border: 1px solid rgba(52, 211, 153, 0.12); border-radius: 12px; padding: 1.25rem;">
        <p class="text-[11px] font-medium uppercase tracking-wider" style="color: #64748b;">Stok Aman</p>
        <p class="text-xl font-bold tracking-tight mt-1.5" style="color: #34d399;">{{ stokStats.stok_aman }}</p>
      </div>
      <!-- Card 3: Stok Rendah -->
      <div class="kpi-card group" style="background: linear-gradient(135deg, #131b2e 0%, #0f1623 100%); border: 1px solid rgba(245, 158, 11, 0.12); border-radius: 12px; padding: 1.25rem;">
        <p class="text-[11px] font-medium uppercase tracking-wider" style="color: #64748b;">Stok Rendah</p>
        <p class="text-xl font-bold tracking-tight mt-1.5" style="color: #fbbf24;">{{ stokStats.stok_rendah }}</p>
      </div>
      <!-- Card 4: Stok Habis -->
      <div class="kpi-card group" style="background: linear-gradient(135deg, #131b2e 0%, #0f1623 100%); border: 1px solid rgba(239, 68, 68, 0.12); border-radius: 12px; padding: 1.25rem;">
        <p class="text-[11px] font-medium uppercase tracking-wider" style="color: #64748b;">Stok Habis</p>
        <p class="text-xl font-bold tracking-tight mt-1.5" style="color: #f87171;">{{ stokStats.stok_habis }}</p>
      </div>
    </div>

    <!-- Table Container -->
    <div style="background: linear-gradient(135deg, #131b2e 0%, #0f1623 100%); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; overflow: hidden;">
      <div v-if="loading" class="p-8 text-center text-sm" style="color: #475569;">
        Memuat data laporan...
      </div>
      <div v-else class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr style="border-bottom: 1px solid rgba(255,255,255,0.06);">
              <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-wider" style="color: #64748b; background: rgba(0,0,0,0.15);">Kode</th>
              <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-wider" style="color: #64748b; background: rgba(0,0,0,0.15);">Produk</th>
              <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-wider" style="color: #64748b; background: rgba(0,0,0,0.15);">Kategori</th>
              <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-wider text-center" style="color: #64748b; background: rgba(0,0,0,0.15);">Stok</th>
              <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-wider text-center" style="color: #64748b; background: rgba(0,0,0,0.15);">Minimum</th>
              <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-wider" style="color: #64748b; background: rgba(0,0,0,0.15);">Status</th>
            </tr>
          </thead>
          <tbody class="text-xs">
            <tr
              v-for="p in produkList"
              :key="p.id"
              class="transition-colors duration-200"
              style="border-bottom: 1px solid rgba(255,255,255,0.03);"
              @mouseenter="($event.currentTarget as HTMLElement).style.background = 'rgba(99, 102, 241, 0.04)'"
              @mouseleave="($event.currentTarget as HTMLElement).style.background = 'transparent'"
            >
              <td class="px-4 py-3 font-mono font-bold" style="color: #818cf8;">
                {{ p.kode_produk }}
              </td>
              <td class="px-4 py-3 font-semibold font-sans" style="color: #cbd5e1;">
                {{ p.nama_produk }}
              </td>
              <td class="px-4 py-3 font-sans" style="color: #64748b;">
                {{ p.kategori }}
              </td>
              <td class="px-4 py-3 text-center font-bold font-mono" :style="{ color: p.stok <= 0 ? '#f87171' : p.stok <= p.stok_minimum ? '#fbbf24' : '#34d399' }">
                {{ p.stok }}
              </td>
              <td class="px-4 py-3 text-center font-mono" style="color: #64748b;">
                {{ p.stok_minimum }}
              </td>
              <td class="px-4 py-3">
                <span
                  class="px-2 py-0.5 rounded-md text-[10px] font-semibold uppercase border"
                  :style="{
                    background: p.stok <= 0 ? 'rgba(239,68,68,0.08)' : p.stok <= p.stok_minimum ? 'rgba(245,158,11,0.08)' : 'rgba(16,185,129,0.08)',
                    color: p.stok <= 0 ? '#fb7185' : p.stok <= p.stok_minimum ? '#fbbf24' : '#34d399',
                    borderColor: p.stok <= 0 ? 'rgba(239,68,68,0.15)' : p.stok <= p.stok_minimum ? 'rgba(245,158,11,0.15)' : 'rgba(16,185,129,0.15)'
                  }"
                >
                  {{ p.stok <= 0 ? 'Habis' : p.stok <= p.stok_minimum ? 'Rendah' : 'Aman' }}
                </span>
              </td>
            </tr>
            <tr v-if="produkList.length === 0">
              <td colspan="6" class="px-4 py-8 text-center text-sm" style="color: #475569;">
                Belum ada data
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import type { Produk, StokStats } from '@/types'
import { produkService, laporanService } from '@/services'

const loading = ref(true)
const produkList = ref<Produk[]>([])
const stokStats = ref<StokStats>({ total_jenis_produk: 0, stok_aman: 0, stok_rendah: 0, stok_habis: 0 })

onMounted(async () => {
  try {
    const [pRes, sRes] = await Promise.all([produkService.getAll(), laporanService.getStok()])
    produkList.value = pRes.data as Produk[]
    stokStats.value = sRes.data
  } catch {}
  finally { loading.value = false }
})
</script>
