@php
    $admin = auth()->user();
    $adminInitial = strtoupper(substr($admin?->name ?? 'A', 0, 1));
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jodoh Together | Admin Users</title>

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
            transition: 0.25s ease;
        }

        .admin-logo:hover .admin-logo-icon {
            transform: scale(1.06);
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
            max-width: 1360px;
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
            transition: 0.25s ease;
        }

        .dropdown-link:hover {
            background: var(--coral-light);
            color: var(--coral);
            transform: translateX(3px);
        }

        .dropdown-link.logout {
            color: var(--red);
        }

        .dropdown-link.logout:hover {
            background: #fef2f2;
            color: var(--red);
        }

        .user-stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: #ffffff;
            border: 1px solid rgba(225, 225, 225, 0.9);
            border-radius: 22px;
            padding: 22px;
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            gap: 16px;
            transition: 0.25s ease;
        }

        .stat-card:hover,
        .filter-card:hover,
        .users-panel:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-hover);
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
            flex-shrink: 0;
            transition: 0.25s ease;
        }

        .stat-card:hover .stat-icon {
            transform: scale(1.06);
        }

        .stat-card span {
            display: block;
            color: var(--muted);
            font-size: 13px;
            font-weight: 800;
        }

        .stat-card strong {
            display: block;
            margin-top: 5px;
            font-size: 31px;
            font-weight: 900;
        }

        .filter-card,
        .users-panel {
            background: #ffffff;
            border: 1px solid rgba(225, 225, 225, 0.9);
            border-radius: 24px;
            padding: 22px;
            box-shadow: var(--shadow);
            transition: 0.25s ease;
        }

        .filter-card {
            margin-bottom: 24px;
        }

        .filter-top {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: center;
            margin-bottom: 18px;
        }

        .filter-top h2,
        .users-panel h2 {
            margin: 0;
            font-size: 21px;
            font-weight: 900;
        }

        .filter-top p,
        .users-panel p {
            margin: 6px 0 0;
            color: var(--muted);
            font-size: 13px;
            font-weight: 700;
        }

        .filter-form {
            display: grid;
            grid-template-columns: minmax(260px, 1fr) auto auto;
            gap: 14px;
            align-items: center;
        }

        .search-box {
            position: relative;
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }

        .search-box input,
        .filter-form select {
            width: 100%;
            height: 46px;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            background: #ffffff;
            outline: none;
            font-family: inherit;
            font-size: 14px;
            color: var(--text);
        }

        .search-box input {
            padding: 0 14px 0 42px;
        }

        .filter-form select {
            padding: 0 13px;
        }

        .search-box input:focus,
        .filter-form select:focus {
            border-color: var(--coral);
            box-shadow: 0 0 0 4px rgba(217, 95, 74, 0.12);
        }

        .filter-btn,
        .reset-btn {
            height: 46px;
            padding: 0 16px;
            border-radius: 14px;
            border: none;
            font-family: inherit;
            font-size: 14px;
            font-weight: 900;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            cursor: pointer;
            transition: 0.25s ease;
        }

        .filter-btn {
            background: var(--coral);
            color: #ffffff;
        }

        .reset-btn {
            background: #f3f4f6;
            color: #374151;
        }

        .filter-btn:hover,
        .reset-btn:hover,
        .view-btn:hover,
        .delete-btn:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
        }

        .reset-btn:hover {
            background: var(--coral-light);
            color: var(--coral);
        }

        .success-alert,
        .error-alert {
            margin-bottom: 20px;
            border-radius: 16px;
            padding: 14px 16px;
            font-size: 14px;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .success-alert {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #bbf7d0;
        }

        .error-alert {
            background: #fef2f2;
            color: var(--red);
            border: 1px solid #fecaca;
        }

        .table-wrap {
            overflow-x: auto;
        }

        .users-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 920px;
        }

        .users-table th {
            text-align: left;
            padding: 14px 12px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--border);
        }

        .users-table td {
            padding: 16px 12px;
            border-bottom: 1px solid #f1f1f1;
            vertical-align: middle;
            font-size: 14px;
            font-weight: 700;
        }

        .users-table tbody tr {
            transition: 0.2s ease;
        }

        .users-table tbody tr:hover {
            background: #fff7f4;
        }

        .user-main {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-name {
            font-weight: 900;
            color: var(--text);
        }

        .user-email {
            margin-top: 4px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            min-height: 30px;
            padding: 0 11px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 900;
        }

        .badge.admin {
            background: var(--coral-light);
            color: var(--coral);
        }

        .badge.user {
            background: #eff6ff;
            color: #2563eb;
        }

        .badge.current {
            background: #f3f4f6;
            color: #374151;
        }

        .action-group {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .view-btn,
        .delete-btn {
            min-height: 36px;
            padding: 0 12px;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            font-family: inherit;
            font-size: 12px;
            font-weight: 900;
            transition: 0.25s ease;
            display: inline-flex;
            align-items: center;
            gap: 7px;
        }

        .view-btn {
            background: #f3f4f6;
            color: #374151;
        }

        .view-btn:hover {
            background: var(--coral-light);
            color: var(--coral);
        }

        .delete-btn {
            background: #fef2f2;
            color: var(--red);
        }

        .empty-state {
            padding: 42px;
            text-align: center;
        }

        .empty-state i {
            color: var(--coral);
            font-size: 36px;
            margin-bottom: 12px;
        }

        .empty-state h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 900;
        }

        .empty-state p {
            margin: 8px 0 0;
            color: var(--muted);
            font-size: 14px;
            font-weight: 700;
        }

        .detail-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(17, 24, 39, 0.55);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 24px;
            z-index: 100;
        }

        .detail-modal-backdrop.show {
            display: flex;
        }

        .user-detail-modal {
            width: 100%;
            max-width: 820px;
            max-height: 90vh;
            overflow-y: auto;
            background: #ffffff;
            border-radius: 26px;
            padding: 28px;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.25);
        }

        .detail-modal-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 22px;
        }

        .detail-user-main {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .detail-avatar {
            width: 58px;
            height: 58px;
            border-radius: 18px;
            background: linear-gradient(135deg, var(--coral), var(--coral-dark));
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: 900;
        }

        .detail-user-main h2 {
            margin: 0;
            font-size: 25px;
            font-weight: 900;
        }

        .detail-user-main p {
            margin: 5px 0 0;
            color: var(--muted);
            font-size: 14px;
            font-weight: 700;
        }

        .detail-close {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            border: none;
            background: #f3f4f6;
            color: #374151;
            cursor: pointer;
            transition: 0.25s ease;
        }

        .detail-close:hover {
            transform: translateY(-3px);
            background: #fef2f2;
            color: var(--red);
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .detail-card {
            background: #fafafa;
            border: 1px solid #f0f0f0;
            border-radius: 20px;
            padding: 18px;
            transition: 0.25s ease;
        }

        .detail-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow);
            background: #fff7f4;
            border-color: #ffd6cf;
        }

        .detail-card.wide {
            grid-column: 1 / -1;
        }

        .detail-card h3 {
            margin: 0 0 14px;
            font-size: 16px;
            font-weight: 900;
            color: var(--text);
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .detail-card h3 i {
            color: var(--coral);
        }

        .detail-list {
            display: grid;
            gap: 11px;
        }

        .detail-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            font-size: 14px;
        }

        .detail-row span {
            color: var(--muted);
            font-weight: 800;
        }

        .detail-row strong {
            color: var(--text);
            font-weight: 900;
            text-align: right;
        }

        .detail-stat-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
        }

        .detail-stat {
            background: #ffffff;
            border: 1px solid #eeeeee;
            border-radius: 16px;
            padding: 14px;
            text-align: center;
        }

        .detail-stat span {
            display: block;
            color: var(--muted);
            font-size: 12px;
            font-weight: 800;
        }

        .detail-stat strong {
            display: block;
            margin-top: 5px;
            color: var(--text);
            font-size: 21px;
            font-weight: 900;
        }

        .budget-progress {
            height: 10px;
            background: #f3f4f6;
            border-radius: 999px;
            overflow: hidden;
            margin-top: 12px;
        }

        .budget-progress-fill {
            height: 100%;
            width: 0%;
            background: linear-gradient(135deg, var(--coral), var(--coral-dark));
            border-radius: 999px;
            transition: 0.3s ease;
        }

        @media (max-width: 1200px) {
            .user-stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .filter-form {
                grid-template-columns: 1fr 1fr;
            }

            .search-box {
                grid-column: 1 / -1;
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
            .filter-top {
                flex-direction: column;
                align-items: flex-start;
            }

            .header-actions,
            .profile-wrap,
            .profile-btn {
                width: 100%;
            }

            .user-stats,
            .filter-form,
            .detail-grid {
                grid-template-columns: 1fr;
            }

            .search-box,
            .detail-card.wide {
                grid-column: auto;
            }

            .detail-stat-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
        /* Compact popup for admin account view */
.user-detail-modal.admin-compact {
    max-width: 620px;
    padding: 26px;
}

.user-detail-modal.admin-compact .detail-grid {
    grid-template-columns: 1fr;
}

.user-detail-modal.admin-compact .detail-card {
    padding: 18px 20px;
}

.user-detail-modal.admin-compact .detail-card.wide {
    grid-column: auto;
}

.user-detail-modal.admin-compact .detail-row {
    align-items: flex-start;
}

.user-detail-modal.admin-compact .detail-row strong {
    max-width: 260px;
    line-height: 1.4;
}

.user-detail-modal.admin-compact .detail-card:hover #adminOnlyCard {
    background: #fafafa;
    border-color: #f0f0f0;
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

            <a href="{{ route('admin.users') }}" class="admin-nav-link active">
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
                <p class="admin-eyebrow">User Management</p>
                <h1>Users</h1>
                <span>View registered couples, manage accounts, and monitor wedding planning activity.</span>
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

        @if (session('success'))
            <div class="success-alert">
                <i class="fa-solid fa-circle-check"></i>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="error-alert">
                <i class="fa-solid fa-triangle-exclamation"></i>
                {{ session('error') }}
            </div>
        @endif

        <section class="user-stats">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-regular fa-user"></i>
                </div>

                <div>
                    <span>Total Accounts</span>
                    <strong>{{ $totalUsers }}</strong>
                </div>
            </div>

            

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-heart"></i>
                </div>

                <div>
                    <span>Users</span>
                    <strong>{{ $regularUsers }}</strong>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-regular fa-calendar-plus"></i>
                </div>

                <div>
                    <span>Recent Signups</span>
                    <strong>{{ $recentUsers }}</strong>
                </div>
            </div>
        </section>

        <section class="filter-card">
            <div class="filter-top">
                <div>
                    <h2>Search & Filter</h2>
                    <p>Find users by name, email, or account role.</p>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.users') }}" class="filter-form" id="userSearchForm">
    <div class="search-box">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input
            type="text"
            name="search"
            id="userSearchInput"
            value="{{ request('search') }}"
            placeholder="Search users..."
            autocomplete="off"
        >
    </div>

    <a href="{{ route('admin.users') }}" class="reset-btn" id="userSearchReset">
        <i class="fa-solid fa-rotate-left"></i>
        Reset
    </a>
</form>
        </section>

        <section class="users-panel">
            <div class="filter-top">
                <div>
                    <h2>User List</h2>
                    <p id="userFoundText">
    {{ $users->count() }} account{{ $users->count() === 1 ? '' : 's' }} found.
</p>
                </div>
            </div>

            <div class="table-wrap">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>Joined</th>
                            <th>Last Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody id="usersTableBody">
                        @forelse ($users as $user)
                            @php
                                $details = $userDetails[$user->id] ?? [];
                            @endphp

                            <tr>
                                <td>
                                    <div class="user-main">
                                        <div class="user-avatar">
                                            {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                        </div>

                                        <div>
                                            <div class="user-name">{{ $user->name }}</div>
                                            <div class="user-email">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    @if (($user->role ?? 'user') === 'admin')
                                        <span class="badge admin">
                                            <i class="fa-solid fa-user-shield"></i>
                                            Admin
                                        </span>
                                    @else
                                        <span class="badge user">
                                            <i class="fa-regular fa-user"></i>
                                            User
                                        </span>
                                    @endif
                                </td>

                                <td>{{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</td>

                                <td>{{ $user->updated_at ? $user->updated_at->format('d M Y') : '-' }}</td>

                                <td>
                                    <div class="action-group">
                                        <button
                                            type="button"
                                            class="view-btn"
                                            onclick="openUserDetails({{ \Illuminate\Support\Js::from($details) }})"
                                        >
                                            <i class="fa-regular fa-eye"></i>
                                            View
                                        </button>

                                        @if (($user->role ?? 'user') !== 'admin' && auth()->id() !== $user->id)
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete this user account?')">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="delete-btn">
                                                    <i class="fa-solid fa-trash"></i>
                                                    Delete
                                                </button>
                                            </form>
                                        @else
                                            <span class="badge current">
                                                {{ auth()->id() === $user->id ? 'Current Admin' : 'Protected Admin' }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <i class="fa-regular fa-user"></i>
                                        <h3>No users found</h3>
                                        <p>Try changing your search or filter.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <div class="detail-modal-backdrop" id="userDetailsModal">
        <div class="user-detail-modal" id="userDetailCard">
            <div class="detail-modal-head">
                <div class="detail-user-main">
                    <div class="detail-avatar" id="detailAvatar">U</div>

                    <div>
                        <h2 id="detailName">User Name</h2>
                        <p id="detailEmail">user@email.com</p>
                    </div>
                </div>

                <button type="button" class="detail-close" onclick="closeUserDetails()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="detail-grid">
                <div class="detail-card">
                    <h3>
                        <i class="fa-regular fa-user"></i>
                        Account Details
                    </h3>

                    <div class="detail-list">
                        <div class="detail-row">
                            <span>Role</span>
                            <strong id="detailRole">-</strong>
                        </div>

                        <div class="detail-row">
                            <span>Joined</span>
                            <strong id="detailJoined">-</strong>
                        </div>

                        <div class="detail-row">
                            <span>Last Updated</span>
                            <strong id="detailUpdated">-</strong>
                        </div>
                    </div>
                </div>
                <div class="detail-card wide" id="adminOnlyCard" style="display: none;">
    <h3>
        <i class="fa-solid fa-shield-halved"></i>
        Admin Account Information
    </h3>

    <div class="detail-list">
        <div class="detail-row">
            <span>Account Type</span>
            <strong id="detailAdminType">Administrator Account</strong>
        </div>

        <div class="detail-row">
            <span>Access Level</span>
            <strong id="detailAdminAccess">Full admin panel access</strong>
        </div>

        <div class="detail-row">
            <span>Responsibility</span>
            <strong id="detailAdminResponsibility">Manage users, vendors, and system data</strong>
        </div>
    </div>
</div>
                <div class="detail-card" id="weddingDetailsCard">
                    <h3>
                        <i class="fa-solid fa-heart"></i>
                        Wedding Details
                    </h3>

                    <div class="detail-list">
                        <div class="detail-row">
                            <span>Partner Name</span>
                            <strong id="detailPartner">-</strong>
                        </div>

                        <div class="detail-row">
                            <span>Wedding Date</span>
                            <strong id="detailWeddingDate">-</strong>
                        </div>

                        <div class="detail-row">
                            <span>Venue</span>
                            <strong id="detailVenue">-</strong>
                        </div>

                        <div class="detail-row">
                            <span>Theme</span>
                            <strong id="detailTheme">-</strong>
                        </div>

                        <div class="detail-row">
                            <span>Guests</span>
                            <strong id="detailGuests">-</strong>
                        </div>
                    </div>
                </div>

                <div class="detail-card wide" id="planningSummaryCard">
                    <h3>
                        <i class="fa-solid fa-chart-simple"></i>
                        Planning Summary
                    </h3>

                    <div class="detail-stat-grid">
                        <div class="detail-stat">
                            <span>Total Tasks</span>
                            <strong id="detailTotalTasks">0</strong>
                        </div>

                        <div class="detail-stat">
                            <span>Completed</span>
                            <strong id="detailCompletedTasks">0</strong>
                        </div>

                        <div class="detail-stat">
                            <span>Pending</span>
                            <strong id="detailPendingTasks">0</strong>
                        </div>

                        <div class="detail-stat">
                            <span>Overdue</span>
                            <strong id="detailOverdueTasks">0</strong>
                        </div>
                    </div>
                </div>

                <div class="detail-card wide" id="budgetVendorCard">
                    <h3>
                        <i class="fa-solid fa-wallet"></i>
                        Budget & Vendors
                    </h3>

                    <div class="detail-list">
                        <div class="detail-row">
                            <span>Total Budget</span>
                            <strong id="detailTotalBudget">RM 0.00</strong>
                        </div>

                        <div class="detail-row">
                            <span>Total Spent</span>
                            <strong id="detailTotalSpent">RM 0.00</strong>
                        </div>

                        <div class="detail-row">
                            <span>Remaining</span>
                            <strong id="detailRemaining">RM 0.00</strong>
                        </div>

                        <div class="detail-row">
                            <span>Pending Expenses</span>
                            <strong id="detailPendingExpenses">0</strong>
                        </div>

                        <div class="detail-row">
                            <span>Vendors Added</span>
                            <strong id="detailVendorsAdded">0</strong>
                        </div>

                        <div class="budget-progress">
                            <div class="budget-progress-fill" id="detailBudgetProgress"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const profileWrap = document.getElementById('adminProfileWrap');
        const profileBtn = document.getElementById('adminProfileBtn');

        if (profileWrap && profileBtn) {
            profileBtn.addEventListener('click', function (event) {
                event.stopPropagation();
                profileWrap.classList.toggle('open');
            });

            document.addEventListener('click', function (event) {
                if (!profileWrap.contains(event.target)) {
                    profileWrap.classList.remove('open');
                }
            });
        }

        const userDetailsModal = document.getElementById('userDetailsModal');

        function formatMoney(amount) {
            const value = Number(amount || 0);

            return 'RM ' + value.toLocaleString('en-MY', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function setText(id, value) {
            const element = document.getElementById(id);

            if (element) {
                element.textContent = value ?? '-';
            }
        }

    function openUserDetails(details) {
    details = details || {};

    const modalCard = document.getElementById('userDetailCard');
    const initial = details.name ? details.name.charAt(0).toUpperCase() : 'U';

    setText('detailAvatar', initial);
    setText('detailName', details.name || 'User');
    setText('detailEmail', details.email || '-');

    setText('detailRole', details.role || '-');
    setText('detailJoined', details.joined || '-');
    setText('detailUpdated', details.updated || '-');

    const isAdmin = details.is_admin === true;

    const adminOnlyCard = document.getElementById('adminOnlyCard');
    const weddingDetailsCard = document.getElementById('weddingDetailsCard');
    const planningSummaryCard = document.getElementById('planningSummaryCard');
    const budgetVendorCard = document.getElementById('budgetVendorCard');

    if (isAdmin) {
        modalCard.classList.add('admin-compact');

        if (adminOnlyCard) adminOnlyCard.style.display = 'block';
        if (weddingDetailsCard) weddingDetailsCard.style.display = 'none';
        if (planningSummaryCard) planningSummaryCard.style.display = 'none';
        if (budgetVendorCard) budgetVendorCard.style.display = 'none';

        setText('detailAdminType', details.admin_details?.account_type || 'Administrator Account');
        setText('detailAdminAccess', details.admin_details?.access_level || 'Full admin panel access');
        setText('detailAdminResponsibility', details.admin_details?.responsibility || 'Manage users, vendors, and system data');
    } else {
        modalCard.classList.remove('admin-compact');

        if (adminOnlyCard) adminOnlyCard.style.display = 'none';
        if (weddingDetailsCard) weddingDetailsCard.style.display = 'block';
        if (planningSummaryCard) planningSummaryCard.style.display = 'block';
        if (budgetVendorCard) budgetVendorCard.style.display = 'block';

        setText('detailPartner', details.wedding?.partner_name || '-');
        setText('detailWeddingDate', details.wedding?.wedding_date || '-');
        setText('detailVenue', details.wedding?.venue || '-');
        setText('detailTheme', details.wedding?.theme || '-');
        setText('detailGuests', details.wedding?.guest_count || '-');

        setText('detailTotalTasks', details.tasks?.total || 0);
        setText('detailCompletedTasks', details.tasks?.completed || 0);
        setText('detailPendingTasks', details.tasks?.pending || 0);
        setText('detailOverdueTasks', details.tasks?.overdue || 0);

        setText('detailTotalBudget', formatMoney(details.budget?.total_budget));
        setText('detailTotalSpent', formatMoney(details.budget?.total_spent));
        setText('detailRemaining', formatMoney(details.budget?.remaining));
        setText('detailPendingExpenses', details.budget?.pending_expenses || 0);
        setText('detailVendorsAdded', details.vendors?.added || 0);

        const totalBudget = Number(details.budget?.total_budget || 0);
        const totalSpent = Number(details.budget?.total_spent || 0);
        const percentage = totalBudget > 0 ? Math.min((totalSpent / totalBudget) * 100, 100) : 0;

        const progress = document.getElementById('detailBudgetProgress');

        if (progress) {
            progress.style.width = percentage + '%';
        }
    }

    userDetailsModal.classList.add('show');
}


        function closeUserDetails() {
            userDetailsModal.classList.remove('show');
        }

        if (userDetailsModal) {
            userDetailsModal.addEventListener('click', function (event) {
                if (event.target === userDetailsModal) {
                    closeUserDetails();
                }
            });
        }
        const userSearchInput = document.getElementById('userSearchInput');
    const usersTableBody = document.getElementById('usersTableBody');
    const userFoundText = document.getElementById('userFoundText');
    const userSearchReset = document.getElementById('userSearchReset');

    let userSearchTimer = null;

    function updateUsersTable() {
        const searchValue = userSearchInput.value.trim();

        const url = new URL("{{ route('admin.users') }}", window.location.origin);

        if (searchValue.length > 0) {
            url.searchParams.set('search', searchValue);
        }

        fetch(url.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                const newTableBody = doc.getElementById('usersTableBody');
                const newFoundText = doc.getElementById('userFoundText');

                if (newTableBody && usersTableBody) {
                    usersTableBody.innerHTML = newTableBody.innerHTML;
                }

                if (newFoundText && userFoundText) {
                    userFoundText.innerHTML = newFoundText.innerHTML;
                }

                const cleanUrl = searchValue.length > 0
                    ? `${window.location.pathname}?search=${encodeURIComponent(searchValue)}`
                    : window.location.pathname;

                window.history.replaceState({}, '', cleanUrl);
            })
            .catch(error => {
                console.error('User search failed:', error);
            });
    }

    if (userSearchInput) {
        userSearchInput.addEventListener('input', function () {
            clearTimeout(userSearchTimer);

            userSearchTimer = setTimeout(function () {
                updateUsersTable();
            }, 250);
        });
    }

    if (userSearchReset) {
        userSearchReset.addEventListener('click', function (event) {
            event.preventDefault();

            userSearchInput.value = '';
            updateUsersTable();
        });
    }
    </script>
</body>
</html>
