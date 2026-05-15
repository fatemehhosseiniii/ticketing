<?php

namespace App\Infrastructure\Adapters;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class ExternalTicketConfirmAdapter
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.confirm_endpoint.base_url');
    }

    public function confirm(array $data): array
    {
        try {
            $response = Http::post($this->baseUrl . '/endpoint/fake/confirm-ticket', $data);
            return [
                'status' => $response->status(),
                'successful' => $response->successful(),
                'body' => $response->json(),
            ];
        } catch (RequestException $e) {
            return [
                'status' => 500,
                'successful' => false,
            ];
        }
    }


}
