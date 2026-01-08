# 🧾 POS Application (PHP MVC)

Aplikasi **Point of Sale (POS)** berbasis **PHP Native (MVC)** untuk kebutuhan kasir dan admin, dilengkapi dengan **manajemen shift**, **laporan transaksi**, **laporan produk**, dan **visualisasi grafik**.

---

## ✨ Fitur Utama

### 🔐 Autentikasi & Role
- Login & Logout
- Role: **Admin** dan **Kasir**
- Middleware Auth & Role

### 🛒 Transaksi
- Input transaksi
- Multi metode pembayaran (Cash, QRIS)
- Status transaksi (paid, void)

### 🕒 Shift Kasir
- Buka & tutup shift
- Rekap otomatis saat shift ditutup
- Laporan shift per periode
- Detail pembayaran & produk terjual
- Print laporan shift

### 📊 Laporan
#### 📄 Laporan Transaksi
- Filter tanggal
- Print laporan

#### 📦 Laporan Produk Terjual
- Qty terjual & total penjualan
- Filter periode
- **Top 10 produk terlaris**
- **Grafik (Chart.js)**
- Print laporan produk

---

## 🏗️ Arsitektur

Menggunakan **MVC Pattern**:

