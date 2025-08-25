<?php

namespace App\Services;

use App\Models\OAuthToken;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class MicrosoftAuthService
{
    protected string $tokenUrl = 'https://login.microsoftonline.com/{tenant}/oauth2/v2.0/token';

    public function exchangeCode(string $code, int $userId): OAuthToken
    {
        $response = Http::asForm()->post($this->tokenUrl, [
            'client_id'     => config('services.microsoft.client_id'),
            'client_secret' => config('services.microsoft.client_secret'),
            'grant_type'    => 'authorization_code',
            'code'          => $code,
            'redirect_uri'  => config('services.microsoft.redirect'),
        ]);

        $data = $response->json();

        return OAuthToken::updateOrCreate(
            ['user_id' => $userId, 'provider' => 'microsoft'],
            [
                'access_token'  => $data['access_token'],
                'refresh_token' => $data['refresh_token'] ?? null,
                'token_type'    => $data['token_type'] ?? null,
                'expires_in'    => $data['expires_in'],
                'expires_at'    => Carbon::now()->addSeconds($data['expires_in']),
            ]
        );
    }
}
