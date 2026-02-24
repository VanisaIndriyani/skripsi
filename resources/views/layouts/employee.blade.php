<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Employee - PT Putra Muara Sukses</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #D4AF37;
            --primary-hover: #b8962e;
            --dark-bg: #121212;
            --card-bg: #ffffff;
        }
        body {
            background-color: var(--dark-bg);
            font-family: 'Poppins', sans-serif;
            color: #f8f9fa;
            min-height: 100vh;
            padding-bottom: 70px; /* Space for bottom nav */
        }
        .text-gold { color: var(--primary-color) !important; }
        .bg-gold { background-color: var(--primary-color) !important; }
        .btn-gold {
            background-color: var(--primary-color);
            color: #000;
            border: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-gold:hover {
            background-color: var(--primary-hover);
            color: #000;
        }
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            background-color: var(--card-bg);
            color: #333;
            margin-bottom: 15px;
        }
        .navbar-top {
            background-color: #000;
            border-bottom: 1px solid #333;
            padding: 15px 0;
        }
        .bottom-nav {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: #000;
            border-top: 1px solid #333;
            display: flex;
            justify-content: space-around;
            padding: 10px 0;
            z-index: 1000;
        }
        .nav-item-mobile {
            text-align: center;
            color: #aaa;
            text-decoration: none;
            font-size: 0.8rem;
        }
        .nav-item-mobile i {
            display: block;
            font-size: 1.2rem;
            margin-bottom: 4px;
        }
        .nav-item-mobile.active {
            color: var(--primary-color);
        }
        .scan-btn-wrapper {
            position: relative;
            top: -25px;
        }
        .scan-btn-circle {
            width: 60px;
            height: 60px;
            background-color: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #000;
            font-size: 1.5rem;
            box-shadow: 0 0 10px var(--primary-color);
        }
    </style>
</head>
<body>

    <!-- Top Navbar -->
    <div class="navbar-top sticky-top">
        <div class="container text-center">
            <h5 class="m-0 text-gold fw-bold">PT PUTRA MUARA SUKSES</h5>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <!-- Bottom Navigation -->
    <div class="bottom-nav">
        <a href="{{ route('employee.dashboard') }}" class="nav-item-mobile {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            Home
        </a>
        <a href="{{ route('employee.history') }}" class="nav-item-mobile {{ request()->routeIs('employee.history') ? 'active' : '' }}">
            <i class="fas fa-history"></i>
            Riwayat
        </a>
        <form action="{{ route('logout') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="nav-item-mobile bg-transparent border-0 text-danger">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </button>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto dismiss alerts
        setTimeout(function() {
            var alertList = document.querySelectorAll('.alert');
            alertList.forEach(function (alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 3000);
    </script>
</body>
</html>
