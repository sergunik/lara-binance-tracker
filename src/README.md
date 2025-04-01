# Binance Portfolio Tracker

A Laravel-based application for tracking your Binance portfolio, including spot and earn positions.

## Requirements

- PHP 8.1 or higher
- Composer
- MySQL 5.7 or higher
- Binance API Key and Secret Key

## Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd src
```

2. Run Docker
```bash
docker-compose up -d
docker-compose exec php bash
```

3. Install PHP dependencies:
```bash
composer install
```

4. Copy the environment file:
```bash
cp .env.example .env
```

5. Generate application key:
```bash
php artisan key:generate
```

6. Configure your database in `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

7. Configure Binance API credentials in `.env`:
```
BINANCE_API_KEY=your_api_key
BINANCE_SECRET_KEY=your_secret_key
```

8. Run database migrations:
```bash
php artisan migrate
```

The application will be available at `http://localhost`

## Features

- Real-time portfolio tracking
- Spot balance monitoring
- Earn positions tracking
- Portfolio value calculation in USDT
- Asset allocation visualization
- Portfolio trend analysis

## Security

Make sure to:
- Keep your API keys secure
- Never commit `.env` file
- Use appropriate permissions for storage and bootstrap/cache directories
- Configure proper CORS and CSP headers in production

## License

This project is licensed under the MIT License.
