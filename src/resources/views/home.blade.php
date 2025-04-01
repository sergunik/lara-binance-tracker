<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Форма</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>Форма введення</h1>
            <p>Введіть текст для отримання відповіді від ChatGPT</p>
        </header>

        <main class="content">
            @if(session('message'))
                <div class="message">
                    {{ session('message') }}
                </div>
            @endif

            @if(session('error'))
                <div class="message error">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('submit') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="text">Введіть текст:</label>
                    <input type="text" id="text" name="text" required>
                </div>
                
                <div style="text-align: center;">
                    <button type="submit" class="button">Відправити</button>
                </div>
            </form>
        </main>
    </div>

    <script src="{{ asset('js/main.js') }}"></script>
</body>
</html> 