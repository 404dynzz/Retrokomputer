<template>
  <div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg border border-slate-200 p-5">
      <h2 class="text-sm font-semibold text-slate-800 mb-4">Tambah Pembelian</h2>
      <form @submit.prevent="handleSubmit" class="space-y-4">
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Supplier</label>
            <input v-model="form.supplier" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required />
          </div>
          <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">No. Invoice</label>
            <input v-model="form.invoice" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required />
          </div>
        </div>

        <div>
          <div class="flex items-center justify-between mb-2">
            <label class="text-xs font-medium text-slate-600">Item Pembelian</label>
            <button type="button" @click="addRow" class="text-xs text-blue-600 hover:underline">+ Tambah Item</button>
          </div>
          <div v-for="(item, i) in form.items" :key="i" class="flex gap-2 mb-2 items-end">
            <div class="flex-1">
              <select v-model="item.produk_id" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                <option value="">Pilih Produk</option>
                <option v-for="p in produkList" :key="p.id" :value="p.id">{{ p.nama_produk }}</option>
              </select>
            </div>
            <div class="w-20">
              <input v-model.number="item.qty" type="number" min="1" placeholder="Qty" class="w-full px-2 py-2 text-sm border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required />
            </div>
            <div class="w-28">
              <input v-model.number="item.harga_beli" type="number" min="0" placeholder="Harga" class="w-full px-2 py-2 text-sm border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required />
            </div>
            <button v-if="form.items.length > 1" type="button" @click="form.items.splice(i, 1)" class="text-red-400 hover:text-red-600 text-sm pb-2">✕</button>
          </div>
        </div>

        <div v-if="error" class="p-2.5 rounded-md bg-red-50 border border-red-200 text-red-600 text-xs">{{ error }}</div>

        <div class="flex justify-end gap-2 pt-2">
          <router-link to="/pembelian" class="text-xs px-3 py-1.5 rounded-md border border-slate-200 text-slate-600 hover:bg-slate-50">Batal</router-link>
          <button type="submit" :disabled="saving" class="text-xs px-4 py-1.5 rounded-md bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50">
            {{ saving ? 'Menyimpan...' : 'Simpan' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import type { Produk, PembelianPayload } from '@/types'
import { produkService, pembelianService } from '@/services'

const router = useRouter()
const saving = ref(false)
const error = ref('')
const produkList = ref<Produk[]>([])

const form = ref<PembelianPayload>({
  supplier: '', invoice: '',
  items: [{ produk_id: 0, qty: 1, harga_beli: 0 }]
})

function addRow() { form.value.items.push({ produk_id: 0, qty: 1, harga_beli: 0 }) }

onMounted(async () => {
  try { const res = await produkService.getAll(); produkList.value = res.data as Produk[] } catch {}
})

async function handleSubmit() {
  saving.value = true; error.value = ''
  try {
    await pembelianService.create(form.value)
    router.push('/pembelian')
  } catch (e: any) {
    error.value = e.response?.data?.message || 'Gagal menyimpan'
  } finally { saving.value = false }
}
</script>
