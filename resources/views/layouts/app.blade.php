<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Pencatatan Keuangan' }} - Dompet Pintar</title>
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- FontAwesome Free Icons (CDN) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --primary: #059669;
            --primary-hover: #047857;
            --primary-light: #ecfdf5;
            --primary-dark: #064e3b;
            
            --income: #10b981;
            --income-bg: #d1fae5;
            --income-text: #065f46;
            
            --expense: #ef4444;
            --expense-bg: #fee2e2;
            --expense-text: #991b1b;
            
            --bg-body: #f8fafc;
            --card-bg: #ffffff;
            --border-color: #e2e8f0;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --nav-bg: #ffffff;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.08), 0 2px 4px -2px rgb(0 0 0 / 0.05);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.08), 0 4px 6px -4px rgb(0 0 0 / 0.04);
            --radius-md: 10px;
            --radius-lg: 16px;
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
            line-height: 1.5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Navbar */
        .navbar {
            background-color: var(--nav-bg);
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: var(--shadow-sm);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 70px;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: var(--text-main);
            font-weight: 800;
            font-size: 1.25rem;
        }

        .brand-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #10b981, #047857);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--radius-md);
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            list-style: none;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1rem;
            border-radius: 8px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.925rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .nav-link:hover {
            color: var(--primary);
            background-color: var(--primary-light);
        }

        .nav-link.active {
            color: var(--primary);
            background-color: var(--primary-light);
            font-weight: 700;
        }

        .nav-user {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .badge-role {
            font-size: 0.75rem;
            padding: 0.2rem 0.6rem;
            border-radius: 9999px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .badge-role.admin {
            background-color: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .badge-role.user {
            background-color: #dbeafe;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
        }

        /* Main Container */
        .main-content {
            flex: 1;
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
            padding: 2rem 1.25rem 4rem 1.25rem;
        }

        /* Alerts */
        .alert {
            padding: 1rem 1.25rem;
            border-radius: var(--radius-md);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            font-size: 0.95rem;
            line-height: 1.4;
        }

        .alert-success {
            background-color: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert-danger {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert-warning {
            background-color: #fffbeb;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        /* Cards */
        .card {
            background: var(--card-bg);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            padding: 1.75rem;
            margin-bottom: 1.75rem;
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .card-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.65rem 1.25rem;
            font-size: 0.925rem;
            font-weight: 600;
            border-radius: 8px;
            text-decoration: none;
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
            box-shadow: 0 2px 4px rgba(5, 150, 105, 0.2);
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
        }

        .btn-secondary {
            background-color: #f1f5f9;
            color: #334155;
            border-color: #cbd5e1;
        }

        .btn-secondary:hover {
            background-color: #e2e8f0;
            color: #0f172a;
        }

        .btn-danger {
            background-color: var(--expense);
            color: white;
        }

        .btn-danger:hover {
            background-color: #dc2626;
        }

        .btn-success {
            background-color: #10b981;
            color: white;
        }

        .btn-sm {
            padding: 0.4rem 0.75rem;
            font-size: 0.825rem;
            border-radius: 6px;
        }

        /* Form Inputs */
        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.4rem;
            font-size: 0.9rem;
            font-weight: 600;
            color: #334155;
        }

        .form-control {
            width: 100%;
            padding: 0.7rem 0.95rem;
            font-size: 0.95rem;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background-color: #ffffff;
            color: var(--text-main);
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
            font-family: inherit;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
        }

        .form-error {
            color: #dc2626;
            font-size: 0.825rem;
            margin-top: 0.35rem;
        }

        /* Tables */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            text-align: left;
        }

        .table th {
            background-color: #f8fafc;
            color: #64748b;
            font-weight: 700;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0.85rem 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .table td {
            padding: 0.95rem 1rem;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.925rem;
            vertical-align: middle;
        }

        .table tr:last-child td {
            border-bottom: none;
        }

        .table tr:hover td {
            background-color: #f8fafc;
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.25rem 0.65rem;
            font-size: 0.775rem;
            font-weight: 700;
            border-radius: 9999px;
        }

        .badge-income {
            background-color: var(--income-bg);
            color: var(--income-text);
        }

        .badge-expense {
            background-color: var(--expense-bg);
            color: var(--expense-text);
        }

        .badge-active {
            background-color: #ecfdf5;
            color: #065f46;
        }

        .badge-inactive {
            background-color: #fef2f2;
            color: #991b1b;
        }

        .badge-system {
            background-color: #f1f5f9;
            color: #475569;
        }

        /* Footer */
        .footer {
            margin-top: auto;
            background-color: white;
            border-top: 1px solid var(--border-color);
            padding: 1.5rem 1.25rem;
            text-align: center;
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .nav-container {
                flex-direction: column;
                height: auto;
                padding: 1rem;
                gap: 0.75rem;
            }
            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
                gap: 0.25rem;
            }
            .nav-user {
                margin-top: 0.25rem;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="{{ Auth::check() ? (Auth::user()->isAdmin() ? route('admin.users.index') : route('dashboard')) : route('login') }}" class="brand-logo">
                <div class="brand-icon">
                    <i class="fa-solid fa-wallet"></i>
                </div>
                <span>DompetKeuangan</span>
            </a>

            @auth
                <ul class="nav-links">
                    @if(Auth::user()->role === 'user')
                        <li>
                            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <i class="fa-solid fa-chart-pie"></i> Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('transactions.index') }}" class="nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-clock-rotate-left"></i> Catatan Transaksi
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('topup.index') }}" class="nav-link {{ request()->routeIs('topup.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-circle-plus"></i> Isi Saldo
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-tags"></i> Kategori
                            </a>
                        </li>
                    @elseif(Auth::user()->role === 'admin')
                        <li>
                            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-users-gear"></i> Kelola Pengguna
                            </a>
                        </li>
                    @endif
                    <li>
                        <a href="{{ route('profile.index') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-user-gear"></i> Profil
                        </a>
                    </li>
                </ul>

                <div class="nav-user">
                    <span class="badge-role {{ Auth::user()->role }}">
                        {{ Auth::user()->role === 'admin' ? 'Administrator' : 'User' }}
                    </span>
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-secondary btn-sm" onclick="return confirm('Apakah Anda yakin ingin keluar?')">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar
                        </button>
                    </form>
                </div>
            @else
                <div class="nav-links">
                    <a href="{{ route('login') }}" class="btn btn-secondary btn-sm">Masuk</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Daftar Akun</a>
                </div>
            @endauth
        </div>
    </nav>

    <!-- Main Content Area -->
    <main class="main-content">
        <!-- Flash Alerts -->
        @if(session('success'))
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check" style="font-size: 1.2rem; color: #10b981;"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                <i class="fa-solid fa-circle-exclamation" style="font-size: 1.2rem; color: #ef4444;"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <i class="fa-solid fa-triangle-exclamation" style="font-size: 1.2rem; color: #ef4444;"></i>
                <div>
                    <strong style="display: block; margin-bottom: 0.25rem;">Terdapat kesalahan pada input Anda:</strong>
                    <ul style="margin-left: 1.25rem; font-size: 0.9rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; {{ date('Y') }} <strong>Aplikasi Pencatatan Keuangan</strong>. Dibuat rapi, aman, dan mudah digunakan.</p>
    </footer>

    @stack('scripts')
</body>
</html>
