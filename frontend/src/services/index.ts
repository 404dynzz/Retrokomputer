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
} from '@/types'

export const authService = {
  login: (payload: LoginPayload) =>
    api.post<LoginResponse>('/login', payload),
  logout: () =>
    api.post('/logout'),
  me: () =>
    api.get('/me'),
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
}

export const pembelianService = {
  getAll: () =>
    api.get<Pembelian[]>('/pembelian'),
  getById: (id: number) =>
    api.get<Pembelian>(`/pembelian/${id}`),
  create: (payload: PembelianPayload) =>
    api.post<Pembelian>('/pembelian', payload),
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
  create: (payload: BarangRusakPayload) =>
    api.post<{ message: string; data: BarangRusak }>('/barang-rusak', payload),
}

