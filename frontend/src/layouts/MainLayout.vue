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
      <div class="flex items-center px-3 border-b border-slate-200 shrink-0 bg-retro-dark transition-all duration-150 justify-center"
           style="height: 56px;">
        <span v-if="sidebarOpen" class="text-xs font-mono font-bold text-white truncate" title="Sistem POS">SISTEM POS</span>
        <span v-else class="text-white font-bold text-xs font-mono">POS</span>
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
            <p class="text-[10px] text-slate-400 uppercase font-semibold font-mono">
              {{ authStore.user?.role }}
              <span v-if="authStore.activeKasirProfile" class="text-retro-blue font-bold font-sans lowercase text-[9px] block">
                kasir: {{ authStore.activeKasirProfile.nama }}
              </span>
            </p>
          </div>
          <button
            @click="triggerLogout"
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
          <h2 class="text-sm font-semibold text-slate-800 font-sans">{{ pageTitle }}</h2>
        </div>

        <div class="flex items-center gap-4">
          <!-- POS Button (Kasir only) -->
          <router-link
            v-if="authStore.isKasir"
            to="/pos"
            class="text-xs font-bold px-3 py-1.5 bg-retro-blue text-white rounded hover:bg-blue-700 transition-colors hidden sm:inline-flex items-center gap-1.5"
          >
            <span>⊞</span> Kasir POS
          </router-link>

          <!-- Profile Dropdown -->
          <div class="relative profile-dropdown-container">
            <button
              @click="toggleDropdown"
              class="flex items-center gap-2 p-1 rounded-md hover:bg-slate-50 border border-slate-100 transition-colors"
            >
              <div class="w-8 h-8 rounded bg-retro-blue/10 flex items-center justify-center border border-retro-blue/20">
                <span class="text-retro-blue font-bold text-xs font-mono">{{ userInitial }}</span>
              </div>
              <div class="hidden md:flex flex-col text-left shrink-0 max-w-[120px]">
                <span class="text-xs font-semibold text-slate-700 truncate leading-tight">{{ authStore.userName }}</span>
                <span class="text-[9px] font-bold font-mono text-slate-400 uppercase leading-none">
                  {{ authStore.user?.role }}
                  <span v-if="authStore.activeKasirProfile" class="text-retro-blue font-bold">
                    ({{ authStore.activeKasirProfile.nama }})
                  </span>
                </span>
              </div>
              <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
              </svg>
            </button>

            <!-- Dropdown Menu -->
            <transition name="dropdown">
              <div
                v-if="dropdownOpen"
                class="absolute right-0 mt-2 w-48 bg-white border border-slate-200 rounded-md shadow-lg py-1.5 z-50 animate-fadeIn font-sans"
              >
                <div class="px-3 py-2 border-b border-slate-100">
                  <p class="text-xs font-semibold text-slate-800 truncate">{{ authStore.userName }}</p>
                  <p class="text-[10px] text-slate-400 font-mono font-bold uppercase mt-0.5">{{ authStore.user?.role }}</p>
                </div>
                <div class="py-1">
                  <button
                    @click="triggerLogout"
                    class="w-full flex items-center gap-2 px-3 py-2 text-xs text-red-600 hover:bg-red-50 text-left transition-colors"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 00 1 1h5a1 1 0 100-2H4V5h4a1 1 0 100-2H3zm12.293 3.293a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 01-1.414-1.414L16.586 11H8a1 1 0 110-2h8.586l-1.293-1.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                    <span>Keluar Akun</span>
                  </button>
                </div>
              </div>
            </transition>
          </div>
        </div>
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

    <!-- Confirmation Modal -->
    <div v-if="showLogoutConfirm" class="fixed inset-0 z-[100] flex items-center justify-center bg-retro-dark/60 backdrop-blur-sm animate-fadeIn">
      <div class="bg-white border-2 border-retro-blue rounded-lg max-w-sm w-full mx-4 overflow-hidden shadow-2xl font-mono">
        <!-- Title bar -->
        <div class="bg-retro-blue text-white px-4 py-2 flex items-center justify-between">
          <span class="font-bold text-xs font-mono">KONFIRMASI KELUAR</span>
          <button @click="showLogoutConfirm = false" class="text-white hover:text-retro-yellow transition-colors font-bold text-lg leading-none">×</button>
        </div>
        <div class="p-6">
          <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded bg-amber-50 border border-amber-300 flex items-center justify-center shrink-0 text-amber-500 text-xl font-bold">!</div>
            <div>
              <h3 class="text-sm font-bold text-slate-800 mb-1">Keluar dari Aplikasi?</h3>
              <p class="text-xs text-slate-500 leading-relaxed font-sans">Apakah Anda yakin ingin keluar dari sistem Retrokomputer? Sesi Anda akan diakhiri.</p>
            </div>
          </div>
        </div>
        <div class="bg-slate-50 px-4 py-3 flex justify-end gap-2 border-t border-slate-100">
          <button @click="showLogoutConfirm = false" class="px-3 py-1.5 text-xs text-slate-600 hover:text-slate-800 bg-white border border-slate-300 rounded font-sans transition-colors">
            Batal
          </button>
          <button @click="handleLogout" class="px-3 py-1.5 text-xs bg-red-600 hover:bg-red-700 text-white rounded font-sans font-semibold transition-colors shadow-sm">
            Ya, Keluar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const route = useRoute()
const authStore = useAuthStore()

const sidebarOpen = ref(true)
const isMobile = ref(false)

const dropdownOpen = ref(false)
const showLogoutConfirm = ref(false)

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
    'supplier': 'Supplier',
    'retur': 'Retur',
    'retur-tambah': 'Tambah Retur',
    'stok-riwayat': 'Riwayat Stok',
    'laporan-penjualan': 'Laporan Penjualan',
    'laporan-stok': 'Laporan Stok',
    'laporan-laba-rugi': 'Laba Rugi',
    'barang-rusak': 'Barang Rusak / Hilang',
    'profil-kasir': 'Profil Kasir',
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
        { label: 'Profil Kasir', icon: '👤', path: '/profil-kasir' },
        { label: 'Kasir POS', icon: '⊞', path: '/pos' },
      ]},
      { label: 'Laporan', items: [
        { label: 'Transaksi', icon: '↗', path: '/transaksi' },
        { label: 'Penjualan', icon: '▤', path: '/laporan/penjualan' },
      ]},
    ]
  }

  // Admin items
  const items = [
    { label: 'Menu', items: [
      { label: 'Dashboard', icon: '◉', path: '/dashboard' },
    ]},
    { label: 'Inventaris', items: [
      { label: 'Produk', icon: '□', path: '/produk' },
      { label: 'Supplier', icon: '▤', path: '/supplier' },
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
    { label: 'Pengelolaan', items: [
      { label: 'Profil Kasir', icon: '👤', path: '/profil-kasir' },
    ]},
  ]

  // (Removed system administration menu)

  return items
})

function isActive(path: string) {
  return route.path === path || route.path.startsWith(path + '/')
}

function checkMobile() {
  isMobile.value = window.innerWidth < 1024
  if (isMobile.value) sidebarOpen.value = false
}

function toggleDropdown() {
  dropdownOpen.value = !dropdownOpen.value
}

function closeDropdown() {
  dropdownOpen.value = false
}

function triggerLogout() {
  closeDropdown()
  showLogoutConfirm.value = true
}

function handleLogout() {
  showLogoutConfirm.value = false
  authStore.logout()
}

function handleWindowClick(e: MouseEvent) {
  const target = e.target as HTMLElement
  if (dropdownOpen.value && !target.closest('.profile-dropdown-container')) {
    closeDropdown()
  }
}

onMounted(() => {
  checkMobile()
  window.addEventListener('resize', checkMobile)
  window.addEventListener('click', handleWindowClick)
})

onUnmounted(() => {
  window.removeEventListener('resize', checkMobile)
  window.removeEventListener('click', handleWindowClick)
})
</script>

<style scoped>
.page-enter-active, .page-leave-active {
  transition: opacity 0.15s ease;
}
.page-enter-from, .page-leave-to {
  opacity: 0;
}

.dropdown-enter-active, .dropdown-leave-active {
  transition: opacity 0.15s ease, transform 0.15s ease;
}
.dropdown-enter-from, .dropdown-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}
</style>

