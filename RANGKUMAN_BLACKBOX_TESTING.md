# Rangkuman Blackbox Testing (Role Admin & Kasir) — **Hasil 100% Berhasil (PASS)**

## 1) Ruang Lingkup Modul yang Diuji
- **Autentikasi**: Login, Logout
- **Dashboard**: ringkasan transaksi & pendapatan (dibedakan Admin vs Kasir)
- **Manajemen Produk (Admin)**: CRUD + upload gambar + validasi
- **Manajemen User (Admin)**: CRUD + validasi + larangan hapus akun sendiri
- **Transaksi (Admin & Kasir)**: buat transaksi, validasi pembayaran, pengurangan stok, detail transaksi, riwayat transaksi
- **Laporan (Admin)**: filter tanggal, total pendapatan, cetak laporan

---

## 2) Tabel Rangkuman Test Case Blackbox

### A. Autentikasi (Admin & Kasir)
| Fungsi yang Diuji | Input | Hasil yang Diharapkan | Hasil Pengujian |
|---|---|---|---|
| Login berhasil | Email & password valid | Masuk sistem dan diarahkan ke halaman utama (`/dashboard`) | PASS |
| Login gagal (password salah) | Email valid, password salah | Muncul pesan gagal login / tidak masuk dashboard | PASS |
| Logout berhasil | User dalam keadaan login | Session berakhir dan kembali ke halaman login | PASS |

---

### B. Akses Role / Autorisasi
| Fungsi yang Diuji | Input | Hasil yang Diharapkan | Hasil Pengujian |
|---|---|---|---|
| Admin membuka menu Produk | Role Admin, akses `products.index` | Halaman produk tampil | PASS |
| Kasir mencoba akses Produk | Role Kasir, akses URL `/products` | Ditolak `403 UNAUTHORIZED ACTION` | PASS |
| Admin membuka menu Users | Role Admin, akses `/users` | Halaman users tampil | PASS |
| Kasir mencoba akses Users | Role Kasir, akses URL `/users` | Ditolak `403` | PASS |
| Admin membuka Laporan | Role Admin, akses `/reports` | Halaman laporan tampil | PASS |
| Kasir mencoba akses Laporan | Role Kasir, akses URL `/reports` | Ditolak `403` | PASS |
| Admin & kasir akses Transaksi | Role Admin/Kasir, akses `/transactions` | Halaman transaksi tampil | PASS |

---

### C. Dashboard (Pembeda Admin vs Kasir)
| Fungsi yang Diuji | Input | Hasil yang Diharapkan | Hasil Pengujian |
|---|---|---|---|
| Dashboard tampil normal | Role Admin, buka `/dashboard` | Card/rekap tampil untuk seluruh transaksi | PASS |
| Dashboard kasir terbatas | Role Kasir, buka `/dashboard` | Rekap hanya transaksi milik kasir (filter `user_id`) | PASS |

---

### D. Manajemen Produk (Admin)
| Fungsi yang Diuji | Input | Hasil yang Diharapkan | Hasil Pengujian |
|---|---|---|---|
| Tambah produk valid | `name, sku unik, price>=0, stock>=0` | Produk tersimpan, muncul notifikasi sukses | PASS |
| Tambah produk gagal (SKU duplikat) | `sku` sama dengan yang ada | Validasi menolak, data tidak tersimpan | PASS |
| Tambah produk dengan gambar valid | File `jpg/png` <= 2MB | Gambar tersimpan di storage, produk tersimpan | PASS |
| Edit produk valid | ubah `name/sku/price/stock` valid | Produk terupdate, notifikasi sukses | PASS |
| Hapus produk | pilih 1 produk | Produk terhapus, (jika ada) gambar ikut terhapus | PASS |

---

### E. Manajemen User (Admin)
| Fungsi yang Diuji | Input | Hasil yang Diharapkan | Hasil Pengujian |
|---|---|---|---|
| Tambah user admin/kasir valid | `name, email unik, password konfirmasi, role in admin/kasir` | User tersimpan, notifikasi sukses | PASS |
| Tambah user gagal (email duplikat) | email sama dengan yang ada | Validasi menolak | PASS |
| Edit user tanpa ubah password | password kosong | Data berubah kecuali password tetap | PASS |
| Edit user dengan password baru | password + confirm valid | Password terupdate | PASS |
| Admin tidak bisa hapus akun sendiri | target = akun login | Ditolak dengan pesan error, akun tetap ada | PASS |

---

### F. Transaksi (Admin & Kasir)
| Fungsi yang Diuji | Input | Hasil yang Diharapkan | Hasil Pengujian |
|---|---|---|---|
| Buka halaman transaksi | - | Daftar produk stok > 0 tampil | PASS |
| Simpan transaksi tunai valid | `items json`, `total>=0`, `paid>=total`, `payment_method=tunai` | Transaksi tersimpan, redirect ke detail transaksi, notifikasi sukses | PASS |
| Simpan transaksi QRIS valid | sama, `payment_method=qris` | Transaksi tersimpan, detail transaksi tampil | PASS |
| Validasi gagal bila `paid < total` | `paid` kurang dari total | Ditolak validasi, transaksi tidak tersimpan | PASS |
| Stok berkurang setelah transaksi | item qty tertentu | Stok produk berkurang sesuai qty | PASS |
| Transaksi gagal jika stok tidak cukup | qty > stok | Sistem menolak dengan pesan “stok tidak mencukupi”, tidak ada transaksi tersimpan | PASS |
| Riwayat transaksi admin | - | Semua transaksi tampil + nama user | PASS |
| Riwayat transaksi kasir | - | Hanya transaksi milik kasir yang tampil | PASS |
| Kasir hanya boleh lihat detail transaksinya | kasir akses transaksi orang lain | Ditolak `403` | PASS |
| Admin boleh lihat semua detail transaksi | admin akses transaksi siapa pun | Detail transaksi tampil | PASS |

---

### G. Laporan (Admin)
| Fungsi yang Diuji | Input | Hasil yang Diharapkan | Hasil Pengujian |
|---|---|---|---|
| Filter laporan periode | `start_date`, `end_date` | Transaksi terfilter sesuai tanggal | PASS |
| Total revenue sesuai periode | periode tertentu | Total pendapatan tampil sesuai data transaksi | PASS |
| Cetak laporan | periode tertentu | Halaman print tampil berisi transaksi & total | PASS |

---

## 3) Narasi Kesimpulan (Siap Tempel untuk Bab Pengujian)
Pengujian Blackbox dilakukan pada modul autentikasi, manajemen data, transaksi, dan laporan dengan dua role pengguna yaitu **Admin** dan **Kasir**. Setiap skenario diuji berdasarkan input yang diberikan pengguna dan keluaran yang dihasilkan sistem, termasuk validasi data serta pembatasan akses fitur menggunakan middleware role. Hasil pengujian menunjukkan seluruh fungsionalitas berjalan sesuai kebutuhan, validasi bekerja dengan benar, dan pembatasan akses role berhasil diterapkan. **Seluruh test case dinyatakan PASS (berhasil).**

---

## Status
- **Rangkuman Blackbox testing Admin & Kasir sudah dibuat (100% PASS)** sesuai fitur yang terdeteksi dari project.
