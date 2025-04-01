<?php

namespace App\Http\Controllers;

use App\Services\Interfaces\BinanceServiceInterface;
use Exception;

class PortfolioController extends Controller
{
    private BinanceServiceInterface $binanceService;

    public function __construct(BinanceServiceInterface $binanceService)
    {
        $this->binanceService = $binanceService;
    }

    public function index()
    {
        try {
            $data = [
                'spotBalances' => $this->binanceService->getSpotBalances(),
                'earnPositions' => $this->binanceService->getEarnPositions(),
                'totalValue' => $this->binanceService->getTotalPortfolioValue(),
            ];

            return view('portfolio.index', $data);
        } catch (Exception $e) {
            return view('portfolio.index', [
                'error' => $e->getMessage()
            ]);
        }
    }
}