<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solo</title>

    <!-- Open Graph tags for WhatsApp / iMessage / Social card preview -->
    <meta property="og:title" content="Check this out on Solo" />
    <meta property="og:description" content="Open in the Solo app" />
    <meta property="og:type" content="website" />

    <!-- Attempt automatic redirect into the app -->
    <script>
        window.location.href = "{{ $schemeUrl }}";
    </script>

    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            background-color: #0e0e0e;
            color: #ffffff;
            text-align: center;
            padding: 20px;
        }
        .card {
            max-width: 400px;
            padding: 30px;
            background: #1a1a1a;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        h1 { margin: 0 0 10px; font-size: 24px; }
        p { color: #888; font-size: 14px; margin-bottom: 25px; }
        .btn {
            display: block;
            width: 100%;
            padding: 14px;
            background: #ff6600;
            color: #fff;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            margin-bottom: 12px;
            box-sizing: border-box;
        }
        .btn-secondary {
            background: #2a2a2a;
            color: #bbb;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>Solo</h1>
        <p>Open this content in the Solo app.</p>
        <a href="{{ $schemeUrl }}" class="btn">Open in Solo App</a>
        <a href="https://play.google.com/store/apps/details?id=com.services.solo" class="btn btn-secondary">Get on Google Play</a>
        <a href="https://apps.apple.com" class="btn btn-secondary">Get on App Store</a>
    </div>
</body>
</html>