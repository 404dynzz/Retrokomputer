<template>
  <div class="flex h-screen bg-slate-50">
    <!-- Sidebar -->
    <aside
      :class="[
        'fixed inset-y-0 left-0 z-50 flex flex-col bg-white border-r border-slate-200 transition-all duration-200 lg:relative',
        sidebarOpen ? 'w-56' : 'w-0 lg:w-16',
      ]"
    >
      <!-- Logo -->
      <div class="flex items-center px-3 border-b border-slate-200 shrink-0 bg-retro-dark transition-all duration-150"
           :style="{ height: (parseInt(logoHeight) + 24) + 'px' }">
        <div v-if="sidebarOpen" class="flex items-center gap-2 overflow-hidden w-full">
          <!-- Logo Type: Image -->
          <div v-if="logoType === 'image' && logoUrl" class="w-full flex items-center justify-center py-1 rounded border border-retro-orange/30 animate-fadeIn"
               :style="{ height: (parseInt(logoHeight) + 8) + 'px' }">
            <img :src="logoUrl" class="object-contain transition-all duration-150" :style="{ height: logoHeight + 'px' }" alt="Retro Logo" />
          </div>
          <!-- Logo Type: Text -->
          <div v-else class="flex items-center gap-2 w-full animate-fadeIn">
            <div class="w-7 h-7 rounded bg-retro-blue flex items-center justify-center shrink-0">
              <span class="text-white font-bold text-xs font-mono">R</span>
            </div>
            <span class="text-xs font-mono font-bold text-retro-orange truncate" :title="logoText"
                  :style="{ fontSize: (parseInt(logoHeight) * 0.4) + 'px' }">&gt;_ {{ logoText }}</span>
          </div>
        </div>
        <div v-else class="hidden lg:flex w-full justify-center">
          <div class="w-7 h-7 rounded bg-retro-blue flex items-center justify-center">
            <span class="text-white font-bold text-xs font-mono">R</span>
          </div>
        </div>
      </div>

      <!-- Nav -->
      <nav class="flex-1 overflow-y-auto py-3 px-2 space-y-0.5" v-if="sidebarOpen || !isMobile">
        <template v-for="group in menuGroups" :key="group.label">
          <p v-if="sidebarOpen" class="px-3 pt-4 pb-1 text-[10px] font-semibold uppercase tracking-wider text-slate-400">
            {{ group.label }}
          </p>
          <router-link
            v-for="item in group.items"
            :key="item.path"
            :to="item.path"
            :class="[
              'flex items-center gap-2.5 px-3 py-2 rounded text-[13px] transition-colors',
              isActive(item.path)
                ? 'bg-retro-blue/10 text-retro-blue font-bold border-l-2 border-retro-blue'
                : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900',
            ]"
            @click="isMobile && (sidebarOpen = false)"
          >
            <span class="text-base shrink-0 w-5 text-center">{{ item.icon }}</span>
            <span v-if="sidebarOpen" class="truncate">{{ item.label }}</span>
          </router-link>
        </template>
      </nav>

      <!-- User Info -->
      <div v-if="sidebarOpen" class="p-2 border-t border-slate-200 shrink-0">
        <div class="flex items-center gap-2 p-2 rounded-md">
          <div class="w-7 h-7 rounded-full bg-slate-200 flex items-center justify-center shrink-0">
            <span class="text-slate-600 text-xs font-semibold">{{ userInitial }}</span>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-xs font-medium text-slate-800 truncate">{{ authStore.userName }}</p>
            <p class="text-[10px] text-slate-400 uppercase font-semibold font-mono">{{ authStore.user?.role }}</p>
          </div>
          <button
            @click="authStore.logout()"
            class="p-1 rounded hover:bg-red-50 text-slate-400 hover:text-red-500 transition-colors"
            title="Logout"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 3a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h5a1 1 0 1 0 0-2H4V5h4a1 1 0 1 0 0-2H3zm12.293 3.293a1 1 0 0 1 1.414 0l3 3a1 1 0 0 1 0 1.414l-3 3a1 1 0 0 1-1.414-1.414L16.586 11H8a1 1 0 1 1 0-2h8.586l-1.293-1.293a1 1 0 0 1 0-1.414z" clip-rule="evenodd"/></svg>
          </button>
        </div>
      </div>
    </aside>

    <!-- Mobile overlay -->
    <div
      v-if="sidebarOpen && isMobile"
      class="fixed inset-0 bg-black/20 z-40 lg:hidden"
      @click="sidebarOpen = false"
    />

    <!-- Main -->
    <div class="flex-1 flex flex-col overflow-hidden">
      <!-- Header -->
      <header class="h-14 flex items-center justify-between px-4 lg:px-6 bg-white border-b border-slate-200 shrink-0">
        <div class="flex items-center gap-3">
          <button
            @click="sidebarOpen = !sidebarOpen"
            class="p-1.5 rounded-md hover:bg-slate-100 text-slate-500 transition-colors"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 5a1 1 0 0 1 1-1h12a1 1 0 1 1 0 2H4a1 1 0 0 1-1-1zm0 5a1 1 0 0 1 1-1h12a1 1 0 1 1 0 2H4a1 1 0 0 1-1-1zm0 5a1 1 0 0 1 1-1h6a1 1 0 1 1 0 2H4a1 1 0 0 1-1-1z" clip-rule="evenodd"/></svg>
          </button>
          <h2 class="text-sm font-semibold text-slate-800">{{ pageTitle }}</h2>
        </div>
        <router-link
          v-if="authStore.isAdmin || authStore.isKasir"
          to="/pos"
          class="text-xs font-bold px-3 py-1.5 bg-retro-blue text-white rounded hover:bg-blue-700 transition-colors hidden sm:inline-flex"
        >
          Kasir POS
        </router-link>
      </header>

      <!-- Content -->
      <main class="flex-1 overflow-y-auto p-4 lg:p-6">
        <router-view v-slot="{ Component }">
          <transition name="page" mode="out-in">
            <component :is="Component" />
          </transition>
        </router-view>
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { settingService } from '@/services'

const route = useRoute()
const authStore = useAuthStore()

const sidebarOpen = ref(true)
const isMobile = ref(false)

const logoType = ref('text')
const logoText = ref('Retro Komputer')
const logoUrl = ref('')
const logoHeight = ref('32')

const userInitial = computed(() => {
  const name = authStore.userName
  return name ? name.charAt(0).toUpperCase() : '?'
})

const pageTitle = computed(() => {
  const titles: Record<string, string> = {
    'dashboard': 'Dashboard',
    'dashboard-owner': 'Dashboard Owner',
    'produk': 'Produk',
    'produk-tambah': 'Tambah Produk',
    'produk-edit': 'Edit Produk',
    'pos': 'Kasir POS',
    'transaksi': 'Transaksi',
    'transaksi-detail': 'Detail Transaksi',
    'pembelian': 'Pembelian',
    'pembelian-tambah': 'Tambah Pembelian',
    'retur': 'Retur',
    'retur-tambah': 'Tambah Retur',
    'stok-riwayat': 'Riwayat Stok',
    'laporan-penjualan': 'Laporan Penjualan',
    'laporan-stok': 'Laporan Stok',
    'laporan-laba-rugi': 'Laba Rugi',
    'barang-rusak': 'Barang Rusak / Hilang',
    'settings': 'Pengaturan Sistem',
  }
  return titles[route.name as string] || 'Retro Komputer'
})

interface MenuItem { label: string; icon: string; path: string }
interface MenuGroup { label: string; items: MenuItem[] }

const menuGroups = computed<MenuGroup[]>(() => {
  if (authStore.isOwner) {
    return [
      { label: 'Overview', items: [
        { label: 'Dashboard', icon: '◉', path: '/dashboard/owner' },
      ]},
      { label: 'Monitoring', items: [
        { label: 'Transaksi', icon: '↗', path: '/transaksi' },
        { label: 'Barang Rusak', icon: '☒', path: '/barang-rusak' },
        { label: 'Riwayat Stok', icon: '⇅', path: '/stok/riwayat' },
      ]},
      { label: 'Laporan', items: [
        { label: 'Penjualan', icon: '▤', path: '/laporan/penjualan' },
        { label: 'Stok', icon: '▥', path: '/laporan/stok' },
        { label: 'Laba Rugi', icon: '◎', path: '/laporan/laba-rugi' },
      ]},
    ]
  }

  // Kasir: only show their relevant menu items
  if (authStore.isKasir) {
    return [
      { label: 'Menu', items: [
        { label: 'Dashboard', icon: '◉', path: '/dashboard' },
        { label: 'Kasir POS', icon: '⊞', path: '/pos' },
      ]},
      { label: 'Laporan', items: [
        { label: 'Transaksi', icon: '↗', path: '/transaksi' },
        { label: 'Penjualan', icon: '▤', path: '/laporan/penjualan' },
      ]},
    ]
  }

  const items = [
    { label: 'Menu', items: [
      { label: 'Dashboard', icon: '◉', path: '/dashboard' },
      { label: 'Kasir POS', icon: '⊞', path: '/pos' },
    ]},
    { label: 'Inventaris', items: [
      { label: 'Produk', icon: '□', path: '/produk' },
      { label: 'Pembelian', icon: '↙', path: '/pembelian' },
      { label: 'Retur', icon: '↩', path: '/retur' },
      { label: 'Barang Rusak', icon: '☒', path: '/barang-rusak' },
    ]},
    { label: 'Stok & Laporan', items: [
      { label: 'Riwayat Stok', icon: '⇅', path: '/stok/riwayat' },
      { label: 'Transaksi', icon: '↗', path: '/transaksi' },
      { label: 'Penjualan', icon: '▤', path: '/laporan/penjualan' },
      { label: 'Stok', icon: '▥', path: '/laporan/stok' },
    ]},
  ]

  // Add system administration menu for Admin
  if (authStore.isAdmin) {
    items.push({
      label: 'Sistem',
      items: [
        { label: 'Pengaturan', icon: '⚙', path: '/settings' }
      ]
    })
  }

  return items
})

function isActive(path: string) {
  return route.path === path || route.path.startsWith(path + '/')
}

function checkMobile() {
  isMobile.value = window.innerWidth < 1024
  if (isMobile.value) sidebarOpen.value = false
}

async function loadLogo() {
  try {
    const res = await settingService.getActive()
    logoType.value = res.data.logo_type
    logoText.value = res.data.logo_text
    logoUrl.value = res.data.logo_url
    logoHeight.value = res.data.logo_height || '32'
  } catch (err) {
    console.error('Gagal mengambil logo:', err)
  }
}

onMounted(() => {
  checkMobile()
  window.addEventListener('resize', checkMobile)
  loadLogo()
  
  // Listen for the custom event dispatched when Admin updates the settings in SettingsPage
  window.addEventListener('settings-updated', ((e: CustomEvent) => {
    logoType.value = e.detail.logo_type
    logoText.value = e.detail.logo_text
    logoUrl.value = e.detail.logo_url
    logoHeight.value = e.detail.logo_height || '32'
  }) as EventListener)
})

onUnmounted(() => {
  window.removeEventListener('resize', checkMobile)
})
</script>

<style scoped>
.page-enter-active, .page-leave-active {
  transition: opacity 0.15s ease;
}
.page-enter-from, .page-leave-to {
  opacity: 0;
}
</style>
