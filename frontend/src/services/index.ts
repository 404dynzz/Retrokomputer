import api from './api'
import type {
  LoginPayload,
  LoginResponse,
  Produk,
  ProdukPayload,
  TransaksiPayload,
  Transaksi,
  Pembelian,
  PembelianPayload,
  Retur,
  ReturPayload,
  RiwayatStok,
  DashboardStats,
  StokStats,
  FilterParams,
  ActiveSettings,
  BarangRusak,
  BarangRusakPayload,
  Supplier,
  SupplierPayload,
  Notifikasi,
  ProfilKasir,
  ProfilKasirPayload,
} from '@/types'

export const authService = {
  login: (payload: LoginPayload) =>
    api.post<LoginResponse>('/login', payload),
  logout: () =>
    api.post('/logout'),
  me: () =>
    api.get('/me'),
}

export const supplierService = {
  getAll: () =>
    api.get<Supplier[]>('/suppliers'),
  create: (payload: SupplierPayload) =>
    api.post<Supplier>('/suppliers', payload),
  update: (id: number, payload: Partial<SupplierPayload>) =>
    api.put<Supplier>(`/suppliers/${id}`, payload),
  delete: (id: number) =>
    api.delete(`/suppliers/${id}`),
}

export const produkService = {
  getAll: (params?: FilterParams) =>
    api.get<Produk[]>('/produk', { params }),
  getById: (id: number) =>
    api.get<Produk>(`/produk/${id}`),
  create: (payload: ProdukPayload) =>
    api.post<Produk>('/produk', payload),
  update: (id: number, payload: Partial<ProdukPayload>) =>
    api.put<Produk>(`/produk/${id}`, payload),
  delete: (id: number) =>
    api.delete(`/produk/${id}`),
}

export const transaksiService = {
  getAll: () =>
    api.get<Transaksi[]>('/transaksi'),
  getById: (id: number) =>
    api.get<Transaksi>(`/transaksi/${id}`),
  create: (payload: TransaksiPayload) =>
    api.post<Transaksi>('/transaksi', payload),
  delete: (id: number) =>
    api.delete(`/transaksi/${id}`),
}

export const pembelianService = {
  getAll: () =>
    api.get<Pembelian[]>('/pembelian'),
  getById: (id: number) =>
    api.get<Pembelian>(`/pembelian/${id}`),
  create: (payload: PembelianPayload | FormData) =>
    api.post<Pembelian>('/pembelian', payload, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    }),
}

export const returService = {
  getAll: () =>
    api.get<Retur[]>('/retur'),
  create: (payload: ReturPayload) =>
    api.post<Retur>('/retur', payload),
}

export const laporanService = {
  getDashboard: () =>
    api.get<DashboardStats>('/laporan/dashboard'),
  getStok: () =>
    api.get<StokStats>('/laporan/stok'),
  getRiwayatStok: (params?: FilterParams) =>
    api.get<RiwayatStok[]>('/riwayat-stok', { params }),
}

export const settingService = {
  getActive: () =>
    api.get<ActiveSettings>('/settings/active'),
  update: (payload: FormData) =>
    api.post<ActiveSettings>('/settings', payload, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    }),
}

export const barangRusakService = {
  getAll: () =>
    api.get<BarangRusak[]>('/barang-rusak'),
  create: (payload: BarangRusakPayload | FormData) => {
    if (payload instanceof FormData) {
      return api.post<{ message: string; data: BarangRusak }>('/barang-rusak', payload, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      })
    }
    return api.post<{ message: string; data: BarangRusak }>('/barang-rusak', payload)
  },
}

export const notifikasiService = {
  getAll: () =>
    api.get<Notifikasi[]>('/notifikasi'),
  markRead: (id: number) =>
    api.put(`/notifikasi/${id}`),
  markAllRead: () =>
    api.put('/notifikasi/read-all'),
}

export const profilKasirService = {
  getAll: () =>
    api.get<ProfilKasir[]>('/profil-kasir'),
  create: (payload: ProfilKasirPayload) =>
    api.post<ProfilKasir>('/profil-kasir', payload),
  update: (id: number, payload: { nama: string; kode_khusus?: string }) =>
    api.put<ProfilKasir>(`/profil-kasir/${id}`, payload),
  delete: (id: number) =>
    api.delete(`/profil-kasir/${id}`),
  aktifkan: (id: number, kode_khusus: string) =>
    api.post<ProfilKasir>('/profil-kasir/aktifkan', { id, kode_khusus }),
  nonaktifkan: () =>
    api.post('/profil-kasir/nonaktifkan'),
  getAktif: () =>
    api.get<ProfilKasir | null>('/profil-kasir/aktif'),
  getKasirUsers: () =>
    api.get<{ id: number; name: string; username: string }[]>('/kasir-users'),
}

