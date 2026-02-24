<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PT Putra Muara Sukses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #D4AF37;
            --dark-bg: #121212;
            --card-bg: #1e1e1e;
        }
        body {
            background-color: var(--dark-bg);
            font-family: 'Poppins', sans-serif;
            color: #fff;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background-color: var(--card-bg);
            border-radius: 15px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            border: 1px solid #333;
            text-align: center;
        }
        .btn-gold {
            background-color: var(--primary-color);
            color: #000;
            font-weight: 600;
            border: none;
            width: 100%;
            padding: 12px;
        }
        .btn-gold:hover {
            background-color: #b8962e;
            color: #000;
        }
        .form-control {
            background-color: #2a2a2a;
            border: 1px solid #444;
            color: #fff;
        }
        .form-control:focus {
            background-color: #333;
            border-color: var(--primary-color);
            color: #fff;
            box-shadow: 0 0 0 0.25rem rgba(212, 175, 55, 0.25);
        }
        .logo-text {
            color: var(--primary-color);
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }
        .logo-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid var(--primary-color);
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <img src="{{ asset('img/logo.jpeg') }}" alt="Logo PMS" class="logo-img">
        <h4 class="logo-text">PT PUTRA MUARA SUKSES</h4>
        <h5 class="text-muted mb-4 small">Login Admin System</h5>
        
        @if ($errors->any())
            <div class="alert alert-danger text-start">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('authenticate') }}" method="POST" class="text-start">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" required autofocus>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" class="form-control border-end-0" id="password" name="password" required>
                    <span class="input-group-text bg-dark border-start-0 text-muted" style="border: 1px solid #444; cursor: pointer;" onclick="togglePassword()">
                        <i class="fas fa-eye" id="toggleIcon"></i>
                    </span>
                </div>
            </div>
            <button type="submit" class="btn btn-gold rounded-pill">Login</button>
        </form>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
