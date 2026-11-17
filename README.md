# Modul F-6: CRM (Client and Contact Management)

**ERP PT Anaheim Nimbus Universal — Kelompok F-6 (Kelas F)**

## 1. Deskripsi Aplikasi

Modul F-6 mengelola data klien, kontak, dan riwayat interaksi perusahaan di sistem ERP PT Anaheim Nimbus Universal. Modul ini menyediakan RESTful API referensi klien yang dipakai oleh modul lain (seperti modul Registrasi Proyek & Intake D-1), serta mengintegrasikan keamanan hak akses terpusat berbasis token JWT dari modul Identitas dan Akun (F-1).

Fitur yang dikembangkan:

- **Dashboard CRM**: Ringkasan metrik statistik klien, kontak, dan log interaksi terbaru.
- **Master Klien**: Daftar, pencarian, detail, tambah, dan edit data klien.
- **Kontak Klien**: Pengelolaan kontak per klien (PIC dan Direktur) maupun global.
- **Riwayat Interaksi**: Perekaman log komunikasi/interaksi klien dengan catatan dan waktu.
- **Pengarsipan & Pemulihan (Archive & Restore)**: Fitur soft-archive dan restore untuk klien serta kontak.
- **Autentikasi & Keamanan Hak Akses (F-1)**: Proteksi API berbasis token JWT F-1 via middleware `VerifyF1Token` (menolak token ilegal dengan 401 dan membatasi izin peran dengan 403).
- **Halaman Login Web**: Integrasi sesi login langsung ke server F-1.

---

## 2. Teknologi

| Teknologi | Keterangan |
|---|---|
| Laravel 13 | Framework backend dan tampilan (Blade) |
| PHP 8.3 / 8.4+ | Bahasa pemrograman backend |
| SQLite | Database lokal bawaan (`database/database.sqlite`) |
| Composer | Pengelola dependency PHP |
| REST API | Komunikasi antar-modul (`/api/v1`) |
| JavaScript (Fetch API) | Integrasi frontend memanggil API secara asynchronous |
| OpenAPI 3.0 | Dokumentasi kontrak API (`openapi/openapi.yaml`) |
| GitHub | Pengelolaan repositori dan kolaborasi tim |

---

## 3. Cabang (Branch)

| Cabang | Fungsi |
|---|---|
| `main` | **Cabang produksi / rilis**: berisi seluruh kode backend, frontend, dan integrasi terbaru yang stabil. |
| `integrasi` | **Cabang kerja integrasi**: penggabungan backend API, middleware F-1, dan antarmuka web. |
| `be_anandita` | Cabang kerja backend & API |
| `fe_mustika` | Cabang kerja frontend Blade |
| `design_fernanda` | Cabang kerja UI/UX designer |

---

## 4. Struktur Folder

```text
PPL2026_F6/
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/   # ClientController, ContactController, InteractionController, DashboardController, SessionController
│   │   └── Middleware/        # VerifyF1Token.php (verifikasi token & role ke F-1)
│   └── Models/                # Client, Contact, Interaction
├── database/
│   ├── migrations/            # Tabel clients, contacts, interactions (UUID)
│   └── seeders/               # ClientSeeder, DatabaseSeeder (data contoh CLI-001 s.d. CLI-003)
├── openapi/
│   └── openapi.yaml           # Dokumentasi spesifikasi kontrak API
├── public/
│   ├── css/                   # style.css, layout.css
│   └── js/api.js              # Interseptor fetch API (Bearer token & redirect otomatis)
├── resources/views/           # Halaman web Blade (login, dashboard, klien, kontak, riwayat, arsip, layouts)
├── routes/
│   ├── api.php                # Endpoint /api/v1 ber-middleware
│   └── web.php                # Rute antarmuka web
├── tests/                     # Automated testing (Feature & Unit)
├── .env.example
├── composer.json
└── README.md
```

---

## 5. Cara Menjalankan di Lokal

### A. Persyaratan
- PHP 8.3 / 8.4+ (dengan ekstensi `pdo_sqlite` dan `curl`)
- Composer
- Git
- Browser (Google Chrome / Microsoft Edge / Mozilla Firefox)

*(Pengguna **Laragon** sudah otomatis memiliki PHP dan Composer).*

### B. Langkah Instalasi

1. **Clone repositori:**
   ```bash
   git clone https://github.com/Anin05/PPL2026_F6.git
   cd PPL2026_F6
   ```

2. **Install dependensi PHP:**
   ```bash
   composer install
   ```

3. **Buat file environment `.env`:**
   * Di Windows CMD/PowerShell:
     ```cmd
     copy .env.example .env
     ```
   * Di Linux / macOS:
     ```bash
     cp .env.example .env
     ```

4. **Pastikan konfigurasi `.env` memiliki baris berikut:**
   ```env
   DB_CONNECTION=sqlite
   F1_URL=https://ppl-f-13948.containers.snapdeploy.app
   ```

5. **Generate App Key & Siapkan Database (Seeder):**
   ```bash
   php artisan key:generate
   php artisan migrate:fresh --seed
   ```
   *(Jika muncul pertanyaan konfirmasi pembuatan file database SQLite, ketik `yes` lalu tekan Enter).*

6. **Jalankan server aplikasi:**
   ```bash
   php artisan serve
   ```
   Aplikasi dapat diakses di browser pada: **`http://127.0.0.1:8000`**

---

## 6. Akun Uji Coba Demo (Integrasi Server F-1)

Sistem login terhubung langsung ke Server F-1 (`https://ppl-f-13948.containers.snapdeploy.app`).

> **Password semua akun demo:** `Password123!`

| Username / Email | Peran (Role) | Hak Akses Modul CRM F-6 | Skenario Uji |
|---|---|---|---|
| `super.admin` | **ADMIN** | **Diizinkan (200 OK)** | Akses penuh seluruh modul CRM |
| `yuril.salsabila` | **COO** | **Diizinkan (200 OK)** | Akses pimpinan operasional |
| `account.manager` | **AM** | **Diizinkan (200 OK)** | Pengelolaan data klien & kontak |
| `dwi.saputra` | **EMPLOYEE** | **Ditolak (403 Forbidden)** | Pengujian penolakan peran tidak berwenang |
| `maelani.tw` | **TW** | **Ditolak (403 Forbidden)** | Pengujian penolakan peran tidak berwenang |

---

## 7. Status Halaman Web

Seluruh antarmuka web telah terintegrasi penuh memanggil REST API lokal (`/api/v1`):

| Halaman | Alamat URL | Fungsi & Fitur | Status |
|---|---|---|---|
| **Login** | `/login` | Masuk ke sistem via autentikasi F-1 & simpan accessToken | **Selesai** |
| **Dashboard** | `/dashboard` | Agregasi statistik klien, kontak, dan riwayat interaksi terbaru | **Selesai** |
| **Master Klien** | `/klien` | Pencarian, filter kota, pagination, dan daftar klien aktif | **Selesai** |
| **Tambah Klien** | `/klien/tambah` | Formulir pendaftaran klien baru | **Selesai** |
| **Detail Klien** | `/klien/{kode}` | Profil klien, daftar kontak PIC/Direktur, dan log interaksi | **Selesai** |
| **Edit Klien** | `/klien/{kode}/edit`| Pembaruan data informasi klien | **Selesai** |
| **Kontak Klien** | `/kontak` | Pengelolaan data kontak global & tambah kontak per klien | **Selesai** |
| **Riwayat Interaksi** | `/riwayat` | Pencatatan komunikasi klien dengan filter tanggal & kanal | **Selesai** |
| **Arsip** | `/arsip` | Daftar klien nonaktif dan tombol pemulihan (*restore*) | **Selesai** |

---

## 8. Dokumentasi & Endpoint API

Kontrak API didokumentasikan dengan OpenAPI pada berkas [`openapi/openapi.yaml`](openapi/openapi.yaml).

Semua endpoint dilindungi oleh middleware `VerifyF1Token` dan mewajibkan header:
`Authorization: Bearer <accessToken>`

### Endpoint Utama (`/api/v1`):

* **Sesi Publik:**
  * `POST /session/login` — Login kredensial pengguna ke server F-1
* **Dashboard & Statistik:**
  * `GET /dashboard` — Statistik total klien, kontak, interaksi, dan log terbaru
* **Master Klien:**
  * `GET /clients` — Mendapatkan daftar klien (`q`, `status`, `city`, `limit`, `page`)
  * `POST /clients` — Mendaftarkan klien baru
  * `GET /clients/{id}` — Melihat detail klien (menerima UUID maupun kode klien)
  * `PUT /clients/{id}` — Memperbarui data klien
  * `PATCH /clients/{id}/status` — Mengubah status klien (active / archived)
* **Kontak Klien:**
  * `GET /contacts` & `POST /contacts` — Kontak global
  * `GET, POST /clients/{id}/contacts` — Kontak per klien
  * `PUT, PATCH, DELETE /contacts/{id}` — Operasi kontak
* **Riwayat Interaksi:**
  * `GET, POST /interactions` — Riwayat interaksi global
  * `GET, POST /clients/{id}/interactions` — Riwayat interaksi per klien

---

## 9. Integrasi Lintas Modul

- **F-1 (Identitas & Akses)**: 
  Menyediakan token JWT via endpoint `POST /auth/login`. Modul F-6 memverifikasi setiap request ke endpoint F-1 `GET /accounts/{id}/role` untuk memastikan validitas token dan kecocokan peran yang berwenang (`ADMIN`, `AM`, `CEO`, `COO`).
- **D-1 (Registrasi Proyek & Intake)**: 
  Mengonsumsi referensi klien aktif via endpoint `GET /api/v1/clients` dan `GET /api/v1/clients/{id}` dalam format JSON standar `{ "success": true, "data": [...] }`.

---

## 10. Pemecahan Masalah (Troubleshooting)

- **Database file does not exist:** Jalankan `php artisan migrate:fresh --seed` (pilih `yes` saat diminta).
- **Server F-1 sedang bangun (Error 503):** Server Snapdeploy gratisan memiliki fitur tidur otomatis setelah 15 menit *idle*. Tunggu 30–60 detik lalu klik tombol **Masuk** kembali.
- **Akses Ditolak (401 / 402 / 403):** Pastikan login menggunakan akun berwenang (`super.admin`, `yuril.salsabila`, atau `account.manager`).
- **Tampilan CSS tidak muncul:** Pastikan folder `public/css/` tersedia dan file `public/css/style.css` terpanggil di layout.

---

**Kelompok F-6 — Kelas F**  
*Program Studi Teknik Informatika, Universitas Pasundan*
