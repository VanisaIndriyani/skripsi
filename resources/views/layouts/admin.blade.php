<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - PT Putra Muara Sukses</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root {
            --primary-color: #D4AF37;
            --primary-hover: #b8962e;
            --dark-bg: #121212;
            --sidebar-bg: #0a0a0a;
            --card-bg: #ffffff;
            --text-dark: #333333;
            --sidebar-width: 250px;
            --sidebar-collapsed-width: 80px;
        }
        body {
            background-color: var(--dark-bg);
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
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
            transform: translateY(-2px);
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            background-color: var(--card-bg);
            margin-bottom: 20px;
        }
        
        /* Sidebar Styles */
        .sidebar {
            background-color: var(--sidebar-bg);
            min-height: 100vh;
            border-right: 1px solid #333;
            width: var(--sidebar-width);
            transition: width 0.3s ease;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            overflow: hidden;
        }
        
        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }
        
        .sidebar .nav-link {
            color: #bbb;
            padding: 15px 25px;
            font-size: 0.95rem;
            border-left: 3px solid transparent;
            white-space: nowrap;
            display: flex;
            align-items: center;
            transition: all 0.2s ease;
        }
        
        .sidebar .nav-link i {
            width: 25px;
            font-size: 1.1rem;
            margin-right: 10px;
            text-align: center;
        }
        
        .sidebar.collapsed .nav-link span {
            opacity: 0;
            width: 0;
            display: none;
        }
        
        .sidebar.collapsed .nav-link {
            padding: 15px 0;
            justify-content: center;
        }
        
        .sidebar.collapsed .nav-link i {
            margin-right: 0;
        }
        
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: var(--primary-color);
            background: rgba(212, 175, 55, 0.1);
            border-left: 3px solid var(--primary-color);
        }
        
        .logo-container {
            padding: 20px 0;
            border-bottom: 1px solid #333;
            text-align: center;
            white-space: nowrap;
            overflow: hidden;
        }
        
        .sidebar.collapsed .logo-container h5 {
            display: none;
        }
        
        .sidebar.collapsed .logo-container img {
            width: 40px;
            height: 40px;
            margin-bottom: 0;
        }

        /* Main Content */
        .main-content-wrapper {
            margin-left: var(--sidebar-width);
            transition: margin-left 0.3s ease;
            min-height: 100vh;
            background-color: #f4f6f9;
        }
        
        .sidebar.collapsed + .main-content-wrapper {
            margin-left: var(--sidebar-collapsed-width);
        }

        .main-content {
            padding: 30px;
        }

        /* Top Navbar */
        .top-navbar {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .toggle-btn {
            background: none;
            border: none;
            color: #333;
            font-size: 1.2rem;
            cursor: pointer;
        }

        .logo-text {
            color: var(--primary-color);
            font-weight: 700;
            letter-spacing: 1px;
            margin-top: 10px;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content-wrapper {
                margin-left: 0 !important;
            }
            .top-navbar {
                padding: 15px;
            }
            .main-content {
                padding: 15px; /* Reduce padding on mobile */
            }
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="logo-container">
            <img src="{{ asset('img/logo.jpeg') }}" alt="Logo PMS" style="width: 60px; height: 60px; border-radius: 50%; border: 2px solid #D4AF37; margin-bottom: 10px; object-fit: cover;">
            <h5 class="m-0 logo-text">PMS ADMIN</h5>
        </div>
        <ul class="nav flex-column mt-3">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}" title="Dashboard">
                    <i class="fas fa-home"></i> <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.employees*') ? 'active' : '' }}" href="{{ route('admin.employees') }}" title="Karyawan">
                    <i class="fas fa-users"></i> <span>Karyawan</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.sessions*') ? 'active' : '' }}" href="{{ route('admin.sessions') }}" title="Manajemen Sesi">
                    <i class="fas fa-calendar-alt"></i> <span>Manajemen Sesi</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.attendances*') ? 'active' : '' }}" href="{{ route('admin.attendances') }}" title="Monitoring Absensi">
                    <i class="fas fa-clock"></i> <span>Monitoring Absensi</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.payrolls*') ? 'active' : '' }}" href="{{ route('admin.payrolls') }}" title="Penggajian">
                    <i class="fas fa-money-bill-wave"></i> <span>Penggajian</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.reports*') ? 'active' : '' }}" href="{{ route('admin.reports') }}" title="Laporan">
                    <i class="fas fa-file-alt"></i> <span>Laporan</span>
                </a>
            </li>
            <li class="nav-item mt-4 pt-3 border-top border-secondary">
                <a class="nav-link {{ request()->routeIs('admin.profile*') ? 'active' : '' }}" href="{{ route('admin.profile') }}" title="Edit Profil">
                    <i class="fas fa-user-cog"></i> <span>Edit Profil</span>
                </a>
            </li>
            <li class="nav-item">
                <form action="{{ route('logout') }}" method="POST" id="logout-form">
                    @csrf
                    <button type="submit" class="nav-link bg-transparent border-0 w-100 text-start text-danger" title="Logout">
                        <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
                    </button>
                </form>
            </li>
        </ul>
    </div>

    <!-- Main Content Wrapper -->
    <div class="main-content-wrapper">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <button class="toggle-btn" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="d-flex align-items-center">
                <span class="me-2 d-none d-md-block fw-bold text-dark">Halo, Admin</span>
                <img src="https://ui-avatars.com/api/?name=Admin&background=D4AF37&color=000" class="rounded-circle" width="35" height="35">
            </div>
        </div>

        <div class="main-content">
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // SweetAlert2 Toast/Popup Logic
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 3000,
                toast: true,
                position: 'top-end',
                timerProgressBar: true
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: "{{ session('error') }}",
                showConfirmButton: true
            });
        @endif

        // Global Delete Confirmation
        document.addEventListener('click', function(e) {
            if (e.target.closest('.delete-form') || e.target.classList.contains('btn-delete')) {
                e.preventDefault();
                let form = e.target.closest('form');
                
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('sidebarToggle');
            const mainContent = document.querySelector('.main-content-wrapper');
            
            // Check local storage for preference
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (isCollapsed) {
                sidebar.classList.add('collapsed');
            }

            toggleBtn.addEventListener('click', function() {
                // On mobile, simple toggle class 'show'
                if (window.innerWidth <= 768) {
                    sidebar.classList.toggle('show');
                } else {
                    // On desktop, toggle collapse
                    sidebar.classList.toggle('collapsed');
                    // Save preference
                    localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
                }
            });

            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                if (window.innerWidth <= 768) {
                    const isClickInsideSidebar = sidebar.contains(event.target);
                    const isClickOnToggle = toggleBtn.contains(event.target);
                    
                    if (!isClickInsideSidebar && !isClickOnToggle && sidebar.classList.contains('show')) {
                        sidebar.classList.remove('show');
                    }
                }
            });
        });
    </script>
</body>
</html>
