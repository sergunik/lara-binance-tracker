<?php

namespace App\Services\Interfaces;

interface BinanceServiceInterface
{
    public function getSpotBalances(): array;
    public function getEarnPositions(): array;
    public function getTotalPortfolioValue(): float;
    public function getAssetPrice(string $asset): float;
} 