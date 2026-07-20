# Dokumen Handover: VEDA Banjarnegara
**Visualisasi, Evaluasi, & Data Analitik Kemiskinan Ekstrem Kabupaten Banjarnegara**

Dokumen ini berisi rangkuman teknis dan fungsional sistem VEDA sebagai panduan serah terima proyek (Handover) untuk diteruskan oleh tim pengembang atau administrator sistem di masa mendatang.

---

## 1. Ikhtisar Sistem
VEDA Banjarnegara adalah aplikasi web analitik dan sistem pendukung keputusan (DSS) yang dibangun khusus untuk memetakan, menganalisa, memprediksi, dan mensimulasikan data kemiskinan ekstrem serta distribusi Bantuan Sosial (Bansos) Pangan di wilayah Kabupaten Banjarnegara.

### Teknologi Utama (Tech Stack)
* **Backend:** PHP 8.2, Laravel Framework 12.x
* **Database:** SQLite (`database/database.sqlite`)
* **Frontend:** Blade Templating Engine
* **Styling:** Tailwind CSS (via CDN)
* **Interaktivitas:** Alpine.js (via CDN)
* **Visualisasi Grafik:** Chart.js (via CDN)
* **Visualisasi Peta (GIS):** Leaflet.js + GeoJSON

---

## 2. Arsitektur Modul & Fungsionalitas Utama

Sistem ini menggunakan pendekatan *Data Science Lifecycle* yang diimplementasikan langsung ke dalam arsitektur MVC Laravel:

### A. Dasbor Spasial (Analitik Deskriptif)
* **Fungsi:** Memetakan sebaran kemiskinan dan penerima bansos ke dalam peta interaktif polygon per kecamatan.
* **Controller:** `DashboardController.php`
* **View:** `dasbor.blade.php`
* **Sumber Data Peta:** `public/geojson/peta_kecamatan.geojson`

### B. Analisa Korelasi (Analitik Diagnostik)
* **Fungsi:** Mengukur hubungan (*Pearson Correlation*) antara Tingkat Kemiskinan dengan Penyaluran Bansos. Termasuk klasifikasi Kuadran (Scatter Plot).
* **Controller:** `AnalisaController.php`
* **View:** `analisa.blade.php`

### C. Analisa Klaster (Machine Learning / Unsupervised)
* **Fungsi:** Mengelompokkan kecamatan menjadi 3 Klaster (Tinggi, Menengah, Rendah) menggunakan algoritma **K-Means Clustering** untuk menentukan Target Intervensi prioritas.
* **Controller:** `ClusterController.php`
* **View:** `cluster.blade.php`
* **Catatan Algoritma:** Termasuk fitur *Anti-Duplikasi* (*Foreach Reference Bug Fix*) dan Normalisasi Typo (Cth: `PUWONEGORO` -> `PURWANEGARA`).

### D. Prediksi Tren (Peramalan / Analitik Prediktif)
* **Fungsi:** Memproyeksikan angka kemiskinan 3 tahun ke depan menggunakan **Linear Regression** (Least Squares) dan algoritma tingkat lanjut **Double Exponential Smoothing** (Holt's Method).
* **Controller:** `PrediksiController.php`
* **View:** `prediksi.blade.php`

### E. Simulasi Kebijakan (Analitik Preskriptif)
* **Fungsi:** Kalkulator *What-If Analysis* untuk mengukur potensi penurunan penduduk miskin jika pemerintah melakukan injeksi/penambahan anggaran di suatu kecamatan (berdasarkan Rasio Elastisitas).
* **Controller:** `SimulasiController.php`
* **View:** `simulasi.blade.php`

---

## 3. Struktur Database (Schema)

Sistem menggunakan SQLite dengan tabel utama (Models) sebagai berikut:
1. `KemiskinanData`: Menyimpan agregat historis kabupaten dari tahun ke tahun.
2. `KesejahteraanKecamatan`: Menyimpan data penduduk sangat miskin (Desil 1) per tahun per kecamatan.
3. `BantuanSosial`: Menyimpan agregat jumlah penerima bansos dan anggarannya per kecamatan.

---

## 4. Panduan Pengembangan (Developer Guide)

### Cara Menjalankan Aplikasi di Lokal
1. Pastikan ekstensi PHP yang dibutuhkan Laravel aktif (terutama SQLite/PDO).
2. Buka terminal/Command Prompt di folder `i:\kemiskinan\aplikasi`.
3. Jalankan server lokal:
   ```bash
   php artisan serve --port=8001
   ```
4. Buka peramban (browser) di: `http://localhost:8001/`

### Manajemen Modifikasi UI/UX
Seluruh antarmuka telah didesain dengan konsep **Aesthetic Premium (Glassmorphism, Neon Gradients, Bento Grid)**. 
* Semua *styling* di-inject melalui *class* Tailwind CSS secara *inline*.
* Jika ingin mengubah skema warna, pastikan Anda memahami kelas seperti `bg-slate-900`, gradasi `from-indigo-900 to-purple-900`, dan efek transparan `backdrop-blur-md`.

### Memperbarui Peta (GeoJSON)
Jika peta Banjarnegara dirasa kurang presisi, Anda cukup me-replace *file* `public/geojson/peta_kecamatan.geojson`.
**Penting:** Pastikan *properties* di dalam file GeoJSON baru tetap memiliki atribut `"WADMKC"` (Nama Kecamatan) yang dieja persis sama dengan database.

---

## 5. Rencana Masa Depan (Future Roadmap)
Jika proyek ini akan dikembangkan lebih besar (Tahap 2):
1. **Otomatisasi Data:** Integrasi API langsung dari BPS atau DTKS Kementerian Sosial.
2. **Autentikasi (Login):** Penambahan fitur *Role-Based Access Control (RBAC)* dengan Laravel Breeze/Jetstream agar fitur Simulasi hanya bisa diakses oleh Bupati/Bappeda.
3. **Database Migration:** Mengganti SQLite dengan PostgreSQL atau MySQL/MariaDB untuk menangani data level Desa (Kelurahan) yang mencapai ribuan baris.

---
*Dokumen ini digenerate secara otomatis oleh Antigravity pada akhir masa pengembangan proyek.*
