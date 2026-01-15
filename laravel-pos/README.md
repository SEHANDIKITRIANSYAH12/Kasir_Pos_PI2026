# Kasir POS (Bagus Putra / PD Bagus Putra)

Aplikasi **Point of Sale (POS)** berbasis **Laravel 10** untuk usaha material/toko bangunan.

## Fitur Utama

- **Autentikasi multi-role**: `admin`, `kasir`
- **Manajemen Produk** (CRUD + stok + gambar)
- **Transaksi POS** (keranjang, pembayaran, simpan transaksi)
- **Riwayat Transaksi** (admin melihat semua, kasir hanya miliknya)
- **Laporan Penjualan** (admin)

## Teknologi

- PHP 8.1+
- Laravel 10
- MySQL/MariaDB
- Bootstrap 5 + Blade
- Vite + (Tailwind/Alpine tersedia di project)

---

# Langkah 2: Membuat Proyek Kasir POS dari Awal Sampai Jadi

Bagian ini menjelaskan langkah implementasi dari **setup environment** sampai aplikasi bisa dijalankan.

## 2.1 Prasyarat

- **PHP** 8.1 atau lebih baru
- **Composer**
- **Node.js** (disarankan versi LTS) + **npm**
- **MySQL/MariaDB**

Opsional:

- Git
- Laragon/XAMPP/MAMP (jika butuh stack lokal cepat)

## 2.2 Clone / Siapkan Source Code

Jika source code berasal dari repository:

```bash
git clone <url-repo>
```

Masuk ke folder project Laravel ini:

```bash
cd laravel-pos
```

## 2.3 Install Dependensi

Install dependensi backend (PHP):

```bash
composer install
```

Install dependensi frontend (Vite):

```bash
npm install
```

## 2.4 Konfigurasi Environment (.env)

Di project ini file `.env` sudah tersedia. Pastikan konfigurasi database sesuai komputer kamu.

Edit `.env` dan sesuaikan bagian berikut:

```env
APP_NAME="Kasir POS"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kasir_pos
DB_USERNAME=root
DB_PASSWORD=
```

Catatan:

- **Buat database** `kasir_pos` (atau nama lain, sesuaikan `DB_DATABASE`).
- Jika kamu ingin mengubah port/host, sesuaikan dengan setting MySQL kamu.

Generate key aplikasi (jika belum ada):

```bash
php artisan key:generate
```

## 2.5 Migrasi Database

Jalankan migrasi:

```bash
php artisan migrate
```

Jika kamu ingin mengulang dari nol (menghapus semua tabel lalu buat ulang):

```bash
php artisan migrate:fresh
```

## 2.6 Seeder (Data Awal)

Untuk membuat akun default (admin & kasir) dan data awal, jalankan:

```bash
php artisan db:seed
```

Atau khusus user saja:

```bash
php artisan db:seed --class=UserSeeder
```

Project ini menggunakan `updateOrCreate()` di `UserSeeder`, jadi aman dijalankan berulang kali.

## 2.7 Menjalankan Aplikasi (Backend)

Jalankan server Laravel:

```bash
php artisan serve
```

Akses di browser:

- `http://127.0.0.1:8000`

## 2.8 Menjalankan Aset Frontend (Vite)

Saat development (hot reload):

```bash
npm run dev
```

Untuk build production:

```bash
npm run build
```

## 2.9 Akun Default untuk Login

Setelah seeding, kamu bisa login menggunakan:

- **Admin**
  - Email: `admin@pdbagusputra.com`
  - Password: `password`
- **Kasir 1**
  - Email: `kasir1@pdbagusputra.com`
  - Password: `password`
- **Kasir 2**
  - Email: `kasir2@example.com`
  - Password: `password`

Catatan:

- Role disimpan di kolom `users.role` (nilai: `admin` / `kasir`).

## 2.10 Struktur Route & Hak Akses

Ringkasan akses (lihat `routes/web.php`):

- **Wajib login**: semua route utama memakai middleware `auth`
- **Admin saja**:
  - `products` (manajemen produk)
  - `users` (manajemen pengguna)
  - `reports` (laporan)
- **Admin & Kasir**:
  - transaksi baru, riwayat transaksi, detail transaksi

## 2.11 Troubleshooting

Jika mengalami kendala:

- **Error “These credentials do not match our records”**
  - Pastikan sudah menjalankan seeder (`php artisan db:seed --class=UserSeeder`).
  - Pastikan `.env` mengarah ke database yang benar.

- **Halaman blank / asset tidak muncul**
  - Jalankan `npm install` lalu `npm run dev`.
  - Atau build: `npm run build`.

- **APP_KEY missing / enkripsi error**
  - Jalankan `php artisan key:generate`.

- **Perubahan `.env` tidak terbaca**
  - Jalankan:
    - `php artisan config:clear`
    - `php artisan cache:clear`

---

## Catatan Pengembangan

- Model inti: `User`, `Product`, `Transaction`, `TransactionItem`
- Middleware role: `app/Http/Middleware/CheckRole.php`

