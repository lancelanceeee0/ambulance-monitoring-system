<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Ambulance Dispatch')</title>
    <style>
        /* ---- Shared design tokens (same look as the Log In / Register pages) ---- */
        :root {
            --pink: #f6b0be;
            --blue: #4078a8;
            --link: #539dc4;
            --grad-panel: linear-gradient(135deg, #f6b0be, #4078a8);
            --grad-btn: linear-gradient(90deg, #bd466e, #6a96d8);
            --shadow: 0 10px 30px rgba(0,0,0,.15);
            --radius: 20px;
        }

        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; background: #f7f7f2; font-family: 'Segoe UI', Arial, sans-serif; color: #444; }

        .wrap { width: 90%; max-width: 1050px; margin: 30px auto 50px; }

        /* ---- Navigation ---- */
        nav { background: var(--grad-panel); border-radius: var(--radius); box-shadow: var(--shadow); padding: 14px 24px; display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 26px; }
        nav .brand { color: #fff; font-weight: bold; font-size: 18px; margin-right: auto; }
        nav a, nav button.link { color: #fff; text-decoration: none; font-size: 15px; font-family: inherit; background: none; border: 0; cursor: pointer; padding: 9px 18px; border-radius: 30px; margin: 0; transition: background .15s; }
        nav a:hover, nav button.link:hover { background: rgba(255,255,255,.22); opacity: 1; }
        nav a.active { background: rgba(255,255,255,.92); color: #bd466e; font-weight: bold; }
        nav form { margin: 0; display: block; }
        nav button.link { border: 1px solid rgba(255,255,255,.7); }

        /* ---- Page card: gradient banner on top + white content below ---- */
        .split { display: flex; flex-direction: column; border-radius: var(--radius); overflow: hidden; box-shadow: var(--shadow); background: #fff; margin-bottom: 26px; }
        .split .panel { width: 100%; padding: 26px 36px; background: var(--grad-panel); color: #fff; }
        .split .panel h1 { font-size: 30px; margin: 0 0 4px; line-height: 1.2; }
        .split .panel p { font-size: 16px; line-height: 1.5; margin: 0; opacity: .95; }
        .split .content { width: 100%; padding: 32px 36px 38px; }

        /* ---- Plain card (tables) with a gradient header ---- */
        .card { background: #fff; border-radius: var(--radius); box-shadow: var(--shadow); overflow: hidden; margin-bottom: 26px; }
        .card-head { background: var(--grad-panel); color: #fff; padding: 28px 36px; }
        .card-head h2 { margin: 0 0 4px; font-size: 30px; }
        .card-head p { margin: 0; font-size: 16px; opacity: .95; }
        .card-body { padding: 26px 36px 32px; }

        /* ---- Forms (pill inputs like login) ---- */
        form.stack { display: flex; flex-direction: column; gap: 6px; }
        label { display: block; font-weight: 600; font-size: 14px; color: #666; margin: 10px 0 0 18px; }
        input[type=text], input[type=password], input[type=email], input[type=file] { width: 100%; padding: 14px 18px; border: 1px solid #ddd; border-radius: 30px; font-size: 15px; outline: none; font-family: inherit; background: #fff; }
        input:focus { border-color: #bd467b; }
        input[type=file] { padding: 9px 12px; color: #666; }
        input[type=file]::file-selector-button { margin-right: 12px; padding: 8px 18px; border: 1px solid #bd467b; border-radius: 30px; background: transparent; color: #bd466e; font-weight: 600; cursor: pointer; font-family: inherit; transition: background .15s; }
        input[type=file]::file-selector-button:hover { background: #fcf5f7; }

        /* ---- Buttons ---- */
        .btn { margin-top: 10px; padding: 14px 28px; border: none; border-radius: 30px; background: var(--grad-btn); color: #fff; font-size: 16px; font-weight: bold; cursor: pointer; text-decoration: none; display: inline-block; font-family: inherit; text-align: center; }
        .btn:hover { opacity: .9; }
        .btn.gray { background: #fff; color: #bd466e; border: 1px solid #ddd; }
        .btn.gray:hover { border-color: #bd467b; opacity: 1; }
        .actions { margin-top: 14px; display: flex; gap: 12px; flex-wrap: wrap; }
        .actions .btn { margin-top: 0; }

        /* ---- Messages ---- */
        .muted { color: #666; font-size: 14px; margin: 0 0 18px; }
        .errors { color: red; font-size: 14px; margin-bottom: 15px; }
        .errors ul { padding-left: 20px; margin: 0; }
        .success { color: #166534; background: #dcfce7; padding: 12px 18px; border-radius: 30px; margin-bottom: 16px; font-size: 14px; }

        /* ---- Tables ---- */
        .table-scroll { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #fcf5f7; color: #bd466e; text-align: left; padding: 13px 14px; font-size: 14px; white-space: nowrap; border-bottom: 2px solid var(--pink); }
        th:first-child { border-radius: 14px 0 0 0; }
        th:last-child { border-radius: 0 14px 0 0; }
        td { padding: 13px 14px; border-bottom: 1px solid #eee; font-size: 14px; }
        tbody tr:hover td { background: #fcf5f7; }
        .badge { display: inline-block; padding: 4px 14px; border-radius: 30px; background: #fde8ed; color: #bd466e; font-weight: bold; font-size: 13px; white-space: nowrap; }

        @media (max-width: 750px) {
            .split .panel { padding: 22px 24px; }
            .split .content { padding: 24px 22px 30px; }
            .card-head { padding: 24px 24px; }
            .card-body { padding: 20px 20px 26px; }
            nav { justify-content: center; }
            nav .brand { width: 100%; text-align: center; margin-bottom: 4px; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="wrap">
        <nav>
            <span class="brand">Ambulance Dispatch System</span>
            <a href="/dispatch" class="{{ request()->is('dispatch') ? 'active' : '' }}">Dispatch</a>
            <a href="/vehicle-registration" class="{{ request()->is('vehicle-registration') ? 'active' : '' }}">Vehicle Registration</a>
            <a href="/dispatch-log" class="{{ request()->is('dispatch-log') ? 'active' : '' }}">Dispatch Log</a>
            <a href="/ambulance-arrival" class="{{ request()->is('ambulance-arrival') ? 'active' : '' }}">Ambulance Arrival</a>
            <form action="/logout" method="POST">
                @csrf
                <button type="submit" class="link">Logout</button>
            </form>
        </nav>

        @yield('content')
    </div>

    @stack('scripts')
</body>
</html>
