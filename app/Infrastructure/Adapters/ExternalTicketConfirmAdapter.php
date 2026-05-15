<?php

namespace App\Infrastructure\Adapters;

use App\Models\TicketLog;
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
            $this->setLog($data['id'], $response->status(), json_decode($response->body())->message ?? '--');

            return [
                'status' => $response->status(),
                'successful' => $response->successful(),
                'body' => $response->json(),
            ];
        } catch (RequestException $e) {
            $this->setLog($data['id'], 500, 'Server Error!');
            return [
                'status' => 500,
                'successful' => false,
            ];
        }
    }

    private function setLog(int $ticketId, int $status, string $message): void
    {
        TicketLog::create([
            'ticket_id' => $ticketId,
            'status' => $status,
            'message' => $message,
        ]);
    }


}
