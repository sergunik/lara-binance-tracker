<?php

namespace App\Services;

use App\Services\Interfaces\ChatGPTServiceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class ChatGPTService implements ChatGPTServiceInterface
{
    private string $apiKey;
    private string $apiUrl;
    private string $model;

    public function __construct()
    {
        $this->apiKey = config('services.chatgpt.api_key');
        $this->apiUrl = config('services.chatgpt.api_url');
        $this->model = config('services.chatgpt.model');
    }

    public function sendMessage(string $message): string
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl, [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $message
                    ]
                ]
            ]);

            if ($response->successful()) {
                $result = $response->json();
                return $result['choices'][0]['message']['content'];
            }

            Log::error('ChatGPT API Error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'headers' => $response->headers()
            ]);

            throw new Exception('Помилка при отриманні відповіді від ChatGPT. Статус: ' . $response->status() . '. Деталі: ' . $response->body());
        } catch (Exception $e) {
            Log::error('ChatGPT Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
} 