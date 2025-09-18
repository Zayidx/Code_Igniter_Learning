<?php // Library client HTTP untuk berkomunikasi dengan API Wablas

declare(strict_types=1); // Pastikan tipe ketat aktif

namespace App\Libraries; // Namespace library aplikasi

use Config\Wablas as WablasConfig; // Import konfigurasi Wablas
use CodeIgniter\HTTP\ResponseInterface; // Interface respons HTTP CI4

class WablasClient // Kelas klien untuk memanggil endpoint Wablas
{
    private string $baseUrl;      // Base URL absolut, selalu diakhiri '/'
    private string $token;        // Token Authorization dari .env/config
    private ?string $secret;      // Secret opsional untuk bypass whitelist IP
    private string $epText;       // Path endpoint kirim teks
    private string $epImage;      // Path endpoint kirim gambar
    private string $epDocument;   // Path endpoint kirim dokumen
    private string $epTemplate;   // Path endpoint kirim template

    /** @var \CodeIgniter\HTTP\CURLRequest */
    private $http; // Instance HTTP client CI4 (CURLRequest)

    public function __construct(?string $baseUrl = null, ?string $token = null, ?string $secret = null) // Konstruktor klien
    {
        /** @var WablasConfig $config */
        $config = config('Wablas'); // Ambil konfigurasi Wablas dari Config

        $this->baseUrl = rtrim($baseUrl ?? $config->baseURL, '/') . '/'; // Bentuk base URL dengan trailing slash
        $this->token   = $token   ?? $config->token;   // Token prioritas argumen, fallback ke config
        $this->secret  = $secret  ?? $config->secret;  // Secret prioritas argumen, fallback ke config

        $this->http = \Config\Services::curlrequest([
            'baseURI'     => $this->baseUrl,   // Opsi benar untuk CURLRequest (camelCase)
            'timeout'     => 15,               // Timeout detik
            'http_errors' => false,            // Jangan throw pada 4xx/5xx; biarkan kita baca body
            'headers'     => [
                'Accept' => 'application/json', // Prefer respons JSON
            ],
        ]); // Buat instance HTTP client

        // Simpan path endpoint dari konfigurasi agar mudah diubah via .env
        $this->epText     = $config->endpointSendText;
        $this->epImage    = $config->endpointSendImage;
        $this->epDocument = $config->endpointSendDocument;
        $this->epTemplate = $config->endpointSendTemplate;
    }

    /**
     * Send plain text message.
     *
     * @return array{statusCode:int, body:string, json:mixed}
     */
    public function sendText(string $phone, string $message): array // Kirim pesan teks sederhana
    {
        $payload = [                              // Bangun payload form
            'phone'   => $this->normalizePhone($phone), // Normalisasi nomor ke format internasional
            'message' => $message,                        // Isi pesan teks
        ];
        $payload = $this->attachSecret($payload); // Sertakan secret jika tersedia (.env)

        $response = $this->http->post($this->endpoint($this->epText), [ // Panggil endpoint absolut
            'headers'     => [
                'Authorization' => $this->token, // Header Authorization sesuai Wablas
            ],
            'form_params' => $payload,           // Kirim sebagai x-www-form-urlencoded
        ]);

        return $this->wrapResponse($response);
    }

    /**
     * Send image by URL with optional caption.
     * @return array{statusCode:int, body:string, json:mixed}
     */
    public function sendImage(string $phone, string $imageUrl, ?string $caption = null): array // Kirim gambar via URL
    {
        $payload = [                                  // Payload dasar
            'phone' => $this->normalizePhone($phone), // Nomor tujuan
            'image' => $imageUrl,                      // URL gambar publik
        ];
        if ($caption !== null && $caption !== '') {   // Jika ada caption, sertakan
            $payload['caption'] = $caption;
        }
        $payload = $this->attachSecret($payload);     // Tambahkan secret jika perlu

        $response = $this->http->post($this->endpoint($this->epImage), [ // Panggil endpoint gambar
            'headers'     => [ 'Authorization' => $this->token ],        // Header otorisasi
            'form_params' => $payload,                                   // Body form-urlencoded
        ]);
        return $this->wrapResponse($response);
    }

    /**
     * Send document by URL with optional filename and caption.
     * @return array{statusCode:int, body:string, json:mixed}
     */
    public function sendDocument(string $phone, string $documentUrl, ?string $filename = null, ?string $caption = null): array // Kirim dokumen via URL
    {
        $payload = [                                      // Payload dasar
            'phone'    => $this->normalizePhone($phone),  // Nomor tujuan
            'document' => $documentUrl,                   // URL dokumen publik
        ];
        if ($filename !== null && $filename !== '') {     // Nama file opsional
            $payload['filename'] = $filename;
        }
        if ($caption !== null && $caption !== '') {       // Caption opsional
            $payload['caption'] = $caption;
        }
        $payload = $this->attachSecret($payload);         // Tambahkan secret

        $response = $this->http->post($this->endpoint($this->epDocument), [ // Panggil endpoint dokumen
            'headers'     => [ 'Authorization' => $this->token ],          // Header otorisasi
            'form_params' => $payload,                                     // Body form-urlencoded
        ]);
        return $this->wrapResponse($response);
    }

    /**
     * Send template message.
     * Parameters depend on your Wablas setup; this uses 'template' and 'data'.
     * @param array<string, string|int|float> $params Key-value parameters
     * @return array{statusCode:int, body:string, json:mixed}
     */
    public function sendTemplate(string $phone, string $template, array $params = []): array // Kirim pesan template
    {
        $payload = [                                    // Payload dasar
            'phone'    => $this->normalizePhone($phone), // Nomor tujuan
            'template' => $template,                      // Nama/ID template di Wablas
        ];

        if (! empty($params)) {                          // Jika ada parameter template
            // Beberapa versi memakai 'data' (JSON) atau 'parameters' array.
            // Default: kirim di field 'data' sebagai JSON string.
            $payload['data'] = json_encode($params, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        $payload = $this->attachSecret($payload);        // Sertakan secret

        $response = $this->http->post($this->endpoint($this->epTemplate), [ // Panggil endpoint template
            'headers'     => [ 'Authorization' => $this->token ],          // Header otorisasi
            'form_params' => $payload,                                     // Body form-urlencoded
        ]);
        return $this->wrapResponse($response);
    }

    private function attachSecret(array $payload): array // Tambahkan 'secret' ke payload bila tersedia
    {
        if ($this->secret !== null && $this->secret !== '') { // Jika secret diset di config
            $payload['secret'] = $this->secret;               // Sertakan field secret
        }
        return $payload; // Kembalikan payload (dengan/ tanpa secret)
    }

    private function endpoint(string $path): string // Bentuk URL absolut dari path endpoint
    {
        $p = ltrim($path, '/');          // Pastikan tidak ada leading '/'
        return $this->baseUrl . $p;      // Gabungkan baseUrl + path
    }

    private function wrapResponse(ResponseInterface $response): array // Bungkus respons menjadi array ringkas
    {
        $body = (string) $response->getBody();                   // Ambil body mentah
        $json = null;                                            // Siapkan tempat JSON terurai
        try {
            $json = json_decode($body, true, 512, JSON_THROW_ON_ERROR); // Coba parse JSON
        } catch (\Throwable $e) { /* Abaikan jika bukan JSON */ }

        return [
            'statusCode' => $response->getStatusCode(), // Kode status HTTP
            'body'       => $body,                      // Body mentah
            'json'       => $json,                      // Body terurai (array) bila valid JSON
        ];
    }

    private function normalizePhone(string $phone): string // Ubah nomor lokal → internasional (tanpa '+')
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? ''; // Ambil hanya digit
        if ($digits === '') {               // Jika kosong, kembalikan apa adanya
            return $phone;
        }
        if (str_starts_with($digits, '0')) {  // Nomor lokal diawali '0'
            $digits = '62' . substr($digits, 1); // Ganti '0' dengan kode negara Indonesia '62'
        }
        if (str_starts_with($digits, '620')) { // Tangani kasus '620' → '62'
            $digits = '62' . substr($digits, 3);
        }
        return ltrim($digits, '+'); // Pastikan tidak ada tanda '+'
    }
}
