# Modul F-6: CRM (Client and Contact Management)

**ERP PT Anaheim Nimbus Universal — Kelompok F-6 (Kelas F)**

## 1. Deskripsi Aplikasi

Modul F-6 mengelola data klien dan kontak perusahaan di sistem ERP. Modul ini juga menyediakan API referensi klien yang dipakai modul lain, misalnya Registrasi Proyek (D-1).

Fitur yang dikembangkan:

- Master klien: daftar, tambah, ubah, dan pencarian.
- Kode klien unik untuk setiap perusahaan.
- Pengarsipan klien dan kontak (data tidak dihapus permanen).
- Kontak klien: PIC dan Direktur.
- Riwayat interaksi dengan klien.
- REST API `/api/v1` sebagai sumber data klien untuk modul lain.

## 2. Teknologi

| Teknologi | Keterangan |
|---|---|
| Laravel 13 | Framework backend dan tampilan (Blade) |
| PHP 8.4+ | Bahasa pemrograman |
| SQLite | Database lokal bawaan |
| Composer | Pengelola dependency PHP |
| REST API | Komunikasi antar modul (`/api/v1`) |
| JavaScript (fetch) | Halaman web memanggil API |
| OpenAPI 3.0 | Dokumentasi API (`openapi/openapi.yaml`) |
| GitHub | Pengelolaan repositori |

## 3. Cabang (Branch)

| Cabang | Fungsi |
|---|---|
| `integrasi` | **Cabang kerja utama**: backend API + halaman web yang memakai API |
| `be_anandita` | Backend API (asal cabang `integrasi`) |
| `fe_mustika` | Tampilan web (asal tampilan di `integrasi`) |
| `main` | Cabang utama repositori |

Gunakan cabang **`integrasi`** untuk menjalankan aplikasi.

## 4. Struktur Folder

```text
PPL2026_F6/
├── app/
│   ├── Http/Controllers/Api/   # ClientController, ContactController, InteractionController
│   └── Models/                 # Client, Contact, Interaction
├── database/
│   ├── migrations/             # tabel clients, contacts, interactions
│   └── seeders/                # ClientSeeder, DatabaseSeeder (data contoh)
├── examples/                   # contoh respons JSON
├── openapi/
│   └── openapi.yaml            # dokumentasi API
├── public/
│   ├── css/                    # style.css, layout.css
│   └── js/api.js               # fungsi pemanggil API dari halaman web
├── resources/views/            # halaman web: klien, kontak, riwayat, arsip, layouts
├── routes/
│   ├── api.php                 # endpoint /api/v1
│   └── web.php                 # halaman web
├── tests/
├── .env.example
├── composer.json
└── README.md
```

## 5. Cara Menjalankan di Lokal

### A. Persyaratan

- PHP **8.4 atau lebih baru** (dengan ekstensi `pdo_sqlite`)
- Composer
- Git
- Browser (Postman opsional untuk uji API)

Di Windows, **Laragon** sudah berisi PHP dan Composer. Pastikan versi PHP-nya 8.4+.

### B. Langkah-langkah

```bash
git clone -b integrasi https://github.com/Anin05/PPL2026_F6.git
cd PPL2026_F6
composer install
```

Salin file environment (Windows CMD):

```cmd
copy .env.example .env
```

(Linux atau macOS: `cp .env.example .env`)

Pastikan di `.env` tertulis:

```env
DB_CONNECTION=sqlite
```

Buat application key, tabel, dan data contoh:

```bash
php artisan key:generate
php artisan migrate:fresh --seed
```

Jika Laravel bertanya apakah file database SQLite ingin dibuat, jawab `yes`. Perintah `migrate:fresh --seed` menghapus dan membuat ulang seluruh tabel, lalu mengisinya dengan data contoh.

Jalankan server:

```bash
php artisan serve
```

Aplikasi berjalan di `http://127.0.0.1:8000`.

### C. Data Contoh (Seeder)

`ClientSeeder` mengisi data fiktif: 3 klien (CLI-001 sampai CLI-003) beserta kontak PIC/Direktur dan riwayat interaksi. Seeder aman dijalankan ulang dengan `php artisan db:seed`.

## 6. Halaman Web

| Alamat | Keterangan | Status |
|---|---|---|
| `/klien` | Daftar Master Klien (data dari `GET /api/v1/clients`) | Berjalan |
| `/klien/tambah` | Form Tambah Klien (mengirim ke `POST /api/v1/clients`) | Tampil; simpan masih ditolak API karena kode klien otomatis belum selesai |
| `/klien/{kode}` | Detail klien | Belum dikonversi ke API |
| `/klien/{kode}/edit` | Ubah klien | Belum dikonversi ke API |
| `/kontak`, `/riwayat`, `/arsip` | Kontak, Riwayat Interaksi, Arsip | Halaman penampung |

## 7. Dokumentasi API

Kontrak API ditulis dengan OpenAPI pada berkas [`openapi/openapi.yaml`](openapi/openapi.yaml).

Endpoint utama (awalan `/api/v1`):

| Metode | Path | Fungsi |
|---|---|---|
| GET | `/clients` | Daftar klien (`q`, `status`, `page`, `limit`) |
| GET | `/clients/{kode}` | Detail klien |
| POST | `/clients` | Tambah klien |
| PUT | `/clients/{id}` | Ubah klien |
| PATCH | `/clients/{id}/status` | Arsipkan klien |
| GET, POST | `/clients/{id}/contacts` | Daftar dan tambah kontak |
| PUT | `/clients/{id}/contacts/{contactId}` | Ubah kontak |
| PATCH | `/clients/{id}/contacts/{contactId}/status` | Arsipkan kontak |
| GET, POST | `/clients/{id}/interactions` | Riwayat dan catat interaksi |

Contoh:

```text
http://127.0.0.1:8000/api/v1/clients
http://127.0.0.1:8000/api/v1/clients/CLI-001
```

**Catatan format respons saat ini:** pembungkus respons berupa `success`, `data`, dan `meta`. Pengenal klien memakai `id` (UUID) dan `code`, sedangkan status bernilai `active` atau `archived`. Format ini **belum final** dan masih disesuaikan dengan kontrak bersama modul lain, jadi baca respons asli API sebelum mengintegrasikan. Endpoint kontak dan interaksi saat ini menerima UUID klien, bukan kode.

## 8. Integrasi dengan Modul Lain

- **D-1 (Registrasi Proyek)** mengambil klien lewat `GET /api/v1/clients` dan `GET /api/v1/clients/{kode}`.
- **F-1 (Identitas dan Akses)** direncanakan sebagai penyedia token dan peran. Autentikasi belum terhubung, sehingga API belum membatasi akses berdasarkan peran.

## 9. Status Pengembangan

Sudah ada:
- Struktur backend, endpoint API, dan data contoh.
- Halaman daftar klien yang membaca API.
- Form tambah klien yang memanggil API.

Belum selesai:
- Pembuatan kode klien otomatis di backend.
- Halaman detail dan ubah klien memakai API.
- Penyesuaian format respons dengan kontrak.
- Autentikasi dan pembatasan peran (F-1).

## 10. Pemecahan Masalah

- **`composer install` gagal karena versi PHP:** gunakan PHP 8.4 atau lebih baru.
- **Halaman daftar klien tertulis "Gagal memuat data klien":** pastikan `http://127.0.0.1:8000/js/api.js` bisa dibuka dan server `php artisan serve` masih menyala.
- **Tampilan polos tanpa gaya:** pastikan folder `public/css` ada.
- **Data klien kosong:** jalankan `php artisan migrate:fresh --seed`.

---

**Kelompok F-6**
Program Studi Teknik Informatika, Universitas Pasundan
