<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-1 h-6 rounded-full bg-gradient-to-b from-indigo-400 to-teal-400"></div>
        <h2 class="text-sm font-semibold tracking-wide uppercase" style="color: #e2e8f0; letter-spacing: 0.08em;">
          Laporan Penjualan
        </h2>
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
              <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-wider" style="color: #64748b; background: rgba(0,0,0,0.15);">Tanggal</th>
              <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-wider" style="color: #64748b; background: rgba(0,0,0,0.15);">Metode</th>
              <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-wider text-right" style="color: #64748b; background: rgba(0,0,0,0.15);">Total</th>
            </tr>
          </thead>
          <tbody class="text-xs">
            <tr
              v-for="t in list"
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
              <td class="px-4 py-3">
                <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold uppercase" style="background: rgba(255,255,255,0.04); color: #94a3b8; border: 1px solid rgba(255,255,255,0.06);">
                  {{ t.metode_pembayaran }}
                </span>
              </td>
              <td class="px-4 py-3 font-bold text-right" style="color: #e2e8f0;">
                {{ formatCurrency(t.total) }}
              </td>
            </tr>
            <tr v-if="list.length === 0">
              <td colspan="4" class="px-4 py-8 text-center text-sm" style="color: #475569;">
                Belum ada data
              </td>
            </tr>
          </tbody>
          <tfoot v-if="list.length > 0">
            <tr style="background: rgba(0,0,0,0.25); border-top: 1px solid rgba(255,255,255,0.08);">
              <td colspan="3" class="px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: #cbd5e1;">Total</td>
              <td class="px-4 py-3 text-xs font-bold text-right font-mono" style="color: #5eead4;">{{ formatCurrency(grandTotal) }}</td>
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
