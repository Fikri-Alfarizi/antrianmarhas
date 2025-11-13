<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ImgbbService
{
    private string $apiKey;
    private string $apiUrl = 'https://api.imgbb.com/1/upload';

    public function __construct()
    {
        // Ambil API key dari .env
        $this->apiKey = env('IMGBB_API_KEY', '');
    }

    /**
     * Upload image ke ImgBB
     * Fallback: Jika ImgBB timeout/gagal, simpan ke local storage
     * 
     * @param UploadedFile $file
     * @param string|null $name Nama custom untuk gambar
     * @return array|null
     */
    public function upload(UploadedFile $file, ?string $name = null): ?array
    {
        if (empty($this->apiKey)) {
            throw new \Exception('IMGBB_API_KEY tidak dikonfigurasi di .env');
        }

        try {
            \Log::info('Starting ImgBB upload for file: ' . $file->getClientOriginalName());
            
            // Encode file menjadi base64 string (ImgBB API menerima base64)
            $imageData = base64_encode(file_get_contents($file->getRealPath()));
            
            // Kirim ke ImgBB dengan format yang benar
            $response = Http::timeout(120)  // 120 detik timeout
                ->post($this->apiUrl, [
                    'key' => $this->apiKey,
                    'image' => $imageData,
                    'name' => $name ?? time(),
                ]);

            if ($response->successful()) {
                $data = $response->json();
                \Log::info('ImgBB Response Success');
                
                // Validasi struktur response
                if (!isset($data['data']) || !is_array($data['data'])) {
                    \Log::error('ImgBB Response structure invalid: ' . json_encode($data));
                    return $this->fallbackLocalStorage($file, $name);
                }
                
                $displayUrl = $data['data']['display_url'] ?? $data['data']['url'] ?? null;
                
                if (!$displayUrl) {
                    \Log::error('ImgBB Response missing display_url: ' . json_encode($data['data']));
                    return $this->fallbackLocalStorage($file, $name);
                }
                
                \Log::info('ImgBB Display URL: ' . $displayUrl);
                
                return [
                    'url' => $displayUrl,
                    'thumb' => $data['data']['thumb']['url'] ?? null,
                    'delete_url' => $data['data']['delete_url'] ?? null,
                    'id' => $data['data']['id'] ?? null,
                ];
            }

            \Log::error('ImgBB API Error: Status ' . $response->status() . ' - Response: ' . $response->body());
            return $this->fallbackLocalStorage($file, $name);
            
        } catch (\Exception $e) {
            \Log::error('ImgBB Upload Error: ' . $e->getMessage());
            // Fallback ke local storage jika timeout/gagal
            return $this->fallbackLocalStorage($file, $name);
        }
    }

    /**
     * Fallback: Simpan ke local storage dengan base64 encoding
     * Ini memastikan logo tetap tersimpan meski ImgBB gagal
     */
    private function fallbackLocalStorage(UploadedFile $file, ?string $name = null): ?array
    {
        try {
            \Log::info('Fallback: Menyimpan ke local storage');
            
            // Generate nama file unik
            $filename = ($name ?? 'logo-' . time()) . '.' . $file->getClientOriginalExtension();
            
            // Simpan ke public/storage/logos
            $path = Storage::disk('public')->putFileAs('logos', $file, $filename);
            
            // Generate URL untuk akses
            $url = asset('storage/' . $path);
            
            \Log::info('Fallback Success - Local URL: ' . $url);
            
            return [
                'url' => $url,
                'thumb' => $url,
                'delete_url' => null,
                'id' => 'local-' . time(),
                'method' => 'local_storage' // Mark as fallback
            ];
        } catch (\Exception $e) {
            \Log::error('Fallback Storage Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete image dari ImgBB
     * 
     * @param string $deleteUrl
     * @return bool
     */
    public function delete(string $deleteUrl): bool
    {
        try {
            $response = Http::timeout(30)->delete($deleteUrl);
            return $response->successful();
        } catch (\Exception $e) {
            \Log::error('ImgBB Delete Error: ' . $e->getMessage());
            return false;
        }
    }
}
