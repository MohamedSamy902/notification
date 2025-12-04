<?php

namespace App\Packages\FcmNotifications\Services\Firebase;

use App\Packages\FcmNotifications\Contracts\FcmAuthInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * خدمة المصادقة مع Firebase Cloud Messaging
 * 
 * مسؤولة عن:
 * - توليد Access Token باستخدام Service Account
 * - الحصول على Project ID
 */
class FcmAuthService implements FcmAuthInterface
{
    /**
     * الحصول على Access Token من Firebase
     *
     * @return string|null
     */
    public function getAccessToken(): ?string
    {
        $credentialsPath = storage_path(config('fcm-notifications.credentials_path', 'app/firebase_credentials.json'));

        if (!file_exists($credentialsPath)) {
            Log::error('Firebase credentials file not found', ['path' => $credentialsPath]);
            return null;
        }

        $credentials = json_decode(file_get_contents($credentialsPath), true);

        $now = time();
        $jwtHeader = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $jwtPayload = base64_encode(json_encode([
            'iss' => $credentials['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
        ]));

        $toSign = $jwtHeader . '.' . $jwtPayload;
        $signature = '';
        openssl_sign($toSign, $signature, $credentials['private_key'], 'sha256');
        $jwt = $toSign . '.' . base64_encode($signature);

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);

        return $response->json('access_token');
    }
    
    /**
     * الحصول على Project ID من Firebase
     *
     * @return string|null
     */
    public function getProjectId(): ?string
    {
        $credentialsPath = storage_path(config('fcm-notifications.credentials_path', 'app/firebase_credentials.json'));
        
        if (!file_exists($credentialsPath)) {
            return null;
        }
        
        $credentials = json_decode(file_get_contents($credentialsPath), true);
        return $credentials['project_id'] ?? null;
    }
}