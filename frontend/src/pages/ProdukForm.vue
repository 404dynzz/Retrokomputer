<template>
  <div class="max-w-xl mx-auto">
    <div class="bg-white rounded-lg border border-slate-200 p-5">
      <h2 class="text-sm font-semibold text-slate-800 mb-4">{{ isEdit ? 'Edit Produk' : 'Tambah Produk' }}</h2>

      <div v-if="loadingInit" class="py-8 text-center text-sm text-slate-400">Memuat...</div>

      <form v-else @submit.prevent="handleSubmit" class="space-y-3">
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Kode Produk</label>
            <input v-model="form.kode_produk" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required />
          </div>
          <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Kategori</label>
            <input v-model="form.kategori" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required />
          </div>
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">Nama Produk</label>
          <input v-model="form.nama_produk" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required />
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Harga Beli</label>
            <input v-model.number="form.harga_beli" type="number" min="0" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required />
          </div>
          <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Harga Jual</label>
            <input v-model.number="form.harga_jual" type="number" min="0" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required />
          </div>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Stok {{ isEdit ? '(read-only)' : '' }}</label>
            <input v-model.number="form.stok" type="number" min="0" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" :disabled="isEdit" required />
          </div>
          <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Stok Minimum</label>
            <input v-model.number="form.stok_minimum" type="number" min="0" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required />
          </div>
        </div>

        <div v-if="error" class="p-2.5 rounded-md bg-red-50 border border-red-200 text-red-600 text-xs">{{ error }}</div>

        <div class="flex justify-end gap-2 pt-2">
          <router-link to="/produk" class="text-xs px-3 py-1.5 rounded-md border border-slate-200 text-slate-600 hover:bg-slate-50">Batal</router-link>
          <button type="submit" :disabled="saving" class="text-xs px-4 py-1.5 rounded-md bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50">
            {{ saving ? 'Menyimpan...' : 'Simpan' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import type { ProdukPayload } from '@/types'
import { produkService } from '@/services'

const route = useRoute()
const router = useRouter()

const isEdit = computed(() => !!route.params.id)
const loadingInit = ref(false)
const saving = ref(false)
const error = ref('')

const form = ref<ProdukPayload>({
  kode_produk: '', nama_produk: '', kategori: '',
  harga_beli: 0, harga_jual: 0, stok: 0, stok_minimum: 0,
})

onMounted(async () => {
  if (isEdit.value) {
    loadingInit.value = true
    try {
      const res = await produkService.getById(Number(route.params.id))
      const p = res.data
      form.value = { kode_produk: p.kode_produk, nama_produk: p.nama_produk, kategori: p.kategori, harga_beli: p.harga_beli, harga_jual: p.harga_jual, stok: p.stok, stok_minimum: p.stok_minimum }
    } catch { error.value = 'Gagal memuat data produk' }
    finally { loadingInit.value = false }
  }
})

async function handleSubmit() {
  saving.value = true
  error.value = ''
  try {
    if (isEdit.value) {
      await produkService.update(Number(route.params.id), form.value)
    } else {
      await produkService.create(form.value)
    }
    router.push('/produk')
  } catch (e: any) {
    error.value = e.response?.data?.message || 'Gagal menyimpan produk'
  } finally { saving.value = false }
}
</script>
