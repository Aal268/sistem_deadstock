Import Template & Integration Notes
=================================

Ringkasan singkat: file ini menjelaskan format Excel/CSV yang dapat diimpor ke sistem untuk menambahkan laporan penjualan massal, endpoint yang tersedia, validasi, dan kebutuhan agar fitur berjalan.

# Template Format (header)
- `tanggal_waktu` : tanggal dan waktu transaksi, format contoh `YYYY-MM-DD HH:MM:SS` (contoh: `2026-05-21 10:30:00`)
- `sku`          : SKU produk yang ada di tabel `products` (harus cocok dengan kolom `sku` pada produk)
- `qty`          : jumlah barang (integer > 0)
- `catatan`      : (opsional) catatan untuk baris transaksi

Contoh (CSV / Excel sheet pertama):

tanggal_waktu,sku,qty,catatan
2026-05-21 10:30:00,ELK-001,2,Contoh laporan penjualan
2026-05-21 11:00:00,SKU-ABC-123,1,

Lokasi template yang dapat di-download dari aplikasi: route `GET /histori-sales/template-import` (nama route: `histori-sales.template-import`).

Files utama terkait:
- Controller import/export: [app/Http/Controllers/SaleController.php](app/Http/Controllers/SaleController.php)
- Import processor: [app/Imports/SalesReportImport.php](app/Imports/SalesReportImport.php)
- Export template helper: [app/Exports/SalesReportTemplateExport.php](app/Exports/SalesReportTemplateExport.php)
- View upload / UI: [resources/views/kasir/histori-sales/index.blade.php](resources/views/kasir/histori-sales/index.blade.php)
- Routes: [routes/web.php](routes/web.php)

Apa yang terjadi saat import berhasil:
- Setiap baris valid akan membuat satu record `StockMovement` ber-type `out` dan `status` = `success`.
- `price_at_transaction` diisi dengan `product->unit_price` saat ini.
- `movement_date` diisi dari kolom `tanggal_waktu` pada file.
- `user_id` di-set ke user yang melakukan upload (harus login).
- `note` menggunakan kolom `catatan` jika ada, kalau kosong akan berisi `Impor laporan penjualan`.
- Stok produk (`products.current_stock`) dikurangi sesuai `qty`.

Validasi yang diterapkan oleh import:
- File harus ada dan berekstensi `xlsx`, `xls`, atau `csv`.
- Setiap baris harus memiliki `tanggal_waktu` yang bisa diparse oleh Carbon.
- `sku` harus ditemukan di tabel `products` (pencocokan exact).
- `qty` harus integer dan >= 1.
- Jika ada baris yang invalid, proses di-rollback (semua baris batal) dan error dikembalikan.

Endpoint / Route (ringkas):
- `GET  /histori-sales/template-import` — download template Excel (administrator).
- `POST /histori-sales/import`          — upload file import (administrator) (form multipart, input name `file`).
- `GET  /histori-sales/export`          — export riwayat filter saat ini ke .xls.
- `GET  /histori-sales`                 — lihat riwayat penjualan (filterable).
- `GET  /sales`                         — halaman kasir / input penjualan (administrator role).
- `POST /sales`                         — proses penjualan (single/multi item from POS form).

Hak akses / kebutuhan:
- Pengguna harus login.
- Route import/export dan halaman kasir di-bungkus middleware `role:administrator` — hanya role `administrator` yang bisa mengakses fitur ini.
- Composer package yang diperlukan: `maatwebsite/excel` (telah terpasang).
- PHP >= 8.2 (sesuai composer.json) dan database (MySQL) harus tersedia.

Rekomendasi & catatan operasi:
- Untuk file besar (> beberapa ribu baris) pertimbangkan proses asinkron (queue) dan preview sebelum commit.
- Tambahkan logging untuk tiap import (siapa import, nama file, jumlah baris sukses/gagal) agar audit trail tersedia.
- Tambah unit/integration test untuk `SalesReportImport` untuk mencegah regresi.

Langkah cepat untuk testing manual:
1. Login sebagai user dengan role `administrator`.
2. Buka `Riwayat Penjualan` di UI dan klik `Download Template Import`.
3. Isi contoh baris sesuai header.
4. Upload lewat form `Upload Import Excel`.
5. Cek notifikasi sukses/error dan cek tabel `riwayat` (halaman histori) serta stok produk.

Jika mau, saya bisa tambahkan fitur preview sebelum simpan (tampilkan baris yang akan diimpor dan highlight error), atau log import ke tabel `imports`.

---
File ini dibuat otomatis untuk memudahkan review format import dan integrasi.
