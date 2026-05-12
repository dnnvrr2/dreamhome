<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DreamHome</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">DreamHome</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('properties.index') }}">Properties</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('owners.index') }}">Owners</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('branches.index') }}">Branches</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('clients.index') }}">Clients</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('staff.index') }}">Staff</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('viewings.index') }}">Viewings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('leases.index') }}">Leases</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('inspections.index') }}">Inspections</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    @auth
                    <li class="nav-item">
                        <span class="nav-link text-white-50">{{ Auth::user()->name }}</span>
                    </li>
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-light my-2">
                                Logout
                            </button>
                        </form>
                    </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>