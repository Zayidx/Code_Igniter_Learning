<?php // Controller untuk halaman dan aksi pengiriman WhatsApp via Wablas

declare(strict_types=1); // Aktifkan strict types

namespace App\Controllers; // Namespace controller

use App\Libraries\WablasClient; // Import klien Wablas

class Whatsapp extends BaseController // Controller Whatsapp
{
    public function index() // Tampilkan form kirim WhatsApp
    {
        $config = config('Wablas'); // Ambil konfigurasi Wablas untuk ditampilkan (masking di View)
        return view('whatsapp/form', [ // Render view form
            'config' => $config,      // Kirim config ke view
        ]);
    }

    public function send() // Tangani submit form pengiriman
    {
        $type    = (string) $this->request->getPost('type', FILTER_DEFAULT) ?: 'text'; // Jenis pesan (text/image/document/template)
        $phone   = (string) $this->request->getPost('phone');   // Nomor tujuan
        $message = (string) $this->request->getPost('message'); // Pesan teks

        try {
            $client = new WablasClient(); // Inisialisasi klien Wablas (pakai config default)

            switch ($type) { // Switch sesuai jenis pesan
                case 'image':
                    $imageUrl = (string) $this->request->getPost('image_url');       // URL gambar
                    $caption  = (string) $this->request->getPost('image_caption');   // Caption opsional
                    if ($phone === '' || $imageUrl === '') {                          // Validasi minimal
                        return redirect()->back()->with('error', 'Nomor dan URL gambar wajib diisi.');
                    }
                    $result = $client->sendImage($phone, $imageUrl, $caption);       // Kirim gambar
                    break;

                case 'document':
                    $docUrl   = (string) $this->request->getPost('document_url');    // URL dokumen
                    $docName  = (string) $this->request->getPost('filename');        // Nama file opsional
                    $caption  = (string) $this->request->getPost('document_caption');// Caption opsional
                    if ($phone === '' || $docUrl === '') {                            // Validasi minimal
                        return redirect()->back()->with('error', 'Nomor dan URL dokumen wajib diisi.');
                    }
                    $result = $client->sendDocument($phone, $docUrl, $docName, $caption); // Kirim dokumen
                    break;

                case 'template':
                    $template = (string) $this->request->getPost('template');        // Nama/ID template
                    $raw      = (string) $this->request->getPost('params_json');     // JSON parameter
                    $params   = [];                                                  // Array parameter terurai
                    if ($template === '' || $phone === '') {                         // Validasi minimal
                        return redirect()->back()->with('error', 'Nomor dan nama template wajib diisi.');
                    }
                    if ($raw !== '') {                                               // Jika ada JSON param
                        try {
                            $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR); // Parse JSON
                            if (is_array($decoded)) {
                                $params = $decoded;                                   // Simpan bila array
                            }
                        } catch (\Throwable $e) {                                    // Tangani JSON invalid
                            return redirect()->back()->with('error', 'Format JSON parameter tidak valid.');
                        }
                    }
                    $result = $client->sendTemplate($phone, $template, $params);     // Kirim template
                    break;

                case 'text':
                default:
                    if ($phone === '' || $message === '') {                          // Validasi minimal
                        return redirect()->back()->with('error', 'Nomor dan pesan wajib diisi.');
                    }
                    $result = $client->sendText($phone, $message);                   // Kirim teks
            }

            $json = $result['json'];                                                 // Ambil respons JSON (jika ada)
            if (is_array($json) && ($json['status'] ?? false)) {                      // Jika sukses
                return redirect()->back()
                    ->with('success', 'Pesan berhasil dikirim.')                      // Flash pesan sukses
                    ->with('api_response', $json);                                    // Sertakan respons untuk ditampilkan
            }

            $msg = is_array($json) ? ($json['message'] ?? 'Gagal mengirim pesan') : 'Gagal mengirim pesan'; // Teks error
            return redirect()->back()
                ->with('error', $msg)                                                // Flash error
                ->with('api_response', $json ?? $result['body']);                    // Kirim body mentah bila bukan JSON
        } catch (\Throwable $e) {                                                    // Tangkap error tak terduga
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage()); // Tampilkan pesan exception
        }
    }
}
