<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DreamHome — Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 16px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .brand-icon {
            width: 60px;
            height: 60px;
            background: #1a1a2e;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }
        .btn-login {
            background: #1a1a2e;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: 500;
        }
        .btn-login:hover {
            background: #2d2d4e;
            color: white;
        }
        .form-control:focus {
            border-color: #1a1a2e;
            box-shadow: 0 0 0 0.2rem rgba(26,26,46,0.15);
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="text-center mb-4">
            <div class="brand-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="white" viewBox="0 0 16 16">
                    <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.707 1.5Z"/>
                    <path d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293l6-6Z"/>
                </svg>
            </div>
            <h4 class="fw-bold mb-1">DreamHome</h4>
            <p class="text-muted small">Property Management System</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger py-2 small">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label small fw-500">Email Address</label>
                <input type="email" name="email" class="form-control"
                       value="{{ old('email') }}" placeholder="admin@dreamhome.com" required>
            </div>
            <div class="mb-4">
                <label class="form-label small fw-500">Password</label>
                <input type="password" name="password" class="form-control"
                       placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-login w-100">
                Sign In
            </button>
        </form>

        <p class="text-center text-muted small mt-4 mb-0">
            DreamHome Property Rental Management
        </p>
    </div>
</body>
</html>