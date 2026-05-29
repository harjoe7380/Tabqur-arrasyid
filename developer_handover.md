# 🛠️ Buku Pintar Developer: Sistem Tabungan Qurban (TABQUR)
**Versi: 1.0** *(Dokumen ini akan terus diperbarui jika ada update mayor di masa depan)*

Dokumen ini adalah "Buku Pintar" rahasia khusus untuk Anda sebagai Developer. Dokumen ini berisi pemetaan arsitektur, tumpukan teknologi (*tech stack*), alat-alat pihak ketiga, serta daftar kredensial penting agar Anda bisa melakukan pemeliharaan (*maintenance*) atau menyerahkan proyek ini di masa depan tanpa kebingungan.

---

## 1. Arsitektur & Teknologi Utama
Aplikasi ini dibangun menggunakan arsitektur **Monolith** berbasis PHP yang sangat kokoh dengan pendekatan *Single-Tenant* (Satu instalasi = Satu DKM).

*   **Framework Backend:** Laravel 12.0 (PHP 8.2+)
*   **Framework Frontend:** Bootstrap 5 (Blade Templating Engine)
*   **Database Utama:** PostgreSQL Serverless (Dihosting di Neon.tech)
*   **Cloud Hosting:** Render.com (Web Service Berbasis Docker)
*   **Version Control:** Git & GitHub

## 2. Alat & API Pihak Ketiga (Tools)
Aplikasi ini mengandalkan beberapa alat luar (*Third-Party API*) yang krusial untuk fitur-fitur intinya:

1.  **Neon.tech (Database Cloud)**
    *   **Fungsi:** Menyimpan seluruh data tabel secara gratis di *cloud* agar aplikasi di Render menjadi ringan (Stateless).
    *   **Catatan Dev:** Neon memisahkan *connection string* menjadi dua: Pooled (`-pooler`) untuk kecepatan baca, dan Direct (tanpa `-pooler`) wajib untuk migrasi tabel (`php artisan migrate`).
2.  **Fonnte.com (WhatsApp Gateway)**
    *   **Fungsi:** Mengirimkan notifikasi dan struk digital secara otomatis ke nomor WhatsApp jamaah.
    *   **Catatan Dev:** Konfigurasinya diletakkan di `config/services.php` agar tetap terbaca setelah perintah `php artisan config:cache` dieksekusi di *server production*. Kode utamanya ada di `app/Services/FonnteService.php`.
3.  **barryvdh/laravel-dompdf**
    *   **Fungsi:** Men- *generate* struk resmi berformat PDF yang bisa diunduh oleh Admin.
4.  **Render.com (PaaS Deployment)**
    *   **Fungsi:** Menjalankan *source code* secara otomatis menggunakan `Dockerfile`. 
    *   **Keterbatasan Server Gratis:** Fitur penyimpanan file lokal (seperti bukti transfer gambar) akan terhapus (*reset*) jika *server* tertidur. *Workaround* terbaik di masa depan adalah menyambungkannya ke AWS S3 atau Cloudinary.

---

## 3. Direktori Penting (Core Logic)
Jika Anda ingin mengubah logika aplikasi, berfokuslah pada file-file berikut:
*   `app/Http/Controllers/AdminController.php` (Pusat logika Dasbor Admin, verifikasi transaksi, cetak PDF, pembuatan jamaah).
*   `app/Http/Controllers/ParticipantController.php` (Pusat logika Jamaah, lapor setoran, lihat grafik/kekurangan).
*   `app/Http/Controllers/ReceiptController.php` (Pusat pengiriman WA dan format teks struk).
*   `app/Models/Setting.php` (Sistem dinamis untuk menyimpan nama DKM, no Rekening, dll di *database* agar tidak mati tertulis di dalam kode).
*   `Dockerfile` & `entrypoint.sh` (Skrip sakti yang menyalakan *server* di Render dan otomatis merapikan *cache* dan migrasi).

---

## 4. Daftar Kredensial Penting (Credentials)

> [!CAUTION]
> Jangan pernah membagikan halaman ini kepada siapapun (termasuk Admin DKM), karena berisi kata sandi utama *database* dan API.

### A. Akses Website (Admin Bawaan)
*   **Login URL:** `[URL-RENDER-ANDA]/login`
*   **Email:** `admin@tabqur.com`
*   **Password:** `password123`

### B. Database & Web Services (Production Render)

> **1. Klien: DKM Ar-Rasyid**
> *   **Web URL:** `https://tabqur-arrasyid.onrender.com` (Sesuaikan jika diubah)
> *   **Database Host:** `ep-dawn-cell-ao4d8tu3.c-2.ap-southeast-1.aws.neon.tech`
> *   **Database Name:** `neondb`
> *   **Username:** `neondb_owner`
> *   **Password:** `npg_ceB6fXsyJw7P`
> *   **Port:** `5432`

> **2. Klien: DKM Assalafiyyah**
> *   **Web URL:** `https://tabqur-assalafiyyah.onrender.com` (Sesuaikan jika diubah)
> *   **Database Host:** `ep-icy-thunder-aots0i5f.c-2.ap-southeast-1.aws.neon.tech`
> *   **Database Name:** `neondb`
> *   **Username:** `neondb_owner`
> *   **Password:** `npg_Ac0DxMlYzrC9`
> *   **Port:** `5432`

### C. Fonnte WhatsApp API
*   **Token API:** `FMEk8WAbTnxWaquJEWgx`
*   *Catatan:* Pastikan HP yang digunakan untuk *scan* Fonnte di dasbor mereka selalu terhubung ke internet. Jika paket Fonnte habis, pesan akan otomatis masuk status *Pending*.

### D. Repositori GitHub
*   **URL Asal (Origin):** `https://github.com/harjoe7380/Tabqur-arrasyid.git`
*   **Branch Utama:** `main`

---

## 5. Alur Deployment (SOP Update Aplikasi)
Jika Anda mengembangkan aplikasi ini secara lokal (di laptop Anda `c:\tabqur`) dan ingin menaikkan *update* ke seluruh klien (DKM Masjid manapun yang sudah tersambung di Render), lakukan urutan baku berikut:

1. Tes kode di laptop (Pastikan `.env` lokal mengarah ke `127.0.0.1` atau *database* *dummy*, jangan ke Neon agar data produksi tidak kacau).
2. Jika sudah aman, jalankan di Terminal:
   ```bash
   git add .
   git commit -m "Penjelasan singkat fitur yang diupdate"
   git push origin main
   ```
3. Pekerjaan Anda selesai! Render akan secara otomatis menangkap *push* GitHub tersebut, lalu membangun ulang aplikasi, menjalankan `php artisan migrate`, dan menyalakannya ke publik tanpa campur tangan Anda.
