{{--
    Demo Login Picker — one-click login for all demo accounts.
    Accessible at /demo-login (dev mode only).
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Demo Login — Necoyoad</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #1a365d;
            color: #2d3748;
            min-height: 100vh;
            padding: 2rem;
        }
        .container { max-width: 1000px; margin: 0 auto; }
        h1 { color: white; font-size: 2rem; margin-bottom: 0.5rem; }
        .subtitle { color: #a0aec0; margin-bottom: 2rem; }
        .section {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }
        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1a365d;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .badge {
            background: #4299e1;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-customer { background: #48bb78; }
        .accounts {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 0.75rem;
        }
        .account {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            text-decoration: none;
            color: #2d3748;
            transition: all 0.2s;
            cursor: pointer;
            background: white;
        }
        .account:hover {
            border-color: #4299e1;
            background: #ebf8ff;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #4299e1;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            flex-shrink: 0;
            font-size: 0.875rem;
        }
        .avatar-customer { background: #48bb78; }
        .account-info { flex: 1; min-width: 0; }
        .account-name { font-weight: 600; font-size: 0.875rem; }
        .account-email { font-size: 0.75rem; color: #718096; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .account-store { font-size: 0.625rem; color: #a0aec0; margin-top: 2px; }
        .login-icon { color: #4299e1; font-size: 1.25rem; }
        .password-hint {
            margin-top: 1rem;
            padding: 0.75rem;
            background: #fef5e7;
            border-radius: 8px;
            font-size: 0.8125rem;
            color: #744210;
        }
        .links { margin-top: 1.5rem; text-align: center; }
        .links a { color: #a0aec0; margin: 0 0.75rem; text-decoration: none; font-size: 0.875rem; }
        .links a:hover { color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Necoyoad Demo Login</h1>
        <p class="subtitle">Click any account to log in instantly. All passwords: <code style="background:rgba(255,255,255,0.15);padding:2px 6px;border-radius:4px;color:#68d391;">password</code></p>

        {{-- Admin Accounts --}}
        <div class="section">
            <div class="section-title">
                <span class="badge">ADMIN</span>
                Filament Admin Panel
            </div>
            <div class="accounts">
                @foreach ($admins as $admin)
                    <a href="/demo-login/admin/{{ $admin->id }}" class="account">
                        <div class="avatar">{{ strtoupper(substr($admin->firstname, 0, 1) . substr($admin->lastname, 0, 1)) }}</div>
                        <div class="account-info">
                            <div class="account-name">{{ $admin->firstname }} {{ $admin->lastname }}</div>
                            <div class="account-email">{{ $admin->email }}</div>
                        </div>
                        <span class="login-icon">→</span>
                    </a>
                @endforeach
            </div>
            <div class="password-hint">
                💡 Click an account to auto-login to <code>/admin</code> (Filament dashboard)
            </div>
        </div>

        {{-- Customer Accounts --}}
        <div class="section">
            <div class="section-title">
                <span class="badge badge-customer">CUSTOMER</span>
                Storefront Customer Accounts
            </div>
            <div class="accounts">
                @foreach ($customers as $customer)
                    @php $store = $stores->where('id', $customer->store_id)->first(); @endphp
                    <a href="/demo-login/customer/{{ $customer->id }}" class="account">
                        <div class="avatar avatar-customer">{{ strtoupper(substr($customer->firstname, 0, 1) . substr($customer->lastname, 0, 1)) }}</div>
                        <div class="account-info">
                            <div class="account-name">{{ $customer->firstname }} {{ $customer->lastname }}</div>
                            <div class="account-email">{{ $customer->email }}</div>
                            @if ($store)
                                <div class="account-store">{{ $store->name }} ({{ $store->folder }})</div>
                            @endif
                        </div>
                        <span class="login-icon">→</span>
                    </a>
                @endforeach
            </div>
            <div class="password-hint">
                💡 Click an account to auto-login to the storefront as that customer
            </div>
        </div>

        <div class="links">
            <a href="/">← Back to Storefront</a>
            <a href="/admin">Admin Panel</a>
            <a href="/login">Manual Login</a>
        </div>
    </div>
</body>
</html>
