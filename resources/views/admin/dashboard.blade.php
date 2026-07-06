@php
    $admin = auth()->user();

    $hasUsersTable = \Illuminate\Support\Facades\Schema::hasTable('users');
    $hasVendorsTable = \Illuminate\Support\Facades\Schema::hasTable('vendors');
    $hasRoleColumn = $hasUsersTable && \Illuminate\Support\Facades\Schema::hasColumn('users', 'role');

    $totalUsers = $hasUsersTable
        ? \Illuminate\Support\Facades\DB::table('users')
            ->when($hasRoleColumn, fn ($query) => $query->where('role', '!=', 'admin'))
            ->count()
        : 0;

    $totalVendors = $hasVendorsTable
        ? \Illuminate\Support\Facades\DB::table('vendors')->count()
        : 0;

    $activeVendors = $hasVendorsTable && \Illuminate\Support\Facades\Schema::hasColumn('vendors', 'is_active')
        ? \Illuminate\Support\Facades\DB::table('vendors')->where('is_active', true)->count()
        : $totalVendors;

    $totalCategories = $hasVendorsTable
        ? \Illuminate\Support\Facades\DB::table('vendors')
            ->whereNotNull('category')
            ->distinct()
            ->count('category')
        : 0;

    $vendorCategories = $hasVendorsTable
        ? \Illuminate\Support\Facades\DB::table('vendors')
            ->select('category', \Illuminate\Support\Facades\DB::raw('COUNT(*) as total'))
            ->whereNotNull('category')
            ->groupBy('category')
            ->orderBy('category')
            ->get()
        : collect();

    $recentUsers = $hasUsersTable
        ? \Illuminate\Support\Facades\DB::table('users')
            ->when($hasRoleColumn, fn ($query) => $query->where('role', '!=', 'admin'))
            ->latest()
            ->limit(5)
            ->get()
        : collect();

    $adminInitial = strtoupper(substr($admin?->name ?? 'A', 0, 1));
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jodoh Together | Admin Dashboard</title>

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
            transition: 0.2s ease;
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
            transform: translateX(3px);
        }

        .admin-nav-link.disabled {
            opacity: 0.5;
            cursor: not-allowed;
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
            max-width: 1300px;
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

        .header-actions {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .notification-btn {
            width: 50px;
            height: 50px;
            border-radius: 16px;
            border: none;
            background: #ffffff;
            color: var(--text);
            box-shadow: var(--shadow);
            cursor: pointer;
            font-size: 18px;
            transition: 0.25s ease;
        }

        .notification-btn:hover,
        .profile-btn:hover,
        .admin-card:hover,
        .admin-stat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
        }

        .profile-wrap {
            position: relative;
        }

        .profile-btn {
            min-width: 230px;
            border: none;
            background: #ffffff;
            border-radius: 18px;
            padding: 10px 14px 10px 10px;
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            box-shadow: var(--shadow);
            font-family: inherit;
            transition: 0.25s ease;
        }

        .profile-avatar,
        .user-avatar {
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

        .profile-text {
            display: grid;
            text-align: left;
            flex: 1;
        }

        .profile-text strong {
            font-size: 14px;
            font-weight: 900;
            color: var(--text);
        }

        .profile-text span {
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            margin: 3px 0 0;
            font-size: 12px;
            color: var(--muted);
            font-weight: 700;
        }

        .profile-dropdown {
            position: absolute;
            top: calc(100% + 12px);
            right: 0;
            width: 260px;
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 12px;
            box-shadow: var(--shadow-hover);
            display: none;
            z-index: 30;
        }

        .profile-wrap.open .profile-dropdown {
            display: block;
        }

        .dropdown-link {
            width: 100%;
            border: none;
            background: transparent;
            color: #374151;
            text-decoration: none;
            padding: 11px 10px;
            border-radius: 13px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
            font-family: inherit;
        }

        .dropdown-link:hover {
            background: var(--coral-light);
            color: var(--coral);
        }

        .dropdown-link.logout {
            color: #dc2626;
        }

        .welcome-card {
            background: linear-gradient(135deg, var(--coral), var(--coral-dark));
            color: #ffffff;
            border-radius: 26px;
            padding: 26px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            box-shadow: 0 18px 42px rgba(217, 95, 74, 0.22);
            margin-bottom: 22px;
        }

        .welcome-card p {
            margin: 0 0 6px;
            font-size: 13px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .welcome-card h2 {
            margin: 0;
            font-size: 25px;
            font-weight: 900;
        }

        .welcome-card span {
            display: block;
            margin-top: 8px;
            font-size: 14px;
            font-weight: 700;
            opacity: 0.9;
        }

        .welcome-btn {
            height: 46px;
            padding: 0 17px;
            border-radius: 15px;
            background: #ffffff;
            color: var(--coral);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 9px;
            font-size: 14px;
            font-weight: 900;
            white-space: nowrap;
        }

        .admin-stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 18px;
            margin-bottom: 24px;
        }

        .admin-stat-card {
            background: #ffffff;
            border: 1px solid rgba(225, 225, 225, 0.9);
            border-radius: 22px;
            padding: 22px;
            color: var(--text);
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            gap: 16px;
            transition: 0.25s ease;
        }

        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 17px;
            background: var(--coral-light);
            color: var(--coral);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .admin-stat-card span {
            display: block;
            font-size: 13px;
            font-weight: 800;
            color: var(--muted);
        }

        .admin-stat-card strong {
            display: block;
            margin-top: 5px;
            font-size: 31px;
            font-weight: 900;
            line-height: 1;
        }

        .admin-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 22px;
        }

        .admin-card {
            background: #ffffff;
            border-radius: 24px;
            padding: 24px;
            border: 1px solid rgba(225, 225, 225, 0.9);
            box-shadow: var(--shadow);
            transition: 0.25s ease;
        }

        .admin-card.wide {
            grid-column: 1 / -1;
        }

        .card-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 20px;
        }

        .card-head h2 {
            margin: 0;
            font-size: 20px;
            font-weight: 900;
        }

        .card-head p {
            margin: 6px 0 0;
            color: var(--muted);
            font-size: 13px;
            font-weight: 700;
        }

        .admin-action-btn {
            min-height: 42px;
            padding: 0 15px;
            border-radius: 13px;
            background: var(--coral);
            color: #ffffff;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 9px;
            font-size: 13px;
            font-weight: 900;
            white-space: nowrap;
        }

        .admin-action-btn.muted {
            background: #f3f4f6;
            color: #374151;
        }

        .admin-list,
        .note-grid {
            display: grid;
            gap: 12px;
        }

        .admin-row,
        .user-row,
        .note-row {
            padding: 15px;
            background: #fafafa;
            border: 1px solid #f0f0f0;
            border-radius: 17px;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .admin-row {
            justify-content: space-between;
        }

        .admin-row span,
        .note-row span {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #374151;
            font-size: 14px;
            font-weight: 800;
        }

        .admin-row i,
        .note-row i {
            color: var(--coral);
        }

        .user-row strong {
            display: block;
            font-size: 14px;
            font-weight: 900;
        }

        .user-row span {
            display: block;
            margin-top: 4px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
        }

        @media (max-width: 1200px) {
            .admin-stats,
            .admin-grid {
                grid-template-columns: 1fr 1fr;
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
            .welcome-card {
                flex-direction: column;
                align-items: flex-start;
            }

            .admin-stats,
            .admin-grid {
                grid-template-columns: 1fr;
            }
        }
        .header-actions {
    display: flex;
    align-items: center;
    gap: 14px;
}

.profile-wrap {
    position: relative;
}

.profile-btn {
    min-width: 230px;
    border: none;
    background: #ffffff;
    border-radius: 18px;
    padding: 10px 14px 10px 10px;
    display: flex;
    align-items: center;
    gap: 12px;
    cursor: pointer;
    box-shadow: var(--shadow);
    font-family: inherit;
    transition: 0.25s ease;
}

.profile-btn:hover {
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

.profile-avatar.large {
    width: 48px;
    height: 48px;
}

.profile-text {
    display: grid;
    text-align: left;
    flex: 1;
}

.profile-text strong {
    font-size: 14px;
    font-weight: 900;
    color: var(--text);
}

.profile-text span {
    margin-top: 3px;
    font-size: 12px;
    color: var(--muted);
    font-weight: 700;
}

.profile-chevron {
    color: var(--muted);
    font-size: 12px;
    transition: 0.2s ease;
}

.profile-wrap.open .profile-chevron {
    transform: rotate(180deg);
}

.profile-dropdown {
    position: absolute;
    top: calc(100% + 12px);
    right: 0;
    width: 270px;
    background: #ffffff;
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 12px;
    box-shadow: var(--shadow-hover);
    display: none;
    z-index: 30;
}

.profile-wrap.open .profile-dropdown {
    display: block;
}

.settings-dropdown-head {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px;
}

.settings-dropdown-head strong {
    display: block;
    font-size: 14px;
    font-weight: 900;
    color: var(--text);
}

.settings-dropdown-head span {
    display: block;
    margin-top: 3px;
    color: var(--muted);
    font-size: 12px;
    font-weight: 700;
    word-break: break-all;
}

.dropdown-divider {
    height: 1px;
    background: var(--border);
    margin: 8px 0;
}

.dropdown-link {
    width: 100%;
    border: none;
    background: transparent;
    color: #374151;
    text-decoration: none;
    padding: 11px 10px;
    border-radius: 13px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
    font-weight: 800;
    cursor: pointer;
    font-family: inherit;
    transition: 0.2s ease;
}

.dropdown-link:hover {
    background: var(--coral-light);
    color: var(--coral);
}

.dropdown-link.logout {
    color: #dc2626;
}

.dropdown-link.logout:hover {
    background: #fef2f2;
    color: #dc2626;
}
/* Floating hover effect - same theme as user pages */
.admin-nav-link,
.admin-action-btn,
.welcome-btn,
.profile-btn,
.notification-btn,
.dropdown-link,
.admin-stat-card,
.admin-card,
.admin-row,
.user-row,
.note-row,
.admin-mini-card {
    transition: 0.25s ease;
}

.admin-nav-link:hover {
    transform: translateX(4px);
    box-shadow: 0 10px 24px rgba(217, 95, 74, 0.22);
}

.admin-action-btn:hover,
.welcome-btn:hover,
.profile-btn:hover,
.notification-btn:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-hover);
}

.admin-action-btn:hover {
    background: #c94f3d;
}

.welcome-btn:hover {
    background: #fff7f4;
    color: var(--coral-dark);
}

.dropdown-link:hover {
    transform: translateX(3px);
}

.admin-stat-card:hover,
.admin-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-hover);
}

.admin-row:hover,
.user-row:hover,
.note-row:hover {
    transform: translateY(-3px);
    background: #fff7f4;
    border-color: #ffd6cf;
    box-shadow: 0 12px 28px rgba(31, 41, 55, 0.08);
}

.admin-mini-card:hover {
    transform: translateY(-3px);
    background: rgba(255, 255, 255, 0.1);
}

.stat-icon,
.profile-avatar,
.admin-logo-icon,
.user-avatar,
.admin-mini-avatar {
    transition: 0.25s ease;
}

.admin-stat-card:hover .stat-icon,
.profile-btn:hover .profile-avatar,
.admin-logo:hover .admin-logo-icon,
.user-row:hover .user-avatar,
.admin-mini-card:hover .admin-mini-avatar {
    transform: scale(1.06);
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
            <a href="{{ route('admin.dashboard') }}" class="admin-nav-link active">
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
<a href="{{ route('admin.settings') }}" class="admin-nav-link">
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
                <p class="admin-eyebrow">System Management</p>
                <h1>Admin Dashboard</h1>
                <span>Manage platform users, vendors, categories, and recent activity.</span>
            </div>

            <div class="header-actions">
    <div class="profile-wrap" id="adminProfileWrap">
        <button class="profile-btn" id="adminProfileBtn" type="button">
            <div class="profile-avatar">{{ $adminInitial }}</div>

            <div class="profile-text">
                <strong>{{ $admin?->name ?? 'Admin' }}</strong>
                <span>Admin Settings</span>
            </div>

            <i class="fa-solid fa-chevron-down profile-chevron"></i>
        </button>

        <div class="profile-dropdown">
            <div class="settings-dropdown-head">
                <div class="profile-avatar large">{{ $adminInitial }}</div>

                <div>
                    <strong>{{ $admin?->name ?? 'Admin' }}</strong>
                    <span>{{ $admin?->email ?? 'admin@gmail.com' }}</span>
                </div>
            </div>

            <div class="dropdown-divider"></div>

            <a href="{{ route('admin.settings') }}" class="dropdown-link">
    <i class="fa-solid fa-gear"></i>
    Settings
</a>

            <div class="dropdown-divider"></div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="dropdown-link logout">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    Logout
                </button>
            </form>
        </div>
    </div>
</div>
        </header>

        <section class="welcome-card">
            <div>
                <p>Welcome back, {{ $admin?->name ?? 'Admin' }}</p>
                <h2>Here is your system overview for today.</h2>
                <span>Use this dashboard to monitor users and manage wedding service vendors.</span>
            </div>

            <a href="{{ route('admin.vendors') }}" class="welcome-btn">
                <i class="fa-solid fa-store"></i>
                Manage Vendors
            </a>
        </section>

        <section class="admin-stats">
            <div class="admin-stat-card">
                <div class="stat-icon"><i class="fa-regular fa-user"></i></div>
                <div>
                    <span>Total Users</span>
                    <strong>{{ $totalUsers }}</strong>
                </div>
            </div>

            <div class="admin-stat-card">
                <div class="stat-icon"><i class="fa-solid fa-store"></i></div>
                <div>
                    <span>Total Vendors</span>
                    <strong>{{ $totalVendors }}</strong>
                </div>
            </div>

            <div class="admin-stat-card">
                <div class="stat-icon"><i class="fa-solid fa-circle-check"></i></div>
                <div>
                    <span>Active Vendors</span>
                    <strong>{{ $activeVendors }}</strong>
                </div>
            </div>

            <div class="admin-stat-card">
                <div class="stat-icon"><i class="fa-solid fa-layer-group"></i></div>
                <div>
                    <span>Categories</span>
                    <strong>{{ $totalCategories }}</strong>
                </div>
            </div>
        </section>

        <section class="admin-grid">
            <div class="admin-card">
                <div class="card-head">
                    <div>
                        <h2>Vendor Summary</h2>
                        <p>Current vendors grouped by category.</p>
                    </div>

                    <a href="{{ route('admin.vendors') }}" class="admin-action-btn">
                        <i class="fa-solid fa-store"></i>
                        Manage
                    </a>
                </div>

                <div class="admin-list">
                    @forelse ($vendorCategories as $item)
                        <div class="admin-row">
                            <span>
                                <i class="fa-solid fa-circle-dot"></i>
                                {{ ucfirst($item->category) }}
                            </span>

                            <strong>{{ $item->total }} vendor{{ $item->total > 1 ? 's' : '' }}</strong>
                        </div>
                    @empty
                        <div class="admin-row">
                            <span>No vendors yet</span>
                            <strong>0</strong>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="admin-card">
                <div class="card-head">
                    <div>
                        <h2>Recent Users</h2>
                        <p>Latest registered users.</p>
                    </div>

                    <a href="{{ route('admin.users') }}" class="admin-action-btn muted">
    <i class="fa-regular fa-user"></i>
    View
</a>
                </div>

                <div class="admin-list">
                    @forelse ($recentUsers as $recentUser)
                        <div class="user-row">
                            <div class="user-avatar">
                                {{ strtoupper(substr($recentUser->name ?? 'U', 0, 1)) }}
                            </div>

                            <div>
                                <strong>{{ $recentUser->name }}</strong>
                                <span>
                                    Created
                                    {{ $recentUser->created_at ? \Carbon\Carbon::parse($recentUser->created_at)->format('d M Y') : '-' }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="admin-row">
                            <span>No users yet</span>
                            <strong>0</strong>
                        </div>
                    @endforelse
                </div>
            </div>

            
        </section>
    </main>

    <script>
        const profileWrap = document.getElementById('adminProfileWrap');
        const profileBtn = document.getElementById('adminProfileBtn');

        profileBtn.addEventListener('click', function (event) {
            event.stopPropagation();
            profileWrap.classList.toggle('open');
        });

        document.addEventListener('click', function (event) {
            if (!profileWrap.contains(event.target)) {
                profileWrap.classList.remove('open');
            }
        });
    </script>
</body>
</html>
