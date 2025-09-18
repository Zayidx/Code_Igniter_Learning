<?php // View: Form sederhana untuk mengirim pesan via Wablas
/** @var Config\Wablas $config */ // Tipe variabel config yang diteruskan dari controller
use CodeIgniter\I18n\Time; // Kelas waktu untuk menampilkan timestamp

$success = session()->getFlashdata('success'); // Ambil pesan sukses dari flashdata
$error   = session()->getFlashdata('error');   // Ambil pesan error dari flashdata
$apiResp = session()->getFlashdata('api_response'); // Ambil respons API untuk ditampilkan

// Fungsi util untuk memasker token/secret saat ditampilkan di UI
$mask = static function (?string $value): string {
    if ($value === null || $value === '') return '—'; // Tampilkan dash jika kosong
    $len = strlen($value);                              // Panjang string
    if ($len <= 6) return str_repeat('•', $len);       // Jika pendek, masker semua
    return substr($value, 0, 3) . str_repeat('•', max(0, $len - 7)) . substr($value, -4); // 3 awal + 4 akhir
};
?>
<!doctype html> <!-- Deklarasi tipe dokumen HTML5 -->
<html lang="id"> <!-- Bahasa Indonesia -->
<head> <!-- Kepala dokumen untuk meta dan style -->
    <meta charset="utf-8"> <!-- Encoding karakter UTF-8 -->
    <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- Responsive -->
    <title>WhatsApp via Wablas</title> <!-- Judul halaman -->
    <style> /* Gaya sederhana agar tampilan rapi */
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif; margin: 24px; color: #222; }
        .card { max-width: 720px; margin: 0 auto; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; }
        .row { display: flex; gap: 16px; }
        .field { margin-bottom: 14px; }
        label { display: block; font-size: 14px; color: #374151; margin-bottom: 6px; }
        input[type="text"], textarea { width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; }
        textarea { min-height: 110px; resize: vertical; }
        .btn { appearance: none; background: #10b981; color: #fff; border: none; padding: 10px 14px; border-radius: 6px; cursor: pointer; font-weight: 600; }
        .btn:disabled { opacity: .6; cursor: not-allowed; }
        .muted { color: #6b7280; font-size: 13px; }
        .alert { padding: 10px 12px; border-radius: 6px; margin-bottom: 12px; }
        .success { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
        .error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
        pre { background: #0b1021; color: #e5e7eb; padding: 12px; border-radius: 6px; overflow: auto; }
        .config { background: #f9fafb; border: 1px dashed #e5e7eb; padding: 10px; border-radius: 6px; }
    </style>
    <?= csrf_meta() ?> <!-- Meta tag CSRF untuk keamanan form -->
    <meta name="robots" content="noindex"> <!-- Jangan diindeks mesin pencari -->
    <meta name="referrer" content="no-referrer"> <!-- Jangan kirim referrer -->
    <meta http-equiv="Content-Security-Policy" content="default-src 'self' 'unsafe-inline' data:;"> <!-- CSP sederhana -->
    <script> // Helper untuk mengisi contoh nilai cepat
        function fillExample() {
            const phone = document.getElementById('phone'); // Input nomor
            const msg = document.getElementById('message');  // Input pesan teks
            if (!phone.value) phone.value = '628123456789';  // Contoh nomor
            if (!msg.value) msg.value = 'Halo dari Wablas (' + new Date().toLocaleString() + ')'; // Contoh pesan
        }
    </script>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin> <!-- Optimasi font -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com"> <!-- DNS Prefetch -->
    <link rel="dns-prefetch" href="https://fonts.googleapis.com"> <!-- DNS Prefetch -->
    <link rel="preconnect" href="<?= esc($config->baseURL) ?>" crossorigin> <!-- Preconnect ke Wablas -->
    <link rel="dns-prefetch" href="<?= esc($config->baseURL) ?>"> <!-- DNS Prefetch ke Wablas -->
    <meta name="wablas-base" content="<?= esc($config->baseURL) ?>"> <!-- Info base URL -->
  </head>
  <body> <!-- Badan dokumen -->
  <div class="card"> <!-- Kartu utama berisi form dan hasil -->
      <h2 style="margin-top:0">Kirim WhatsApp via Wablas</h2> <!-- Judul -->
      <p class="muted">Base URL: <strong><?= esc($config->baseURL) ?></strong> | Token: <code><?= esc($mask($config->token)) ?></code> | Secret: <code><?= esc($mask($config->secret)) ?></code></p> <!-- Info config (dimask) -->

      <?php if ($success): ?> <!-- Tampilkan alert sukses jika ada -->
        <div class="alert success"><?= esc($success) ?></div>
      <?php endif; ?>
      <?php if ($error): ?> <!-- Tampilkan alert error jika ada -->
        <div class="alert error"><?= esc($error) ?></div>
      <?php endif; ?>

      <form method="post" action="<?= site_url('whatsapp/send') ?>"> <!-- Form kirim -->
          <?= csrf_field() ?> <!-- Field CSRF hidden untuk proteksi -->
          <div class="field"> <!-- Pilihan jenis pesan -->
              <label for="type">Jenis Pesan</label>
              <select id="type" name="type" onchange="toggleType()"> <!-- Pilih jenis pesan -->
                  <option value="text" <?= old('type')==='text' ? 'selected' : '' ?>>Teks</option>
                  <option value="image" <?= old('type')==='image' ? 'selected' : '' ?>>Gambar</option>
                  <option value="document" <?= old('type')==='document' ? 'selected' : '' ?>>Dokumen</option>
                  <option value="template" <?= old('type')==='template' ? 'selected' : '' ?>>Template</option>
              </select>
          </div>
          <div class="field"> <!-- Input nomor tujuan -->
              <label for="phone">Nomor WhatsApp (format internasional tanpa +)</label>
              <input id="phone" name="phone" type="text" placeholder="62812xxxxxxxx" value="<?= esc(old('phone','')) ?>" required>
              <div class="muted">Jika nomor lokal diawali 0, akan diubah ke 62 otomatis.</div>
          </div>
          <div id="section-text" class="field"> <!-- Seksi pesan teks -->
              <label for="message">Pesan</label>
              <textarea id="message" name="message" placeholder="Tulis pesan..."><?= esc(old('message','')) ?></textarea>
          </div>

          <div id="section-image" class="field" style="display:none"> <!-- Seksi gambar -->
              <label for="image_url">URL Gambar</label>
              <input id="image_url" name="image_url" type="text" placeholder="https://.../image.jpg" value="<?= esc(old('image_url','')) ?>">
              <label for="image_caption" style="margin-top:8px">Caption (opsional)</label>
              <input id="image_caption" name="image_caption" type="text" placeholder="Teks pendamping" value="<?= esc(old('image_caption','')) ?>">
          </div>

          <div id="section-document" class="field" style="display:none"> <!-- Seksi dokumen -->
              <label for="document_url">URL Dokumen</label>
              <input id="document_url" name="document_url" type="text" placeholder="https://.../file.pdf" value="<?= esc(old('document_url','')) ?>">
              <label for="filename" style="margin-top:8px">Nama File (opsional)</label>
              <input id="filename" name="filename" type="text" placeholder="file.pdf" value="<?= esc(old('filename','')) ?>">
              <label for="document_caption" style="margin-top:8px">Caption (opsional)</label>
              <input id="document_caption" name="document_caption" type="text" placeholder="Teks pendamping" value="<?= esc(old('document_caption','')) ?>">
          </div>

          <div id="section-template" class="field" style="display:none"> <!-- Seksi template -->
              <label for="template">Nama/ID Template</label>
              <input id="template" name="template" type="text" placeholder="mis. order_update" value="<?= esc(old('template','')) ?>">
              <label for="params_json" style="margin-top:8px">Parameter (JSON)</label>
              <textarea id="params_json" name="params_json" placeholder='{"1":"John","2":"#1234"}'><?= esc(old('params_json','')) ?></textarea>
              <div class="muted">Gunakan format JSON sederhana key→value. Sesuaikan dengan pengaturan template di Wablas.</div>
          </div>
          <div class="row" style="align-items:center; justify-content:space-between;"> <!-- Area tombol dan waktu -->
              <div> <!-- Tombol aksi -->
                  <button class="btn" type="submit">Kirim Pesan</button>
                  <button class="btn" type="button" onclick="fillExample()" style="background:#3b82f6; margin-left:8px">Contoh</button>
              </div>
              <div class="muted"><?= esc(Time::now('Asia/Jakarta')->toDateTimeString()) ?></div> <!-- Waktu saat ini -->
          </div>
      </form>

      <?php if ($apiResp): ?> <!-- Tampilkan respons API jika ada -->
        <h3>Respons API</h3>
        <pre><?= esc(is_string($apiResp) ? $apiResp : json_encode($apiResp, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)) ?></pre>
      <?php endif; ?>

      <div class="config" style="margin-top:14px"> <!-- Catatan tambahan -->
          <strong>Catatan:</strong>
          <ul>
              <li>Pastikan perangkat di Wablas aktif dan domain cluster benar.</li>
              <li>Untuk media/template, tambahkan endpoint lain di library.</li>
              <li>Token/secret dibaca dari <code>.env</code> melalui <code>Config\Wablas</code>.</li>
          </ul>
      </div>
  </div>
  </body>
  </html>

<script> // Skrip untuk menampilkan input sesuai jenis pesan
  function toggleType() { // Tampilkan seksi sesuai pilihan
    const type = document.getElementById('type').value; // Baca jenis
    const ids = ['section-text','section-image','section-document','section-template']; // Semua seksi
    ids.forEach(id => document.getElementById(id).style.display = 'none'); // Sembunyikan semua
    if (type === 'text') document.getElementById('section-text').style.display = 'block'; // Teks
    if (type === 'image') document.getElementById('section-image').style.display = 'block'; // Gambar
    if (type === 'document') document.getElementById('section-document').style.display = 'block'; // Dokumen
    if (type === 'template') document.getElementById('section-template').style.display = 'block'; // Template
  }
  toggleType(); // Inisialisasi saat load
  const oldType = '<?= esc(old('type','text')) ?>'; // Ambil nilai sebelumnya dari old()
  if (oldType) { document.getElementById('type').value = oldType; toggleType(); } // Sinkronkan jika ada
</script>

