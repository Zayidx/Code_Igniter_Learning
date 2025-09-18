# Belajar API Wablas — Kurikulum Pemula → Mahir

Dokumen ini memandu Anda dari nol sampai mahir menggunakan API Wablas. Berisi konsep dasar, langkah praktik bertahap, checklist produksi, serta kumpulan prompt siap pakai untuk belajar dengan bantuan Gemini AI.

---

## 1) Gambaran Umum
- Wablas menyediakan API HTTP untuk mengirim pesan WhatsApp dari perangkat Anda.
- Komponen penting:
  - Base URL cluster perangkat (contoh: `https://sby.wablas.com`).
  - API Token (header `Authorization`).
  - Secret key (opsional; untuk melewati whitelist IP atau verifikasi webhook).
  - Endpoint inti: `send-message`, `send-image`, `send-document`, `send-template`.
- Mekanisme keamanan: whitelist IP atau sertakan `secret` pada body request.

---

## 2) Prasyarat Teknis (Pemula)
Pelajari dulu hal berikut agar lancar memakai API:
- HTTP dasar: method (GET/POST), header, status code (2xx/4xx/5xx).
- JSON: format, serialisasi, escape karakter.
- Alat uji API: `curl` dan Postman/Insomnia.
- Variabel lingkungan: `.env`, cara menyimpan token/secret dengan aman.
- Sedikit jaringan: perbedaan domain, IP, firewall, dan DNS.
- (Opsional, untuk integrasi CI4) Dasar PHP 8.1+ dan CodeIgniter 4 (Controller, Config, Services, Routes, Views).

Referensi cepat HTTP/JSON:
- Status 200-299: sukses; 400-499: kesalahan klien (validasi, otorisasi); 500-599: kesalahan server.
- Body JSON tipikal: `{ "status": true|false, "message": "...", ... }`.

---

## 3) Menyiapkan Lingkungan
1) Dapatkan kredensial dari dashboard Wablas:
- Base URL cluster (mis. `https://<cluster>.wablas.com`).
- API Token.
- Secret key (jika mau bypass whitelist IP).

2) Simpan aman ke `.env` (jangan commit):
```
WABLAS_BASE_URL="https://<cluster>.wablas.com"
WABLAS_TOKEN="<YOUR_TOKEN>"
WABLAS_SECRET="<YOUR_SECRET>"   # opsional tapi disarankan
```

3) Whitelist IP vs Secret Key:
- Opsi A: whitelist IP server Anda di dashboard perangkat Wablas.
- Opsi B: sertakan `secret` pada body request (`form_params`) di setiap panggilan.

4) Uji koneksi cepat (curl):
```
curl -X POST "https://<cluster>.wablas.com/api/send-message" \
  -H "Authorization: <YOUR_TOKEN>" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  --data "phone=62812xxxxxxx&message=Halo%20Wablas&secret=<YOUR_SECRET>"
```
Jika 403 “Access denied: IP … not authorized”, pastikan `secret` dikirim atau whitelist IP sudah benar.

---

## 4) Peta API Inti Wablas
- Kirim teks: `POST /api/send-message` (wajib `phone`, `message`; opsional `secret`).
- Kirim gambar: `POST /api/send-image` (wajib `phone`, `image` URL; opsional `caption`, `secret`).
- Kirim dokumen: `POST /api/send-document` (wajib `phone`, `document` URL; opsional `filename`, `caption`, `secret`).
- Kirim template: `POST /api/send-template` (wajib `phone`, `template`; parameter tergantung konfigurasi; opsional `data/parameters`, `secret`).
- Webhook status pesan: atur URL webhook di dashboard; verifikasi dengan `secret`/signature jika ada.

Catatan:
- Beberapa cluster memakai path versi lain, contoh `api/v2/...`. Sesuaikan jika endpoint v1 menolak.
- Beberapa setup mewajibkan `Authorization: Bearer <TOKEN>` (alih-alih token polos). Cek dokumentasi perangkat Anda.

---

## 5) Kurikulum Belajar (Modul Bertahap)

Modul 0 — Dasar HTTP & JSON (1–2 jam)
- Tujuan: paham request/response, header, status code.
- Latihan: kirim request dummy ke httpbin.org (GET/POST), baca status & body.

Modul 1 — Quick Start Wablas (1–2 jam)
- Tujuan: kirim pesan teks via `curl` dan Postman.
- Latihan: uji kirim teks ke nomor Anda sendiri; coba dengan/ tanpa `secret` untuk melihat perbedaan 403.

Modul 2 — Penanganan Error & Keamanan (1–2 jam)
- Tujuan: bedakan 401 vs 403 vs 422; pahami whitelist IP vs secret key.
- Latihan: sengaja kirim token salah; format nomor salah; URL media tidak dapat diakses publik.

Modul 3 — Media (Gambar/Dokumen) (2–3 jam)
- Tujuan: kirim gambar dan dokumen melalui URL publik; pahami `caption` dan `filename`.
- Latihan: kirim JPG/PNG/PDF; cek ukuran/aksesibilitas URL.

Modul 4 — Template (2–3 jam)
- Tujuan: kirim template sesuai konfigurasi device; paham payload `data/parameters`.
- Latihan: siapkan template di dashboard; isi parameter dinamis; validasi respons.

Modul 5 — Integrasi CodeIgniter 4 (3–4 jam)
- Tujuan: bungkus API di library; gunakan config & `.env`; buat form kirim.
- Referensi repo ini:
  - `app/Config/Wablas.php` (namespace `Config`) membaca `.env`.
  - `app/Libraries/WablasClient.php` menyediakan `sendText/Image/Document/Template` dan mengikutkan `secret` otomatis.
  - `app/Controllers/Whatsapp.php` + `app/Views/whatsapp/form.php` sebagai UI uji cepat.
- Latihan: tambah validasi input; logging; menampilkan error detail respons.

Modul 6 — Webhook & Status Pesan (3–4 jam)
- Tujuan: terima callback delivery/read; verifikasi dengan `secret`; simpan ke DB/log.
- Latihan: buat route webhook di CI4; validasi signature (jika tersedia); uji dengan ngrok/forwarder.

Modul 7 — Menuju Produksi (4–6 jam)
- Topik: retry dengan backoff, idempoten (jika diperlukan), rate limit & throttling, observability (logs/metrics), pengelolaan rahasia (ENV/secret manager), pemisahan env (dev/staging/prod), monitoring kegagalan.
- Latihan: tulis wrapper retry; circuit breaker ringan; audit log minimal.

Modul 8 — Keamanan & Kepatuhan (2–3 jam)
- Topik: lindungi PII (nomor WA), sanitasi input, akses minimal ke token, rotasi token, audit akses, izin aplikasi.
- Latihan: implementasi masking di UI/log; pisahkan hak akses server.

Modul 9 — Lanjutan (opsional)
- Topik: bulk messaging/scheduling, grup, template interaktif, dashboard kecil, testing otomatis (PHPUnit) untuk wrapper Wablas.
- Latihan: buat job scheduler untuk broadcast terbatas dengan antrian dan pembatasan laju.

---

## 6) Praktik Terarah (Tugas)
- Tugas A: Kirim teks ke 1 nomor; tampilkan respons JSON utuh; log hasil ke file.
- Tugas B: Kirim gambar dan dokumen; tangani error URL tidak publik.
- Tugas C: Kirim template dengan parameter dinamis; fallback jika template gagal.
- Tugas D: Buat endpoint webhook; verifikasi `secret`; simpulkan status ke DB.
- Tugas E: Tambahkan retry eksponensial untuk error 5xx dan network; fail-fast untuk 4xx.
- Tugas F: Buat halaman riwayat pengiriman (filter status, rentang waktu).

Kriteria Sukses Umum:
- Tidak ada rahasia di repository (hanya di `.env`).
- Respons API ditampilkan/log lengkap untuk debugging.
- Nomor diproses ke format internasional (62…) secara konsisten.
- Error 4xx/5xx tertangani dengan jelas (pesan user-friendly + detail teknis).

---

## 7) Troubleshooting Cepat
- 401 Unauthorized: token salah/expired → perbarui token.
- 403 Forbidden: IP tidak di-whitelist atau butuh `secret` → whitelist IP atau sertakan `secret`.
- 422 Unprocessable Entity: payload salah (nomor tidak valid, parameter template tidak sesuai) → validasi dan koreksi data.
- 429 Too Many Requests: batasi laju; tambahkan retry/backoff.
- 5xx Server Error: coba ulang (retry/backoff), cek status perangkat.
- Media gagal: pastikan URL dapat diakses publik dan ukuran file sesuai batas Wablas.
- “Bearer” vs token polos: beberapa cluster perlu `Authorization: Bearer <TOKEN>`.

---

## 8) Prompt Gemini AI (Siap Pakai)
Gunakan, modifikasi, dan jalankan prompt di bawah ini sesuai konteks Anda.

1) Penjelasan Konsep
"Jelaskan cara kerja API Wablas untuk pemula: apa itu base URL cluster, peran Authorization token, fungsi secret key dan whitelist IP, serta contoh alur kirim pesan dari aplikasi ke perangkat. Sertakan analogi dan diagram teks sederhana."

2) Rencana Belajar Bertahap
"Buat kurikulum 2 minggu belajar API Wablas untuk pemula. Bagi per hari: tujuan, materi, sumber, dan tugas kecil. Sertakan evaluasi harian dan final project sederhana."

3) Quick Start (curl/Postman)
"Tunjukkan contoh curl dan Postman lengkap untuk mengirim WhatsApp teks, gambar, dan dokumen melalui Wablas. Jelaskan semua field dan header, serta respons yang diharapkan."

4) Debugging Error
"Saya mendapat error 403 Access Denied dari Wablas. Analisis kemungkinan penyebabnya (IP whitelist vs secret, token, endpoint versi), berikan checklist pemeriksaan, dan rekomendasikan urutan perbaikan."

5) Integrasi CodeIgniter 4
"Tulis kode CodeIgniter 4: Config membaca .env (base URL, token, secret), Services CURLRequest, Library client (sendText/Image/Document/Template), Controller, View form, dan Routes. Sertakan validasi input, normalisasi nomor, serta penanganan error yang baik."

6) Webhook & Keamanan
"Buat spesifikasi endpoint webhook untuk menerima status pesan Wablas. Sertakan validasi secret/signature, contoh payload, skema tabel penyimpanan, dan contoh kode verifikasi di PHP (CI4)."

7) Produksi & Reliabilitas
"Rancang strategi retry dengan backoff untuk panggilan API Wablas, termasuk klasifikasi error (idempoten atau tidak), batas retry, jitter, dan logging/metrics yang perlu dicatat."

8) Quality/Testing
"Tuliskan rencana pengujian untuk wrapper Wablas di PHP: unit test, test integrasi (mock), skenario error (401/403/422/429/5xx), dan strategi test data (nomor dummy, URL media)."

9) Audit & Keamanan Praktis
"Buat checklist keamanan untuk aplikasi yang menggunakan Wablas: manajemen rahasia, masking PII, rotasi token, kontrol akses, logging aman, dan hardening server."

10) Review Kode
"Review kode WablasClient berikut untuk keamanan, keandalan, dan gaya. Berikan saran konkret per bagian dan prioritas perbaikan. [tempelkan potongan kode Anda]"

11) Template & Parametrisasi
"Jelaskan cara mendesain payload template Wablas dengan parameter dinamis. Beri contoh pola `data` atau `parameters`, serta cara validasi sebelum mengirim."

12) Observability
"Usulkan skema logging dan metrik (counter/success/error/latency) untuk integrasi Wablas agar mudah dipantau di produksi."

13) Migrasi/Versi Endpoint
"Bantu saya menilai perbedaan endpoint `api/` vs `api/v2/` pada Wablas. Bagaimana cara mendesain client agar mudah diubah versi endpoint via konfigurasi?"

14) Tutor Langsung (Socratic)
"Bertanya-jawab interaktif untuk mengajarkan saya REST, HTTP status, header, dan JSON dengan contoh Wablas. Mulai dari pertanyaan dasar, lalu tingkatkan kompleksitas bertahap."

Tips memakai prompt:
- Tambahkan konteks Anda (cluster, kebutuhan, potongan kode, error aktual) agar jawaban lebih presisi.
- Minta langkah-langkah konkret, contoh kode, dan checklist.

---

## 9) Checklist Go-Live Singkat
- [ ] Token/secret di `.env`, tidak di repository.
- [ ] IP server di-whitelist atau `secret` selalu dikirim.
- [ ] Endpoint cocok dengan cluster (v1/v2). Header Authorization sesuai.
- [ ] Validasi input nomor (format internasional) dan payload template.
- [ ] Retry/on-failure dan logging aktif.
- [ ] Webhook status terpasang, tervalidasi secret, dan tersimpan dengan benar.

---

## 10) Catatan untuk Repo Ini (CI4)
- Halaman uji cepat: `GET /whatsapp`.
- Pengiriman: `POST /whatsapp/send`.
- Kode utama:
  - Config: `app/Config/Wablas.php` (namespace `Config`).
  - Library: `app/Libraries/WablasClient.php` (otomatis sertakan `secret`).
  - Controller: `app/Controllers/Whatsapp.php`.
  - View: `app/Views/whatsapp/form.php`.
- Variabel `.env` yang dipakai: `WABLAS_BASE_URL`, `WABLAS_TOKEN`, `WABLAS_SECRET` (dan override endpoint opsional `WABLAS_ENDPOINT_*`).

Selamat belajar! Simpan catatan eksperimen Anda di bawah dokumen ini bila perlu.

