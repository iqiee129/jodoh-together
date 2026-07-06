@php
    $admin = auth()->user();
    $adminInitial = strtoupper(substr($admin?->name ?? 'A', 0, 1));
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jodoh Together | Admin Settings</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg: #f7f3ef;
            --dark: #1b1c22;
            --text: #111827;
            --muted: #6b7280;
            --border: #eeeeee;
            --coral: #d95f4a;
            --coral-dark: #b94e3e;
            --coral-light: #fff1ee;
            --red: #dc2626;
            --green: #047857;
            --shadow: 0 14px 38px rgba(31, 41, 55, 0.08);
            --shadow-hover: 0 20px 55px rgba(31, 41, 55, 0.13);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: var(--bg);
            font-family: 'Inter', sans-serif;
            color: var(--text);
        }

        .admin-sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 280px;
            height: 100vh;
            background: var(--dark);
            padding: 28px 20px;
            display: flex;
            flex-direction: column;
            z-index: 20;
        }

        .admin-logo {
            display: flex;
            align-items: center;
            gap: 13px;
            color: #ffffff;
            margin-bottom: 38px;
        }

        .admin-logo-icon {
            width: 46px;
            height: 46px;
            border-radius: 15px;
            background: linear-gradient(135deg, var(--coral), var(--coral-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .admin-logo strong {
            display: block;
            font-size: 20px;
            font-weight: 900;
        }

        .admin-logo span {
            color: #9ca3af;
            font-size: 12px;
            font-weight: 700;
        }

        .admin-nav {
            display: grid;
            gap: 10px;
        }

        .admin-nav-link {
            color: #d1d5db;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 13px;
            padding: 14px 15px;
            border-radius: 15px;
            font-weight: 800;
            font-size: 14px;
            transition: 0.25s ease;
            border: none;
            background: transparent;
            width: 100%;
            cursor: pointer;
            font-family: inherit;
        }

        .admin-nav-link i {
            width: 20px;
            text-align: center;
        }

        .admin-nav-link:hover,
        .admin-nav-link.active {
            background: var(--coral);
            color: #ffffff;
            transform: translateX(4px);
            box-shadow: 0 10px 24px rgba(217, 95, 74, 0.22);
        }

        .sidebar-bottom {
            margin-top: auto;
            display: grid;
            gap: 14px;
        }

        .admin-mini-card {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 13px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.06);
            color: #ffffff;
            transition: 0.25s ease;
        }

        .admin-mini-card:hover {
            transform: translateY(-3px);
            background: rgba(255, 255, 255, 0.1);
        }

        .admin-mini-avatar {
            width: 38px;
            height: 38px;
            border-radius: 13px;
            background: var(--coral);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
        }

        .admin-mini-card strong {
            display: block;
            font-size: 13px;
            font-weight: 900;
        }

        .admin-mini-card span {
            display: block;
            margin-top: 3px;
            color: #9ca3af;
            font-size: 11px;
            font-weight: 700;
        }

        .admin-main {
            margin-left: 280px;
            padding: 34px 44px;
            max-width: 1260px;
        }

        .admin-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            margin-bottom: 24px;
        }

        .admin-eyebrow {
            margin: 0 0 8px;
            color: var(--coral);
            font-size: 13px;
            font-weight: 900;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }

        .admin-header h1 {
            margin: 0;
            font-size: 40px;
            font-weight: 900;
            letter-spacing: -0.05em;
        }

        .admin-header span {
            display: block;
            margin-top: 8px;
            color: var(--muted);
            font-size: 15px;
            font-weight: 700;
        }

        .profile-pill {
            background: #ffffff;
            border-radius: 18px;
            padding: 10px 14px 10px 10px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: var(--shadow);
            transition: 0.25s ease;
            min-width: 230px;
        }

        .profile-pill:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
        }

        .profile-avatar {
            width: 44px;
            height: 44px;
            border-radius: 15px;
            background: linear-gradient(135deg, var(--coral), var(--coral-dark));
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            flex-shrink: 0;
        }

        .profile-pill strong {
            display: block;
            font-size: 14px;
            font-weight: 900;
        }

        .profile-pill span {
            margin: 3px 0 0;
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
        }

        .settings-hero {
            background: linear-gradient(135deg, var(--coral), var(--coral-dark));
            color: #ffffff;
            border-radius: 26px;
            padding: 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 22px;
            box-shadow: 0 18px 42px rgba(217, 95, 74, 0.22);
            margin-bottom: 24px;
        }

        .settings-hero h2 {
            margin: 0;
            font-size: 26px;
            font-weight: 900;
        }

        .settings-hero p {
            margin: 8px 0 0;
            font-size: 14px;
            font-weight: 700;
            opacity: 0.9;
        }

        .hero-icon {
            width: 72px;
            height: 72px;
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.18);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
        }

        .settings-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        .settings-card {
            background: #ffffff;
            border: 1px solid rgba(225, 225, 225, 0.9);
            border-radius: 24px;
            padding: 26px;
            box-shadow: var(--shadow);
            transition: 0.25s ease;
        }

        .settings-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-hover);
        }

        .settings-card.full {
            grid-column: 1 / -1;
        }

        .card-head {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 22px;
        }

        .card-icon {
            width: 48px;
            height: 48px;
            border-radius: 16px;
            background: var(--coral-light);
            color: var(--coral);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .card-head h2 {
            margin: 0;
            font-size: 21px;
            font-weight: 900;
        }

        .card-head p {
            margin: 6px 0 0;
            color: var(--muted);
            font-size: 13px;
            font-weight: 700;
        }

        .settings-form {
            display: grid;
            gap: 16px;
        }

        .form-group {
            display: grid;
            gap: 8px;
        }

        .form-group label {
            color: #374151;
            font-size: 13px;
            font-weight: 900;
        }

        .form-group input {
            width: 100%;
            height: 48px;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            background: #ffffff;
            padding: 0 14px;
            outline: none;
            font-family: inherit;
            font-size: 14px;
            color: var(--text);
        }

        .form-group input:focus {
            border-color: var(--coral);
            box-shadow: 0 0 0 4px rgba(217, 95, 74, 0.12);
        }

        .save-btn {
            height: 46px;
            padding: 0 18px;
            border-radius: 14px;
            border: none;
            background: var(--coral);
            color: #ffffff;
            font-size: 14px;
            font-weight: 900;
            font-family: inherit;
            cursor: pointer;
            transition: 0.25s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            width: fit-content;
        }

        .save-btn:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
            background: #c94f3d;
        }

        .success-alert,
        .error-msg {
            font-size: 13px;
            font-weight: 800;
        }

        .success-alert {
            background: #ecfdf5;
            color: var(--green);
            border: 1px solid #bbf7d0;
            border-radius: 14px;
            padding: 12px 14px;
            display: flex;
            align-items: center;
            gap: 9px;
            margin-bottom: 16px;
        }

        .error-msg {
            color: var(--red);
        }

        .role-list {
            display: grid;
            gap: 12px;
        }

        .role-item {
            background: #fafafa;
            border: 1px solid #f0f0f0;
            border-radius: 17px;
            padding: 15px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            transition: 0.25s ease;
        }

        .role-item:hover {
            transform: translateY(-3px);
            background: #fff7f4;
            border-color: #ffd6cf;
        }

        .role-item i {
            color: var(--coral);
            margin-top: 2px;
        }

        .role-item strong {
            display: block;
            font-size: 14px;
            font-weight: 900;
        }

        .role-item span {
            display: block;
            margin-top: 4px;
            color: var(--muted);
            font-size: 13px;
            font-weight: 700;
            line-height: 1.5;
        }

        @media (max-width: 1000px) {
            .settings-grid {
                grid-template-columns: 1fr;
            }

            .settings-card.full {
                grid-column: auto;
            }
        }

        @media (max-width: 850px) {
            .admin-sidebar {
                position: static;
                width: 100%;
                height: auto;
            }

            .admin-main {
                margin-left: 0;
                padding: 22px;
            }

            .admin-header,
            .settings-hero {
                flex-direction: column;
                align-items: flex-start;
            }

            .profile-pill {
                width: 100%;
            }
        }
    </style>
    <link rel="stylesheet" href="{{ asset('theme.css') }}">
</head>

<body data-theme="{{ $admin?->theme_mode === 'dark' ? 'dark' : 'light' }}">
    <aside class="admin-sidebar">
        <div class="admin-logo">
            <div class="admin-logo-icon">
                <i class="fa-solid fa-heart"></i>
            </div>

            <div>
                <strong>Jodoh Admin</strong>
                <span>System Panel</span>
            </div>
        </div>

        <nav class="admin-nav">
            <a href="{{ route('admin.dashboard') }}" class="admin-nav-link">
                <i class="fa-solid fa-chart-simple"></i>
                Overview
            </a>

            <a href="{{ route('admin.vendors') }}" class="admin-nav-link">
                <i class="fa-solid fa-store"></i>
                Vendors
            </a>

            <a href="{{ route('admin.users') }}" class="admin-nav-link">
                <i class="fa-regular fa-user"></i>
                Users
            </a>

            <a href="{{ route('admin.settings') }}" class="admin-nav-link active">
                <i class="fa-solid fa-gear"></i>
                Settings
            </a>
        </nav>

        <div class="sidebar-bottom">
            <div class="admin-mini-card">
                <div class="admin-mini-avatar">{{ $adminInitial }}</div>

                <div>
                    <strong>{{ $admin?->name ?? 'Admin' }}</strong>
                    <span>Administrator</span>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="admin-nav-link">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <main class="admin-main">
        <header class="admin-header">
            <div>
                <p class="admin-eyebrow">Admin Settings</p>
                <h1>Settings</h1>
                <span>Manage your admin account information and security.</span>
            </div>

            <div class="profile-pill">
                <div class="profile-avatar">{{ $adminInitial }}</div>

                <div>
                    <strong>{{ $admin?->name ?? 'Admin' }}</strong>
                    <span>{{ $admin?->email ?? 'admin@gmail.com' }}</span>
                </div>
            </div>
        </header>

        <section class="settings-hero">
            <div>
                <h2>Admin Control Centre</h2>
                <p>Update your profile, manage password security, and review admin permissions.</p>
            </div>

            <div class="hero-icon">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
        </section>

        <section class="settings-grid">
            <div class="settings-card">
                <div class="card-head">
                    <div class="card-icon">
                        <i class="fa-regular fa-user"></i>
                    </div>

                    <div>
                        <h2>Profile Information</h2>
                        <p>Update your admin name and email address.</p>
                    </div>
                </div>

                @if (session('profile_success'))
                    <div class="success-alert">
                        <i class="fa-solid fa-circle-check"></i>
                        {{ session('profile_success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.settings.profile') }}" class="settings-form">
                    @csrf
                    @method('PATCH')

                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" value="{{ old('name', $admin->name) }}" required>
                        @error('name') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="{{ old('email', $admin->email) }}" required
                            pattern="[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}"
                            title="Please enter a valid email address, like name@example.com.">
                        @error('email') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" class="save-btn">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Save Profile
                    </button>
                </form>
            </div>

            <div class="settings-card">
                <div class="card-head">
                    <div class="card-icon">
                        <i class="fa-solid fa-lock"></i>
                    </div>

                    <div>
                        <h2>Change Password</h2>
                        <p>Use a strong password to protect admin access.</p>
                    </div>
                </div>

                @if (session('password_success'))
                    <div class="success-alert">
                        <i class="fa-solid fa-circle-check"></i>
                        {{ session('password_success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.settings.password') }}" class="settings-form">
                    @csrf
                    @method('PATCH')

                    <div class="form-group">
                        <label>Current Password</label>
                        <input type="password" name="current_password" required>
                        @error('current_password') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="password" required>
                        @error('password') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <input type="password" name="password_confirmation" required>
                    </div>

                    <button type="submit" class="save-btn">
                        <i class="fa-solid fa-key"></i>
                        Update Password
                    </button>
                </form>
            </div>

            

            <div class="settings-card full">
                <div class="card-head">
                    <div class="card-icon">
                        <i class="fa-solid fa-user-shield"></i>
                    </div>

                    <div>
                        <h2>Admin Role Permissions</h2>
                        <p>This explains what your admin account can do in Jodoh Together.</p>
                    </div>
                </div>

                <div class="role-list">
                    <div class="role-item">
                        <i class="fa-solid fa-store"></i>
                        <div>
                            <strong>Manage Vendors</strong>
                            <span>Add, edit, delete, activate, and deactivate vendor listings shown to users.</span>
                        </div>
                    </div>

                    <div class="role-item">
                        <i class="fa-regular fa-user"></i>
                        <div>
                            <strong>View User Accounts</strong>
                            <span>View registered users and their wedding planning details for monitoring purposes.</span>
                        </div>
                    </div>

                    <div class="role-item">
                        <i class="fa-solid fa-chart-simple"></i>
                        <div>
                            <strong>Monitor System Dashboard</strong>
                            <span>View total users, vendors, categories, and recent user activity.</span>
                        </div>
                    </div>

                    <div class="role-item">
                        <i class="fa-solid fa-shield-halved"></i>
                        <div>
                            <strong>Protected Admin Access</strong>
                            <span>Normal users cannot access admin pages. Admin role is controlled through the system, not from the user list.</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <script>
        const adminDarkModeToggle = document.getElementById('adminDarkModeToggle');
        const adminThemeMode = document.getElementById('adminThemeMode');

        if (adminDarkModeToggle && adminThemeMode) {
            adminDarkModeToggle.addEventListener('change', function () {
                const mode = this.checked ? 'dark' : 'light';
                adminThemeMode.value = mode;
                document.body.dataset.theme = mode;
            });
        }
    </script>
</body>
</html>
