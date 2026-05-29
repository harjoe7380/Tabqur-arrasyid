<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    /**
     * Send WhatsApp message via Fonnte
     *
     * @param string $target Phone number (e.g. 08123456789 or 628123456789)
     * @param string $message The message body
     * @return bool
     */
    public static function sendMessage($target, $message)
    {
        $token = config('services.fonnte.token');
        
        if (empty($token) || empty($target)) {
            Log::warning('Fonnte token or target is empty. Cannot send WA notification.');
            return false;
        }

        try {
            $response = Http::withoutVerifying()->withHeaders([
                'Authorization' => $token,
            ])->post('https://api.fonnte.com/send', [
                'target' => $target,
                'message' => $message,
                'countryCode' => '62', // Default to Indonesia
            ]);

            if ($response->successful()) {
                return true;
            } else {
                Log::error('Fonnte API Error: ' . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Fonnte Exception: ' . $e->getMessage());
            return false;
        }
    }
}
