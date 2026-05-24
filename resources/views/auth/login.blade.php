<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DreamHome — Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="/images/logo.png">
    <style>
        body {
            background: url('/images/login.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: rgba(0, 0, 0, 0.25);
            border-radius: 16px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.4);
        }
        .brand-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }
        h4, p {
            color: white !important;
        }
        .form-label {
            color: white !important;
        }
        .form-control {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
        }
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }
        .form-control:focus {
            background: rgba(255, 255, 255, 0.25);
            border-color: rgba(255, 255, 255, 0.6);
            color: white;
            box-shadow: none;
        }
        .btn-login {
            background: rgba(29, 29, 29, 0.85);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: 500;
        }
        .btn-login:hover {
            background: rgba(30, 30, 60, 1);
            color: white;
        }
        .footer-text {
            color: rgba(255, 255, 255, 0.7) !important;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="text-center mb-4">
            <div class="brand-icon">
                <img src="/images/logo.png" alt="DreamHome Logo" style="width: 60px; height: 60px; object-fit: contain;">
            </div>
            <h4 class="fw-bold mb-1">DreamHome</h4>
            <p class="small">Property Management System</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger py-2 small">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label small">Email Address</label>
                <input type="email" name="email" class="form-control"
                       value="{{ old('email') }}" placeholder="Enter your email" required>
            </div>
            <div class="mb-4">
                <label class="form-label small">Password</label>
                <input type="password" name="password" class="form-control"
                       placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn btn-login w-100">
                Sign In
            </button>
        </form>

        <p class="text-center small mt-4 mb-0 footer-text">
            DreamHome Property Rental Management
        </p>
    </div>
</body>
</html>