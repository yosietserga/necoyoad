{{--
    Storefront error page — rendered when a StorefrontException is thrown.
    Shows a user-friendly error message instead of a generic 500 page.
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Error {{ $status }} — {{ config('app.name') }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f8f9fa;
            color: #2d3748;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .error-container {
            max-width: 500px;
            text-align: center;
            background: white;
            padding: 48px 32px;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
        }
        .error-code {
            font-size: 72px;
            font-weight: 700;
            color: #e53e3e;
            line-height: 1;
            margin-bottom: 16px;
        }
        .error-type {
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #718096;
            margin-bottom: 24px;
        }
        .error-message {
            font-size: 18px;
            color: #4a5568;
            margin-bottom: 32px;
            line-height: 1.5;
        }
        .back-link {
            display: inline-block;
            padding: 12px 24px;
            background: #1a365d;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: background 0.2s;
        }
        .back-link:hover { background: #2c5282; }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">{{ $status }}</div>
        <div class="error-type">{{ $type }}</div>
        <div class="error-message">{{ $message }}</div>
        <a href="/" class="back-link">Back to Home</a>
    </div>
</body>
</html>
