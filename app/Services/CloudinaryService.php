<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class CloudinaryService
{
    private $cloudName;
    private $apiKey;
    private $apiSecret;

    public function __construct()
    {
        $this->cloudName = env('CLOUDINARY_CLOUD_NAME');
        $this->apiKey = env('CLOUDINARY_API_KEY');
        $this->apiSecret = env('CLOUDINARY_API_SECRET');
    }

    /**
     * Upload file ke Cloudinary menggunakan signed upload dengan authentication
     */
    public function uploadFile(UploadedFile $file, $folder = 'antrian')
    {
        try {
            $uploadUrl = "https://api.cloudinary.com/v1_1/{$this->cloudName}/image/upload";

            // Build timestamp
            $timestamp = time();
            
            // Build parameters yang akan di-sign
            $params = [
                'api_key' => $this->apiKey,
                'folder' => $folder,
                'overwrite' => true,
                'timestamp' => $timestamp,
            ];

            // Create signature
            $paramsStr = implode('&', array_map(
                function ($k, $v) {
                    return "{$k}={$v}";
                },
                array_keys($params),
                array_values($params)
            )) . $this->apiSecret;

            $signature = hash_hmac('sha1', $paramsStr, $this->apiSecret);
            $params['signature'] = $signature;

            // Create multipart form data
            $ch = curl_init();
            
            curl_setopt_array($ch, [
                CURLOPT_URL => $uploadUrl,
                CURLOPT_POST => true,
                CURLOPT_SAFE_UPLOAD => true,
                CURLOPT_POSTFIELDS => array_merge($params, [
                    'file' => new \CURLFile($file->getRealPath(), $file->getMimeType()),
                ]),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_VERBOSE => false,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError) {
                throw new \Exception('cURL Error: ' . $curlError);
            }

            if ($httpCode !== 200) {
                \Log::error('Cloudinary HTTP Error ' . $httpCode . ': ' . $response);
                throw new \Exception('Cloudinary returned HTTP ' . $httpCode);
            }

            $data = json_decode($response, true);
            
            if (!isset($data['secure_url'])) {
                \Log::error('Cloudinary Response: ' . $response);
                throw new \Exception('No secure_url in response');
            }

            return $data['secure_url'];
        } catch (\Exception $e) {
            \Log::error('Cloudinary Upload Error: ' . $e->getMessage());
            throw new \Exception('Gagal upload ke Cloudinary: ' . $e->getMessage());
        }
    }

    /**
     * Delete file dari Cloudinary
     */
    public function deleteFile($url)
    {
        // Optional: implement delete later if needed
        return true;
    }
}
