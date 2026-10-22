<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connected to {{ $project->name ?? ucfirst($appSlug) }} · RozeHub</title>
    <link rel="stylesheet" href="{{ asset('css/rozehub.css') }}">
    <style>
        body {
            background: #090d16;
            color: #f1f5f9;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            margin: 0;
        }
        .success-card {
            width: 100%;
            max-width: 460px;
            background: #111827;
            border: 1px solid #1f2937;
            border-radius: 16px;
            padding: 40px 32px;
            text-align: center;
            box-shadow: 0 20px 35px -5px rgba(0, 0, 0, 0.6), 0 0 25px rgba(16, 185, 129, 0.15);
        }
        .success-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: rgba(16, 185, 129, 0.15);
            border: 2px solid #10b981;
            color: #10b981;
            font-size: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        h1 {
            font-size: 22px;
            font-weight: 700;
            color: #ffffff;
            margin: 0 0 10px;
        }
        p {
            font-size: 14px;
            color: #94a3b8;
            line-height: 1.5;
            margin: 0 0 28px;
        }
        .btn-action {
            display: inline-block;
            width: 100%;
            box-sizing: border-box;
            padding: 12px 20px;
            background: #10b981;
            color: white;
            font-size: 14px;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            margin-bottom: 12px;
            transition: background 0.15s ease;
        }
        .btn-action:hover {
            background: #059669;
        }
        .btn-secondary {
            display: inline-block;
            width: 100%;
            box-sizing: border-box;
            padding: 10px 18px;
            background: #1e293b;
            color: #cbd5e1;
            font-size: 13px;
            border: 1px solid #334155;
            border-radius: 8px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            margin-bottom: 20px;
        }
        .btn-secondary:hover {
            background: #334155;
            color: white;
        }
        .code-box {
            background: #0f172a;
            border: 1px solid #1e293b;
            border-radius: 8px;
            padding: 10px;
            font-family: monospace;
            font-size: 11px;
            color: #94a3b8;
            word-break: break-all;
            margin-bottom: 16px;
            user-select: all;
        }
        .footer-note {
            font-size: 12px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="success-card">
        <div class="success-icon">✓</div>
        <h1>Connected to {{ $project->name ?? ucfirst($appSlug) }}!</h1>
        <p>Your RozeHub account has been verified. Redirecting you back to the desktop application...</p>

        @if($deepLinkUrl)
            <a href="{{ $deepLinkUrl }}" class="btn-action" id="launch-app-btn">
                Open {{ $project->name ?? ucfirst($appSlug) }}
            </a>
        @endif

        <button type="button" class="btn-secondary" onclick="copyToken()">
            📋 Copy Authentication Code
        </button>

        <div class="code-box" id="token-display">{{ $token }}</div>

        <div class="footer-note">
            You can safely close this browser window at any time.<br>
            <a href="{{ route('account') }}" style="color: #38bdf8; text-decoration: none; margin-top: 8px; display: inline-block;">Go to RozeHub Account</a>
        </div>
    </div>

    <script>
        // Automatic Loopback Delivery (Pattern 1 - Localhost HTTP)
        @if($loopbackUrl)
            try {
                fetch(@json($loopbackUrl), { mode: 'no-cors' }).catch(() => {});
                window.location.replace(@json($loopbackUrl));
            } catch (e) {
                console.log("Loopback delivery attempt:", e);
            }
        @endif

        // Deep Link Launch (Pattern 2 - Custom Protocol)
        @if($deepLinkUrl)
            setTimeout(function() {
                try {
                    window.location.href = @json($deepLinkUrl);
                } catch (e) {}
            }, 500);
        @endif

        function copyToken() {
            const token = document.getElementById('token-display').innerText.trim();
            navigator.clipboard.writeText(token).then(() => {
                alert("Authentication code copied to clipboard!");
            });
        }
    </script>
</body>
</html>
