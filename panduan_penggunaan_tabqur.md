# Buku Panduan Sistem Tabungan Qurban (TABQUR)

Dokumen ini adalah panduan resmi penggunaan Sistem Informasi Tabungan Qurban yang dapat diaplikasikan untuk operasional **DKM Ar-Rasyid**, **DKM Assalafiyyah**, maupun DKM lainnya.

Panduan ini terbagi menjadi 2 bagian: **Panduan Admin DKM** dan **Panduan Jamaah (Peserta Kurban)**.

---

## 👨‍💼 BAGIAN 1: PANDUAN ADMIN DKM

Sebagai Admin DKM, Anda memegang kendali penuh atas sistem, mulai dari memverifikasi dana, mendaftarkan jamaah, hingga mencetak laporan.

### 1. Cara Masuk (Login)
1. Buka *link* website DKM Anda (contoh: `https://tabqur-arrasyid.onrender.com`).
2. Klik tombol **"Masuk Dashboard"** di pojok kanan atas.
3. Masukkan **Email** dan **Password** Admin Anda.
4. Anda akan langsung diarahkan ke Dasbor Utama Admin.

### 2. Mengatur Profil & Nama Masjid
> [!IMPORTANT]
> Lakukan hal ini pertama kali agar seluruh website berubah namanya sesuai nama DKM Anda (Ar-Rasyid / Assalafiyyah).
1. Di Dasbor Admin, klik tombol **"⚙️ Pengaturan"**.
2. Masukkan **Nama Masjid**, **Alamat**, **Nomor Rekening**, dan **Nomor WA Admin**.
3. Klik **Simpan Pengaturan**. Halaman depan dan kop surat PDF akan otomatis mengikuti nama yang baru.

### 3. Cara Mengelola Jamaah
Meskipun jamaah bisa mendaftar sendiri, Admin juga bisa mendaftarkan jamaah (biasanya untuk jamaah lansia).
1. Klik tombol **"👥 Manajemen Jamaah"**.
2. **Tambah Jamaah:** Klik tombol **"+ Tambah Jamaah Manual"**, isikan nama, No. WA (penting untuk notifikasi), dan target tabungan (contoh: 3500000).
3. **Edit Jamaah:** Klik tombol kuning **"✏️ Edit"** di sebelah nama jamaah jika ingin mengubah target tabungan atau mengganti nomor WA.
4. **Hapus Jamaah:** Klik tombol merah **"🗑️ Hapus"** (Harap berhati-hati, tindakan ini akan menghapus seluruh riwayat tabungan jamaah tersebut).

### 4. Cara Mencatat Setoran Uang
1. Di Dasbor Admin, klik tombol hijau **"+ Catat Setoran / Penarikan"**.
2. Pilih nama Jamaah dari daftar.
3. Pilih jenis transaksi (**Uang Masuk / Setoran**).
4. Masukkan **Nominal** uang yang disetorkan.
5. *(Opsional)* Isi keterangan (contoh: Tabungan Bulan 1).
6. Klik **Simpan & Kirim WA**.
7. Sistem akan otomatis merekam setoran dan **langsung mengirim pesan WhatsApp (Struk Digital)** ke nomor HP jamaah!

### 5. Memverifikasi Laporan Setoran dari Jamaah
Jika jamaah melaporkan transfer sendiri dari *smartphone* mereka:
1. Laporan tersebut akan muncul di kotak kuning **"Menunggu Verifikasi"** pada Dasbor Admin.
2. Anda bisa klik **"Lihat Bukti"** untuk mencocokkan struk mutasi bank dengan rekening DKM.
3. Jika uang sudah masuk, klik **"✔ Setujui"**.
4. Jika uang belum masuk atau bukti palsu, klik **"✖ Tolak"**.

### 6. Plotting Hewan (Mengelompokkan 7 Orang untuk 1 Sapi)
1. Klik menu **"🐮 Plotting Hewan"** di Dasbor Admin.
2. Klik **"+ Tambah Kelompok"**, beri nama (contoh: "Sapi Kelompok 1 - Limosin").
3. Di dalam kelompok tersebut, tambahkan maksimal 7 jamaah kurban yang tabungannya sudah mencukupi.

### 7. Mencetak Laporan Keuangan Akhir
1. Klik tombol hitam **"Cetak Laporan Keuangan"** di Dasbor.
2. Sistem akan membuka halaman laporan resmi yang siap untuk di- *print* (Cetak PDF/Kertas) sebagai pertanggungjawaban kepada seluruh jamaah di papan pengumuman masjid.

---

## 👳‍♂️ BAGIAN 2: PANDUAN PENGGUNA (JAMAAH)

Panduan ini bisa Anda bagikan (*copy-paste* via WA) kepada para jamaah Anda agar mereka mengerti cara menabung.

### 1. Cara Mendaftar Tabungan Kurban
1. Buka *link* website Masjid.
2. Klik tombol biru **"Mulai Menabung Kurban"** atau **"Daftar Kurban Sekarang"**.
3. Isikan **Nama Lengkap**, **Alamat Email**, **Nomor WA** (awali dengan awalan biasa, misal 0812...), dan **Password**.
4. Tentukan **Target Harga Kurban** (Bisa ditanyakan ke panitia DKM berapa taksiran harga sapi/domba tahun ini).
5. Klik **Daftar Sekarang**.

### 2. Cara Melaporkan Setoran (Transfer)
> [!TIP]
> Fitur ini digunakan jika jamaah transfer bank/e-wallet dan tidak sempat menyerahkan uang tunai ke panitia secara langsung.
1. Setelah Login, jamaah akan masuk ke **Portal Peserta**.
2. Klik tombol **"Lapor Setoran Transfer"**.
3. Masukkan jumlah uang yang baru saja ditransfer.
4. Lampirkan foto bukti transfer / *screenshot* M-Banking.
5. Klik **Kirim Bukti**.
6. Status tabungan akan menjadi "Menunggu Verifikasi" hingga Admin DKM mengecek uangnya.

### 3. Memantau Progres Tabungan
- Di halaman utama Portal Peserta, jamaah bisa langsung melihat persentase keberhasilan (contoh: `60% Terkumpul`).
- Jamaah bisa melihat daftar rincian tanggal berapa saja mereka pernah menabung di bagian bawah (Riwayat Setoran).
- Jamaah tidak perlu lagi bertanya sisa kurangnya berapa, karena angka **Kekurangan** sudah tertulis dengan huruf tebal berwarna merah (atau hijau jika sudah lunas).

---

> [!NOTE]
> Jika sistem WhatsApp tidak berfungsi saat Admin mencatat setoran, pastikan perangkat WhatsApp Admin terhubung ke internet. Pengaturan lebih lanjut bisa dikonsultasikan dengan tim teknis/Developer.
