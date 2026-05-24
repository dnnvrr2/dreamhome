<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DreamHome</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="icon" type="image/png" href="/images/logo.png">
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: #f0f2f5;
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        #sidebar {
            width: 240px;
            min-height: 100vh;
            background: url('/images/sidebar.jpg') no-repeat center center;
            background-size: cover;
            display: flex;
            flex-direction: column;
            transition: width 0.3s ease;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            overflow: hidden;
        }

        #sidebar.collapsed {
            width: 68px;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 20px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-decoration: none;
        }

        .sidebar-brand img {
            width: 36px;
            height: 36px;
            object-fit: contain;
            border-radius: 8px;
            flex-shrink: 0;
        }

        .sidebar-brand span {
            color: white;
            font-weight: 700;
            font-size: 18px;
            white-space: nowrap;
            transition: opacity 0.3s;
        }

        #sidebar.collapsed .sidebar-brand span {
            opacity: 0;
            width: 0;
        }

        .sidebar-nav {
            flex: 1;
            padding: 12px 8px;
            overflow-y: auto;
        }

        .nav-label {
            color: rgba(255,255,255,0.4);
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 8px 12px 4px;
            white-space: nowrap;
            transition: opacity 0.3s;
        }

        #sidebar.collapsed .nav-label {
            opacity: 0;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 10px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: all 0.2s;
            margin-bottom: 2px;
            white-space: nowrap;
        }

        .sidebar-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }

        .sidebar-link.active {
            background: #e94560;
            color: white;
        }

        .sidebar-link i {
            font-size: 18px;
            flex-shrink: 0;
            width: 24px;
            text-align: center;
        }

        .sidebar-link span {
            transition: opacity 0.3s;
            font-size: 14px;
        }

        #sidebar.collapsed .sidebar-link span {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }

        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #e94560;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 14px;
            flex-shrink: 0;
        }

        .user-details {
            transition: opacity 0.3s;
            overflow: hidden;
        }

        #sidebar.collapsed .user-details {
            opacity: 0;
            width: 0;
        }

        .user-name {
            color: white;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
        }

        .user-role {
            color: rgba(255,255,255,0.5);
            font-size: 11px;
            text-transform: uppercase;
        }

        .btn-logout {
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
            padding: 8px 12px;
            background: rgba(233,69,96,0.2);
            border: 1px solid rgba(233,69,96,0.4);
            color: #e94560;
            border-radius: 8px;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
            overflow: hidden;
        }

        .btn-logout:hover {
            background: #e94560;
            color: white;
        }

        #sidebar.collapsed .btn-logout span {
            opacity: 0;
            width: 0;
        }

        /* Toggle button */
        #toggleBtn {
            position: fixed;
            top: 16px;
            left: 200px;
            z-index: 200;
            background: #e94560;
            border: none;
            color: white;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: left 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }

        #toggleBtn.collapsed {
            left: 50px;
        }

        /* Main content */
        #main {
            margin-left: 240px;
            flex: 1;
            transition: margin-left 0.3s ease;
            min-height: 100vh;
        }

        #main.collapsed {
            margin-left: 68px;
        }

        .top-bar {
            background: white;
            padding: 16px 24px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
            display: flex;
            align-items: center;
            justify-content: between;
        }

        .page-content {
            padding: 24px;
        }
        #sidebar.collapsed .sidebar-nav {
            overflow: hidden;
        }
        #sidebar::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: -1;
        }
    </style>
</head>
<body>

{{-- Sidebar --}}
<div id="sidebar">
    <a class="sidebar-brand" href="{{ route('dashboard') }}">
        <img src="/images/logo.png" alt="Logo">
        <span>DreamHome</span>
    </a>

    <div class="sidebar-nav">
        <div class="nav-label">Main</div>
        <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-house"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('branches.index') }}" class="sidebar-link {{ request()->routeIs('branches*') ? 'active' : '' }}">
            <i class="bi bi-building"></i>
            <span>Branches</span>
        </a>

        @if(in_array(Auth::user()->role, ['admin', 'manager', 'supervisor']))
        <div class="nav-label">Management</div>
        <a href="{{ route('properties.index') }}" class="sidebar-link {{ request()->routeIs('properties*') ? 'active' : '' }}">
            <i class="bi bi-house-door"></i>
            <span>Properties</span>
        </a>
        <a href="{{ route('owners.index') }}" class="sidebar-link {{ request()->routeIs('owners*') ? 'active' : '' }}">
            <i class="bi bi-person-badge"></i>
            <span>Owners</span>
        </a>
        <a href="{{ route('clients.index') }}" class="sidebar-link {{ request()->routeIs('clients*') ? 'active' : '' }}">
            <i class="bi bi-people"></i>
            <span>Clients</span>
        </a>
        <a href="{{ route('viewings.index') }}" class="sidebar-link {{ request()->routeIs('viewings*') ? 'active' : '' }}">
            <i class="bi bi-eye"></i>
            <span>Viewings</span>
        </a>
        <a href="{{ route('inspections.index') }}" class="sidebar-link {{ request()->routeIs('inspections*') ? 'active' : '' }}">
            <i class="bi bi-clipboard-check"></i>
            <span>Inspections</span>
        </a>
        @endif

        @if(in_array(Auth::user()->role, ['admin', 'manager']))
        <div class="nav-label">Admin</div>
        <a href="{{ route('staff.index') }}" class="sidebar-link {{ request()->routeIs('staff*') ? 'active' : '' }}">
            <i class="bi bi-person-workspace"></i>
            <span>Staff</span>
        </a>
        <a href="{{ route('leases.index') }}" class="sidebar-link {{ request()->routeIs('leases*') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-text"></i>
            <span>Leases</span>
        </a>
        @endif
    </div>

    <div class="sidebar-footer">
        @auth
        <div class="user-info">
            <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
            <div class="user-details">
                <div class="user-name">{{ Auth::user()->name }}</div>
                <div class="user-role">{{ Auth::user()->role }}</div>
            </div>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn-logout">
                <i class="bi bi-box-arrow-left"></i>
                <span>Logout</span>
            </button>
        </form>
        @endauth
    </div>
</div>

{{-- Toggle Button --}}
<button id="toggleBtn" onclick="toggleSidebar()">
    <i class="bi bi-chevron-left" id="toggleIcon"></i>
</button>

{{-- Main Content --}}
<div id="main">
    <div class="page-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const main = document.getElementById('main');
    const btn = document.getElementById('toggleBtn');
    const icon = document.getElementById('toggleIcon');

    sidebar.classList.toggle('collapsed');
    main.classList.toggle('collapsed');
    btn.classList.toggle('collapsed');

    if (sidebar.classList.contains('collapsed')) {
        icon.className = 'bi bi-chevron-right';
        localStorage.setItem('sidebarCollapsed', 'true');
    } else {
        icon.className = 'bi bi-chevron-left';
        localStorage.setItem('sidebarCollapsed', 'false');
    }
}

// Restore states on page load
document.addEventListener('DOMContentLoaded', function () {
    // Restore dark mode
    if (localStorage.getItem('darkMode') === 'enabled') {
        document.body.classList.add('dark-mode');
        document.getElementById('darkIcon').className = 'bi bi-sun';
        document.getElementById('darkLabel').textContent = 'Light Mode';
    }

    // Restore sidebar state
    if (localStorage.getItem('sidebarCollapsed') === 'true') {
        document.getElementById('sidebar').classList.add('collapsed');
        document.getElementById('main').classList.add('collapsed');
        document.getElementById('toggleBtn').classList.add('collapsed');
        document.getElementById('toggleIcon').className = 'bi bi-chevron-right';
    }
});
   
</script>
</body>
</html>