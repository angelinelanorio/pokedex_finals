<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Pokédex')</title>
    
    <!-- ✅ CRITICAL: ADD TAILWIND CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Your existing CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- PAGE TRANSITION STYLES -->
    <style>
        /* RESET MARGINS AND PADDING */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        /* BODY WITH PROPER SPACING */
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding-top: 60px !important; /* ADDED IMPORTANT FLAG */
            display: flex;
            flex-direction: column;
        }
        
        /* FIXED NAVBAR */
        .navbar {
            background: linear-gradient(135deg, #FF0000 0%, #CC0000 100%);
            padding: 0;
            color: white;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            z-index: 1000 !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            height: 60px !important; /* FIXED HEIGHT */
        }
        
        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 100%;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .logo h1 {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
        }
        
        .logo i {
            font-size: 22px;
        }
        
        .nav-menu {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .nav-link {
            color: white;
            text-decoration: none;
            padding: 6px 10px;
            border-radius: 4px;
            transition: background 0.3s;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap;
        }
        
        .nav-link:hover, .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
        }
        
        .logout-form {
            margin: 0;
            display: inline;
        }
        
        .logout {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 13px;
            color: white;
            font-family: inherit;
            padding: 6px 10px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .logout:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .user-avatar-placeholder {
            width: 32px;
            height: 32px;
            background: #FFCC00;
            border-radius: 50%;
            border: 2px solid white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }
        
        .user-name {
            font-size: 13px;
            font-weight: 500;
        }
        
        /* MAIN CONTENT WITH PROPER SPACING */
        main.container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 15px;
            width: 100%;
            flex: 1;
        }
        
        /* ALERTS */
        .alert {
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        /* PAGE TRANSITION */
        .page-transition {
            animation: fadeIn 0.3s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* SMOOTH SCROLLING */
        html {
            scroll-behavior: smooth;
        }
        
        /* LOADING OVERLAY */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .loading-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        
        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #FF0000;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* RESPONSIVE ADJUSTMENTS */
        @media (max-width: 768px) {
            body {
                padding-top: 55px !important;
            }
            
            .navbar {
                height: 55px !important;
            }
            
            .logo h1 {
                font-size: 18px;
            }
            
            .logo i {
                font-size: 20px;
            }
            
            .nav-menu {
                gap: 5px;
            }
            
            .nav-link {
                padding: 5px 8px;
                font-size: 12px;
            }
            
            .nav-link i {
                font-size: 12px;
            }
            
            .logout {
                font-size: 12px;
                padding: 5px 8px;
            }
            
            .logout i {
                font-size: 12px;
            }
            
            .user-name {
                display: none;
            }
            
            .user-avatar-placeholder {
                width: 28px;
                height: 28px;
                font-size: 12px;
            }
            
            main.container {
                padding: 10px;
            }
        }
        
        @media (max-width: 640px) {
            .logo h1 {
                font-size: 16px;
            }
            
            .nav-link span {
                display: none;
            }
            
            .nav-link {
                padding: 6px 8px;
            }
            
            .nav-link i {
                font-size: 14px;
                margin: 0;
            }
        }
        
        @media (max-width: 480px) {
            .logo h1 {
                font-size: 14px;
            }
            
            .nav-menu {
                gap: 3px;
            }
            
            .nav-link {
                padding: 5px 6px;
            }
        }
    </style>

    <!-- Custom styles from your profile page -->
    @yield('styles')
    
    <script>
        const APP_URL = '{{ url("/") }}';
        const API_URL = '{{ url("/api") }}';
        const CSRF_TOKEN = '{{ csrf_token() }}';
    </script>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">
                <i class="fas fa-dragon"></i>
                <h1>Pokédex</h1>
            </div>
            
            @if(session('logged_in'))
            <div class="nav-menu">
                <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span>Home</span>
                </a>
                <a href="{{ route('pokemon.index') }}" class="nav-link {{ request()->routeIs('pokemon.*') ? 'active' : '' }}">
                    <i class="fas fa-book"></i>
                    <span>Pokédex</span>
                </a>
                <a href="{{ route('team.index') }}" class="nav-link {{ request()->routeIs('team.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>My Team</span>
                </a>
                <a href="{{ route('trading.index') }}" class="nav-link {{ request()->routeIs('trading.*') ? 'active' : '' }}">
                    <i class="fas fa-exchange-alt"></i>
                    <span>Trading</span>
                </a>
                <a href="{{ route('profile.show') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                    <i class="fas fa-user"></i>
                    <span>Profile</span>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="logout-form">
                    @csrf
                    <button type="submit" class="logout">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
            
            <div class="user-info">
                <div class="user-avatar-placeholder">
                    <i class="fas fa-user"></i>
                </div>
                <span class="user-name">{{ session('trainer_name', session('trainer_username', 'Trainer')) }}</span>
            </div>
            @endif
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container">
        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif
        
        <div class="page-transition">
            @yield('content')
        </div>
    </main>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>
    
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    
    <script>
        // Show loading overlay
        function showLoading() {
            document.getElementById('loadingOverlay').classList.add('active');
        }
        
        // Hide loading overlay
        function hideLoading() {
            document.getElementById('loadingOverlay').classList.remove('active');
        }
        
        // Global event listener for form submissions
        document.addEventListener('DOMContentLoaded', function() {
            // Listen for form submissions
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (!this.classList.contains('no-loading')) {
                        showLoading();
                    }
                });
            });
            
            // Listen for link clicks that might load new content
            const links = document.querySelectorAll('a:not([href^="#"]):not([target="_blank"]):not([href^="http"])');
            links.forEach(link => {
                link.addEventListener('click', function(e) {
                    if (this.getAttribute('href') && !this.classList.contains('no-loading')) {
                        showLoading();
                    }
                });
            });
        });
        
        // Hide loading when page finishes loading
        window.addEventListener('load', function() {
            hideLoading();
        });
    </script>
    
    @yield('scripts')
</body>
</html>