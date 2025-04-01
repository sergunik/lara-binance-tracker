<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crypto Portfolio</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <div class="container">
        <h1>Crypto Portfolio</h1>

        @if(isset($error))
            <div class="message error">
                {{ $error }}
            </div>
        @else
            <div class="summary-cards">
                <div class="summary-card">
                    <h2>Total Value</h2>
                    <div class="value">${{ number_format($totalValue, 2) }}</div>
                </div>
                
                <div class="summary-card">
                    <h2>Spot Portfolio</h2>
                    <div class="value">${{ number_format(collect($spotBalances)->sum('valueInUSDT'), 2) }}</div>
                </div>
                
                <div class="summary-card">
                    <h2>Earn Portfolio</h2>
                    <div class="value">${{ number_format(collect($earnPositions)->sum('valueInUSDT'), 2) }}</div>
                </div>
            </div>

            <div class="portfolio-details">
                <div class="chart-section">
                    <div class="allocation-chart">
                        <div class="chart-placeholder">
                            <p>Portfolio allocation chart placeholder</p>
                        </div>
                    </div>
                    <div class="portfolio-trend">
                        <div class="chart-placeholder">
                            <p>Portfolio trend chart placeholder</p>
                        </div>
                    </div>
                </div>

                <div class="holdings-section">
                    <h2>Holdings</h2>
                    <table class="holdings-table">
                        <thead>
                            <tr>
                                <th>Asset</th>
                                <th>Amount</th>
                                <th>Allocation</th>
                                <th>Price</th>
                                <th>Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($spotBalances as $balance)
                                <tr>
                                    <td>
                                        <div class="asset-cell">
                                            <span class="asset-icon">{{ strtoupper(substr($balance['asset'], 0, 1)) }}</span>
                                            <span class="asset-name">{{ $balance['asset'] }}</span>
                                        </div>
                                    </td>
                                    <td>{{ number_format($balance['amount'], 8) }}</td>
                                    <td>{{ number_format(($balance['valueInUSDT'] / $totalValue) * 100, 1) }}%</td>
                                    <td>${{ number_format($balance['valueInUSDT'] / $balance['amount'], 2) }}</td>
                                    <td>${{ number_format($balance['valueInUSDT'], 2) }}</td>
                                </tr>
                            @endforeach
                            @foreach($earnPositions as $position)
                                <tr>
                                    <td>
                                        <div class="asset-cell">
                                            <span class="asset-icon">{{ strtoupper(substr($position['asset'], 0, 1)) }}</span>
                                            <span class="asset-name">{{ $position['asset'] }} (Earn)</span>
                                        </div>
                                    </td>
                                    <td>{{ number_format($position['totalAmount'], 8) }}</td>
                                    <td>{{ number_format(($position['valueInUSDT'] / $totalValue) * 100, 1) }}%</td>
                                    <td>${{ number_format($position['valueInUSDT'] / $position['totalAmount'], 2) }}</td>
                                    <td>${{ number_format($position['valueInUSDT'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</body>
</html> 