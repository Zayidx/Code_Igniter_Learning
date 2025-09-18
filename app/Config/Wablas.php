<?php // File konfigurasi Wablas untuk CodeIgniter 4

declare(strict_types=1); // Aktifkan strict types untuk keamanan tipe data

namespace Config; // Namespace Config bawaan CI4 untuk file konfigurasi

use CodeIgniter\Config\BaseConfig; // Base class konfigurasi CI4

class Wablas extends BaseConfig // Kelas konfigurasi khusus Wablas yang mewarisi BaseConfig
{
    /**
     * Base URL cluster Wablas Anda, contoh: https://sby.wablas.com
     */
    public string $baseURL = 'https://sby.wablas.com'; // Nilai default, akan dioverride dari .env jika ada

    /**
     * Token API untuk header Authorization.
     */
    public string $token = ''; // Token dibaca dari .env agar tidak hardcode di kode sumber

    /**
     * Secret key opsional (misalnya untuk bypass whitelist IP atau verifikasi webhook).
     */
    public ?string $secret = null; // Secret juga diambil dari .env jika tersedia

    /**
     * Path endpoint (ubah bila cluster Anda memakai versi/path berbeda seperti api/v2)
     */
    public string $endpointSendText     = 'api/send-message'; // Endpoint kirim teks
    public string $endpointSendImage    = 'api/send-image'; // Endpoint kirim gambar via URL
    public string $endpointSendDocument = 'api/send-document'; // Endpoint kirim dokumen via URL
    public string $endpointSendTemplate = 'api/send-template'; // Endpoint kirim template

    public function __construct() // Konstruktor untuk memuat nilai dari .env
    {
        parent::__construct(); // Panggil konstruktor induk BaseConfig

        // Baca baseURL dari env (prioritas: wablas.baseURL, WABLAS_BASE_URL, lalu default property)
        $envBase = (string) (env('wablas.baseURL') ?? env('WABLAS_BASE_URL') ?? $this->baseURL);
        $this->baseURL = rtrim($envBase, '/'); // Pastikan tidak ada trailing slash ganda

        // Baca token dari env (prioritas: wablas.token atau WABLAS_TOKEN)
        $this->token = (string) (env('wablas.token') ?? env('WABLAS_TOKEN') ?? '');
        // Baca secret dari env (prioritas: wablas.secret atau WABLAS_SECRET)
        $secret = env('wablas.secret') ?? env('WABLAS_SECRET');
        $this->secret = $secret !== null ? (string) $secret : null; // Set ke null jika tidak ada

        // Izinkan override path endpoint melalui env (berguna jika cluster memakai api/v2)
        $this->endpointSendText     = (string) (env('wablas.endpoint.text')     ?? env('WABLAS_ENDPOINT_TEXT')     ?? $this->endpointSendText);
        $this->endpointSendImage    = (string) (env('wablas.endpoint.image')    ?? env('WABLAS_ENDPOINT_IMAGE')    ?? $this->endpointSendImage);
        $this->endpointSendDocument = (string) (env('wablas.endpoint.document') ?? env('WABLAS_ENDPOINT_DOCUMENT') ?? $this->endpointSendDocument);
        $this->endpointSendTemplate = (string) (env('wablas.endpoint.template') ?? env('WABLAS_ENDPOINT_TEMPLATE') ?? $this->endpointSendTemplate);
    }
}
