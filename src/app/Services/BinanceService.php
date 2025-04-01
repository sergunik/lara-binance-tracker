<?php

namespace App\Services;

use App\Services\Interfaces\BinanceServiceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class BinanceService implements BinanceServiceInterface
{
    private string $apiKey;
    private string $secretKey;
    private string $baseUrl;
    private ?array $accountInfo = null;
    private ?array $tickerPrices = null;
    private ?array $spotBalances = null;
    private ?array $earnPositions = null;

    public function __construct()
    {
        $this->baseUrl = config('services.binance.base_url');
        $this->apiKey = config('services.binance.api_key');
        $this->secretKey = config('services.binance.secret_key');
    }

    private function generateSignature(array $params): string
    {
        $queryString = http_build_query($params);
        return hash_hmac('sha256', $queryString, $this->secretKey);
    }

    private function makeSignedRequest(string $method, string $endpoint, array $params = []): array
    {
        try {
            $timestamp = round(microtime(true) * 1000);
            $params['timestamp'] = $timestamp;
            $params['signature'] = $this->generateSignature($params);

            $response = Http::withHeaders([
                'X-MBX-APIKEY' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->$method($this->baseUrl . $endpoint, $params);

            if ($response->successful()) {
                $jsonResponse = $response->json();
                if (!is_array($jsonResponse)) {
                    Log::error('Binance API returned invalid JSON response');
                    throw new Exception('API повернув невалідну JSON відповідь');
                }
                return $jsonResponse;
            }

            throw new Exception('Помилка при отриманні даних від Binance API. Статус: ' . $response->status());
        } catch (Exception $e) {
            Log::error('Binance Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function makePublicRequest(string $method, string $endpoint): array
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->$method($this->baseUrl . $endpoint);

            if ($response->successful()) {
                $jsonResponse = $response->json();
                if (!is_array($jsonResponse)) {
                    Log::error('Binance Public API returned invalid JSON response');
                    throw new Exception('API повернув невалідну JSON відповідь');
                }
                return $jsonResponse;
            }

            throw new Exception('Помилка при отриманні даних від Binance Public API. Статус: ' . $response->status());
        } catch (Exception $e) {
            Log::error('Binance Public Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function getAccountInfo(): array
    {
        if ($this->accountInfo === null) {
            $this->accountInfo = $this->makeSignedRequest('get', '/api/v3/account');
        }
        return $this->accountInfo;
    }

    private function getTickerPrices(): array
    {
        if ($this->tickerPrices === null) {
            $this->tickerPrices = $this->makePublicRequest('get', '/api/v3/ticker/price');
        }
        return $this->tickerPrices;
    }

    public function getAssetPrice(string $asset): float
    {
        $tickerPrices = $this->getTickerPrices();
        $symbol = $asset . 'USDT';
        
        foreach ($tickerPrices as $ticker) {
            if ($ticker['symbol'] === $symbol) {
                return floatval($ticker['price']);
            }
        }

        if ($asset === 'USDT') {
            return 1.0;
        }

        Log::error('Не знайдено ціну для активу', [
            'asset' => $asset,
            'symbol' => $symbol
        ]);
        
        throw new Exception("Не вдалося знайти ціну для {$asset}");
    }

    public function getSpotBalances(): array
    {
        if ($this->spotBalances === null) {
            $accountInfo = $this->getAccountInfo();
            $balances = array_filter($accountInfo['balances'], function($balance) {
                return !str_starts_with($balance['asset'], 'LD') && 
                       (floatval($balance['free']) > 0 || floatval($balance['locked']) > 0);
            });

            $this->spotBalances = array_map(function($balance) {
                $amount = floatval($balance['free']) + floatval($balance['locked']);
                $valueInUSDT = 0;
                
                try {
                    $price = $this->getAssetPrice($balance['asset']);
                    $valueInUSDT = $amount * $price;
                } catch (Exception $e) {
                    Log::warning("Не вдалося отримати ціну для {$balance['asset']}: {$e->getMessage()}");
                }

                return [
                    'asset' => $balance['asset'],
                    'amount' => $amount,
                    'valueInUSDT' => $valueInUSDT
                ];
            }, $balances);
        }
        return $this->spotBalances;
    }

    public function getEarnPositions(): array
    {
        if ($this->earnPositions === null) {
            $accountInfo = $this->getAccountInfo();
            $earnBalances = array_filter($accountInfo['balances'], function($balance) {
                return str_starts_with($balance['asset'], 'LD') && 
                       (floatval($balance['free']) > 0 || floatval($balance['locked']) > 0);
            });

            $this->earnPositions = array_map(function($balance) {
                $amount = floatval($balance['free']) + floatval($balance['locked']);
                $valueInUSDT = 0;
                
                try {
                    $price = $this->getAssetPrice(substr($balance['asset'], 2));
                    $valueInUSDT = $amount * $price;
                } catch (Exception $e) {
                    Log::warning("Не вдалося отримати ціну для {$balance['asset']}: {$e->getMessage()}");
                }

                return [
                    'asset' => substr($balance['asset'], 2),
                    'totalAmount' => $amount,
                    'valueInUSDT' => $valueInUSDT,
                    'apy' => 0
                ];
            }, $earnBalances);
        }
        return $this->earnPositions;
    }

    public function getTotalPortfolioValue(): float
    {
        $totalValue = 0;

        foreach ($this->getSpotBalances() as $balance) {
            try {
                $price = $this->getAssetPrice($balance['asset']);
                $amount = floatval($balance['free']) + floatval($balance['locked']);
                $totalValue += $amount * $price;
            } catch (Exception $e) {
                Log::warning("Не вдалося отримати ціну для {$balance['asset']}: {$e->getMessage()}");
            }
        }

        foreach ($this->getEarnPositions() as $position) {
            try {
                $price = $this->getAssetPrice($position['asset']);
                $totalValue += $position['totalAmount'] * $price;
            } catch (Exception $e) {
                Log::warning("Не вдалося отримати ціну для {$position['asset']}: {$e->getMessage()}");
            }
        }

        return $totalValue;
    }
} 