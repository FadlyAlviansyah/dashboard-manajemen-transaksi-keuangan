<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard Manajemen Transaksi & Customer') </title>
    
    <!-- Google Fonts: Plus Jakarta Sans & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Gojek & Duolingo Custom Design System -->
    <style>
        :root {
            /* Palette: Gojek Green & Duolingo Fresh */
            --primary: #00AA13;
            --primary-dark: #00880D;
            --primary-light: #E8F8EE;
            --duo-green: #58CC02;
            --duo-green-dark: #46A302;
            --duo-yellow: #FFC727;
            --duo-yellow-dark: #E5B21D;
            --duo-blue: #1CB0F6;
            --duo-blue-dark: #1899D6;
            --duo-purple: #AF52DE;
            --duo-purple-dark: #9131C2;
            --duo-red: #FF4B4B;
            --duo-red-dark: #D33131;
            
            --bg-body: #F4F7F6;
            --bg-card: #FFFFFF;
            --text-main: #1E293B;
            --text-muted: #64748B;
            --border-color: #E2E8F0;
            --border-card: #E5E9EB;
            
            --radius-sm: 10px;
            --radius-md: 16px;
            --radius-lg: 24px;
            --radius-full: 9999px;

            /* Duolingo Chunky 3D Shadow effects */
            --shadow-3d-green: 0 4px 0 var(--primary-dark);
            --shadow-3d-duo: 0 4px 0 var(--duo-green-dark);
            --shadow-3d-yellow: 0 4px 0 var(--duo-yellow-dark);
            --shadow-3d-blue: 0 4px 0 var(--duo-blue-dark);
            --shadow-3d-gray: 0 4px 0 #CBD5E1;
            --shadow-card: 0 6px 0 #E2E8F0;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            line-height: 1.5;
        }

        h1, h2, h3, h4, .font-heading {
            font-family: 'Outfit', 'Plus Jakarta Sans', sans-serif;
            font-weight: 700;
        }

        /* Top Navbar */
        .navbar {
            background: #FFFFFF;
            border-bottom: 3px solid #E2E8F0;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        }

        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--text-main);
        }

        .brand-badge {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--duo-green), var(--primary));
            border-bottom: 3px solid var(--primary-dark);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 22px;
            box-shadow: 0 3px 0 var(--primary-dark);
        }

        .brand-title {
            font-size: 1.25rem;
            font-weight: 800;
            line-height: 1.2;
            color: #0F172A;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .brand-title span {
            color: var(--primary);
        }

        .brand-subtitle {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 10px;
            list-style: none;
        }

        .nav-item-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: var(--radius-md);
            font-size: 0.9rem;
            font-weight: 700;
            text-decoration: none;
            color: var(--text-muted);
            border: 2px solid transparent;
            transition: all 0.15s ease;
        }

        .nav-item-link:hover {
            color: var(--primary);
            background: var(--primary-light);
        }

        .nav-item-link.active {
            color: var(--primary);
            background: var(--primary-light);
            border: 2px solid #B8E8C7;
            border-bottom: 3px solid var(--primary);
        }

        .csv-pill-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #FEF3C7;
            color: #92400E;
            border: 2px solid #FDE68A;
            border-bottom: 3px solid #F59E0B;
            padding: 6px 14px;
            border-radius: var(--radius-full);
            font-size: 0.78rem;
            font-weight: 800;
        }

        .csv-dot {
            width: 8px;
            height: 8px;
            background: #10B981;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.25);
            animation: pulse-dot 2s infinite;
        }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.6; transform: scale(1.2); }
        }

        /* Main Content Container */
        .main-container {
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
            padding: 28px 24px 60px 24px;
            flex: 1;
        }

        /* 3D Duolingo / Gojek Buttons */
        .btn-3d {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.92rem;
            font-weight: 800;
            padding: 10px 20px;
            border-radius: var(--radius-md);
            text-decoration: none;
            cursor: pointer;
            border: none;
            outline: none;
            transition: all 0.1s ease;
            user-select: none;
        }

        .btn-primary {
            background-color: var(--primary);
            color: #FFFFFF;
            border-bottom: 4px solid var(--primary-dark);
            box-shadow: var(--shadow-3d-green);
        }

        .btn-primary:hover {
            background-color: #00BA15;
        }

        .btn-primary:active {
            transform: translateY(2px);
            border-bottom-width: 2px;
            box-shadow: 0 2px 0 var(--primary-dark);
        }

        .btn-secondary {
            background-color: #FFFFFF;
            color: #334155;
            border: 2px solid #E2E8F0;
            border-bottom: 4px solid #CBD5E1;
            box-shadow: var(--shadow-3d-gray);
        }

        .btn-secondary:hover {
            background-color: #F8FAFC;
            color: #0F172A;
        }

        .btn-secondary:active {
            transform: translateY(2px);
            border-bottom-width: 2px;
        }

        .btn-danger {
            background-color: var(--duo-red);
            color: #FFFFFF;
            border-bottom: 4px solid var(--duo-red-dark);
        }

        .btn-danger:hover {
            background-color: #FF5A5A;
        }

        .btn-danger:active {
            transform: translateY(2px);
            border-bottom-width: 2px;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.8rem;
            border-radius: 10px;
            border-bottom-width: 3px !important;
        }

        /* Duolingo Style 3D Cards */
        .duo-card {
            background: var(--bg-card);
            border: 2.5px solid var(--border-card);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-card);
            padding: 24px;
            position: relative;
            transition: all 0.2s ease;
        }

        /* Segment Badges */
        .badge-segment {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 0.75rem;
            font-weight: 800;
            padding: 4px 10px;
            border-radius: var(--radius-full);
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .badge-retail {
            background: #E8F8EE;
            color: #00880D;
            border: 1.5px solid #A7F3D0;
        }

        .badge-sme {
            background: #E0F2FE;
            color: #0369A1;
            border: 1.5px solid #BAE6FD;
        }

        .badge-premium {
            background: #FEF3C7;
            color: #B45309;
            border: 1.5px solid #FDE68A;
        }

        .badge-corporate {
            background: #EEF2FF;
            color: #4338CA;
            border: 1.5px solid #C7D2FE;
        }

        .badge-wealth {
            background: #F3E8FF;
            color: #7E22CE;
            border: 1.5px solid #E9D5FF;
        }

        .badge-gender-male {
            background: #EFF6FF;
            color: #1D4ED8;
            border: 1px solid #BFDBFE;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .badge-gender-female {
            background: #FDF2F8;
            color: #BE185D;
            border: 1px solid #FBCFE8;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        /* Alert Toast */
        .toast-banner {
            border-radius: var(--radius-md);
            padding: 14px 20px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-weight: 700;
            font-size: 0.95rem;
            border: 2px solid transparent;
            box-shadow: 0 4px 0 rgba(0, 0, 0, 0.05);
            animation: slideDown 0.3s ease;
        }

        .toast-success {
            background: #ECFDF5;
            color: #065F46;
            border-color: #6EE7B7;
            border-bottom: 4px solid #10B981;
        }

        .toast-error {
            background: #FEF2F2;
            color: #991B1B;
            border-color: #FCA5A5;
            border-bottom: 4px solid #EF4444;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Footer */
        .footer {
            background: #FFFFFF;
            border-top: 2px solid #E2E8F0;
            padding: 24px 0;
            text-align: center;
            font-size: 0.85rem;
            color: var(--text-muted);
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .nav-container {
                flex-direction: column;
                align-items: flex-start;
            }
            .nav-links {
                flex-wrap: wrap;
                width: 100%;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Top Navbar -->
    <header class="navbar">
        <div class="nav-container">
            <a href="{{ route('dashboard.table') }}" class="brand-logo">
                <div class="brand-badge">
                    <i data-lucide="wallet"></i>
                </div>
                <div>
                    <div class="brand-title">Fin<span>Track</span> </div>
                    <div class="brand-subtitle">Dashboard Manajemen Transaksi & Customer</div>
                </div>
            </a>

            <nav>
                <ul class="nav-links">
                    <li>
                        <a href="{{ route('dashboard.table') }}" class="nav-item-link {{ request()->routeIs('dashboard.table') ? 'active' : '' }}">
                            <i data-lucide="layout-dashboard" style="width: 18px; height: 18px;"></i>
                            <span>Dashboard & Tabel</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('customers.create') }}" class="nav-item-link {{ request()->routeIs('customers.create') ? 'active' : '' }}">
                            <i data-lucide="user-plus" style="width: 18px; height: 18px;"></i>
                            <span>+ Tambah Customer</span>
                        </a>
                    </li>
                    <li>
                        <div class="csv-pill-badge" title="Data aktif dibaca dan ditulis langsung ke storage/app/data/customers.csv">
                            <span class="csv-dot"></span>
                            <span>CSV Storage: Aktif</span>
                        </div>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Main Container -->
    <main class="main-container">
        <!-- Toast Alerts -->
        @if(session('success'))
            <div class="toast-banner toast-success">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i data-lucide="check-circle-2" style="width: 22px; height: 22px;"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" style="background: none; border: none; cursor: pointer; color: inherit; font-size: 1.2rem;">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="toast-banner toast-error">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i data-lucide="alert-triangle" style="width: 22px; height: 22px;"></i>
                    <span>{{ session('error') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" style="background: none; border: none; cursor: pointer; color: inherit; font-size: 1.2rem;">&times;</button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div style="max-width: 1400px; margin: 0 auto; padding: 0 24px; display: flex; flex-direction: column; gap: 6px; align-items: center;">
            <p><strong>FinTrack Financial Dashboard</strong> &bull; Tugas Pemrograman Web Dashboard</p>
            <p style="font-size: 0.78rem; color: #94A3B8;">Modul CRUD Customer CSV &bull; <code>storage/app/data/customers.csv</code> &bull; Desain UI Referensi Gojek & Duolingo</p>
        </div>
    </footer>

    <!-- Initialize Lucide Icons -->
    <script>
        lucide.createIcons();
    </script>
    @stack('scripts')
</body>
</html>
