import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/login',
      name: 'login',
      component: () => import('@/pages/LoginPage.vue'),
      meta: { guest: true },
    },
    {
      path: '/',
      component: () => import('@/layouts/MainLayout.vue'),
      meta: { requiresAuth: true },
      children: [
        { path: '', redirect: '/dashboard' },
        {
          path: 'dashboard',
          name: 'dashboard',
          component: () => import('@/pages/DashboardAdmin.vue'),
          meta: { roles: ['admin', 'kasir'] },
        },
        {
          path: 'dashboard/owner',
          name: 'dashboard-owner',
          component: () => import('@/pages/DashboardOwner.vue'),
          meta: { roles: ['owner'] },
        },
        {
          path: 'produk',
          name: 'produk',
          component: () => import('@/pages/ProdukList.vue'),
          meta: { roles: ['admin', 'kasir'] },
        },
        {
          path: 'produk/tambah',
          name: 'produk-tambah',
          component: () => import('@/pages/ProdukForm.vue'),
          meta: { roles: ['admin'] },
        },
        {
          path: 'produk/:id/edit',
          name: 'produk-edit',
          component: () => import('@/pages/ProdukForm.vue'),
          meta: { roles: ['admin'] },
        },
        {
          path: 'pos',
          name: 'pos',
          component: () => import('@/pages/POSPage.vue'),
          meta: { roles: ['admin', 'kasir'] },
        },
        {
          path: 'transaksi',
          name: 'transaksi',
          component: () => import('@/pages/TransaksiList.vue'),
          meta: { roles: ['admin', 'kasir', 'owner'] },
        },
        {
          path: 'transaksi/:id',
          name: 'transaksi-detail',
          component: () => import('@/pages/TransaksiDetail.vue'),
          meta: { roles: ['admin', 'kasir', 'owner'] },
        },
        {
          path: 'pembelian',
          name: 'pembelian',
          component: () => import('@/pages/PembelianList.vue'),
          meta: { roles: ['admin'] },
        },
        {
          path: 'pembelian/tambah',
          name: 'pembelian-tambah',
          component: () => import('@/pages/PembelianForm.vue'),
          meta: { roles: ['admin'] },
        },
        {
          path: 'retur',
          name: 'retur',
          component: () => import('@/pages/ReturList.vue'),
          meta: { roles: ['admin'] },
        },
        {
          path: 'retur/tambah',
          name: 'retur-tambah',
          component: () => import('@/pages/ReturForm.vue'),
          meta: { roles: ['admin'] },
        },
        {
          path: 'stok/riwayat',
          name: 'stok-riwayat',
          component: () => import('@/pages/StokRiwayat.vue'),
          meta: { roles: ['admin', 'owner'] },
        },
        {
          path: 'laporan/penjualan',
          name: 'laporan-penjualan',
          component: () => import('@/pages/LaporanPenjualan.vue'),
          meta: { roles: ['admin', 'owner'] },
        },
        {
          path: 'laporan/stok',
          name: 'laporan-stok',
          component: () => import('@/pages/LaporanStok.vue'),
          meta: { roles: ['admin', 'owner'] },
        },
        {
          path: 'laporan/laba-rugi',
          name: 'laporan-laba-rugi',
          component: () => import('@/pages/LaporanLabaRugi.vue'),
          meta: { roles: ['owner'] },
        },
        {
          path: 'barang-rusak',
          name: 'barang-rusak',
          component: () => import('@/pages/BarangRusak.vue'),
          meta: { roles: ['admin', 'owner'] },
        },
        {
          path: 'settings',
          name: 'settings',
          component: () => import('@/pages/SettingsPage.vue'),
          meta: { roles: ['admin'] },
        },
      ],
    },
    {
      path: '/:pathMatch(.*)*',
      redirect: '/login',
    },
  ],
})

router.beforeEach((to, _from, next) => {
  const authStore = useAuthStore()
  authStore.init()

  const isAuthenticated = authStore.isAuthenticated
  const userRole = authStore.user?.role

  if (to.meta.guest && isAuthenticated) {
    return next(userRole === 'owner' ? '/dashboard/owner' : '/dashboard')
  }

  if (to.meta.requiresAuth && !isAuthenticated) {
    return next('/login')
  }

  if (to.meta.roles && userRole) {
    const allowed = to.meta.roles as string[]
    if (!allowed.includes(userRole)) {
      return next(userRole === 'owner' ? '/dashboard/owner' : '/dashboard')
    }
  }

  next()
})

export default router
