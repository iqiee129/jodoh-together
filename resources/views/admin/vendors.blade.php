@php
    $admin = auth()->user();
    $adminInitial = strtoupper(substr($admin?->name ?? 'A', 0, 1));

    $categories = [
        'photography' => 'Photography',
        'venue' => 'Venue',
        'catering' => 'Catering',
        'decoration' => 'Decoration',
        'makeup' => 'Makeup',
        'attire' => 'Attire',
        'entertainment' => 'Entertainment',
        'transportation' => 'Transportation',
        'invitation' => 'Invitation',
        'others' => 'Others',
    ];

    $states = ['Penang', 'Kedah', 'Perlis', 'Perak'];
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jodoh Together | Admin Vendors</title>

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
            --green: #16a34a;
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
        .vendor-avatar {
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

        .vendor-stats {
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
        .vendors-panel:hover {
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
        .vendors-panel {
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
        .vendors-panel h2 {
            margin: 0;
            font-size: 21px;
            font-weight: 900;
        }

        .filter-top p,
        .vendors-panel p {
            margin: 6px 0 0;
            color: var(--muted);
            font-size: 13px;
            font-weight: 700;
        }

        .add-btn {
            height: 44px;
            padding: 0 17px;
            border-radius: 14px;
            border: none;
            background: var(--coral);
            color: #ffffff;
            font-size: 14px;
            font-weight: 900;
            display: inline-flex;
            align-items: center;
            gap: 9px;
            cursor: pointer;
            transition: 0.25s ease;
            font-family: inherit;
            text-decoration: none;
        }

        .add-btn:hover,
        .small-btn:hover,
        .icon-btn:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
        }

        .filter-form {
            display: grid;
            grid-template-columns: minmax(260px, 2fr) 1fr 1fr 1fr auto;
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
        .filter-form select,
        .form-group input,
        .form-group select,
        .form-group textarea {
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
        .filter-form select:focus,
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: var(--coral);
            box-shadow: 0 0 0 4px rgba(217, 95, 74, 0.12);
        }

        .reset-btn {
            height: 46px;
            padding: 0 16px;
            border-radius: 14px;
            background: #f3f4f6;
            color: #374151;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 900;
            text-decoration: none;
            transition: 0.25s ease;
        }

        .reset-btn:hover {
            background: var(--coral-light);
            color: var(--coral);
            transform: translateY(-3px);
        }

        .success-alert {
            margin-bottom: 20px;
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #bbf7d0;
            border-radius: 16px;
            padding: 14px 16px;
            font-size: 14px;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .table-wrap {
            overflow-x: auto;
        }

        .vendors-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 980px;
        }

        .vendors-table th {
            text-align: left;
            padding: 14px 12px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--border);
        }

        .vendors-table td {
            padding: 16px 12px;
            border-bottom: 1px solid #f1f1f1;
            vertical-align: middle;
            font-size: 14px;
            font-weight: 700;
        }

        .vendors-table tbody tr {
            transition: 0.2s ease;
        }

        .vendors-table tbody tr:hover {
            background: #fff7f4;
        }

        .vendor-main {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .vendor-image {
            width: 54px;
            height: 54px;
            border-radius: 16px;
            object-fit: cover;
            background: var(--coral-light);
        }

        .vendor-name {
            font-weight: 900;
            color: var(--text);
        }

        .vendor-desc {
            margin-top: 4px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
            max-width: 260px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
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

        .badge.category {
            background: var(--coral-light);
            color: var(--coral);
        }

        .badge.active {
            background: #ecfdf5;
            color: #047857;
        }

        .badge.inactive {
            background: #fef2f2;
            color: var(--red);
        }

        .action-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .icon-btn,
        .small-btn {
            border: none;
            cursor: pointer;
            font-family: inherit;
            transition: 0.25s ease;
        }

        .icon-btn {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #f3f4f6;
            color: #374151;
        }

        .icon-btn.edit:hover {
            background: var(--coral-light);
            color: var(--coral);
        }

        .icon-btn.delete:hover {
            background: #fef2f2;
            color: var(--red);
        }

        .small-btn {
            min-height: 36px;
            padding: 0 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 900;
            background: #f3f4f6;
            color: #374151;
        }

        .small-btn.active-toggle {
            background: #ecfdf5;
            color: #047857;
        }

        .small-btn.inactive-toggle {
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

        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(17, 24, 39, 0.55);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 24px;
            z-index: 100;
        }

        .modal-backdrop.show {
            display: flex;
        }

        .vendor-modal {
            width: 100%;
            max-width: 760px;
            max-height: 90vh;
            overflow-y: auto;
            background: #ffffff;
            border-radius: 24px;
            padding: 26px;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.25);
        }

        .modal-head {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: flex-start;
            margin-bottom: 22px;
        }

        .modal-head h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 900;
        }

        .modal-head p {
            margin: 6px 0 0;
            color: var(--muted);
            font-size: 14px;
            font-weight: 700;
        }

        .modal-close {
            width: 40px;
            height: 40px;
            border-radius: 13px;
            border: none;
            background: #f3f4f6;
            color: #374151;
            cursor: pointer;
            transition: 0.25s ease;
        }

        .modal-close:hover {
            transform: translateY(-3px);
            background: #fef2f2;
            color: var(--red);
        }

        .vendor-form {
            display: grid;
            gap: 16px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .form-group {
            display: grid;
            gap: 8px;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        .form-group label {
            color: #374151;
            font-size: 13px;
            font-weight: 900;
        }

        .form-group textarea {
            height: 96px;
            resize: vertical;
            padding: 13px;
        }

        .form-group input,
        .form-group select {
            padding: 0 13px;
        }

        .form-help {
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
        }

        .form-group input[type="file"] {
            padding: 11px 13px;
            height: auto;
            cursor: pointer;
        }

        .form-group input[type="file"]::file-selector-button {
            border: none;
            border-radius: 10px;
            background: var(--coral-light);
            color: var(--coral);
            padding: 8px 12px;
            margin-right: 12px;
            font-family: inherit;
            font-size: 12px;
            font-weight: 900;
            cursor: pointer;
        }

        .checkbox-line {
            display: flex;
            align-items: center;
            gap: 9px;
            color: #374151;
            font-size: 14px;
            font-weight: 800;
        }

        .checkbox-line input {
            width: 17px;
            height: 17px;
            accent-color: var(--coral);
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 8px;
        }

        .cancel-btn,
        .save-btn {
            height: 44px;
            padding: 0 18px;
            border-radius: 14px;
            border: none;
            font-family: inherit;
            font-size: 14px;
            font-weight: 900;
            cursor: pointer;
            transition: 0.25s ease;
        }

        .cancel-btn {
            background: #f3f4f6;
            color: #374151;
        }

        .save-btn {
            background: var(--coral);
            color: #ffffff;
        }

        .cancel-btn:hover,
        .save-btn:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
        }

        .error-msg {
            color: var(--red);
            font-size: 12px;
            font-weight: 800;
        }

        @media (max-width: 1200px) {
            .filter-form {
                grid-template-columns: 1fr 1fr;
            }

            .search-box {
                grid-column: 1 / -1;
            }

            .vendor-stats {
                grid-template-columns: 1fr;
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

            .filter-form,
            .form-grid {
                grid-template-columns: 1fr;
            }

            .search-box {
                grid-column: auto;
            }

            .modal-actions {
                flex-direction: column;
            }

            .cancel-btn,
            .save-btn {
                width: 100%;
            }
        }
        .image-uploader {
    border: 1px dashed #e5e7eb;
    border-radius: 18px;
    padding: 16px;
    background: #fafafa;
    transition: 0.25s ease;
}

.image-uploader.dragging {
    border-color: var(--coral);
    background: #fff7f4;
    box-shadow: 0 0 0 4px rgba(217, 95, 74, 0.12);
}

.upload-preview {
    min-height: 190px;
    border-radius: 16px;
    background: #ffffff;
    border: 1px solid #eeeeee;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.upload-preview img {
    width: 100%;
    height: 220px;
    object-fit: cover;
    display: none;
}

.upload-placeholder {
    text-align: center;
    color: var(--muted);
    padding: 24px;
}

.upload-placeholder i {
    width: 54px;
    height: 54px;
    border-radius: 17px;
    background: var(--coral-light);
    color: var(--coral);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    margin-bottom: 12px;
}

.upload-placeholder strong {
    display: block;
    color: var(--text);
    font-size: 15px;
    font-weight: 900;
}

.upload-placeholder span {
    display: block;
    margin-top: 5px;
    font-size: 13px;
    font-weight: 700;
}

.upload-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 14px;
    flex-wrap: wrap;
}

.upload-btn,
.remove-image-btn {
    min-height: 40px;
    padding: 0 14px;
    border-radius: 13px;
    border: none;
    font-size: 13px;
    font-weight: 900;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    transition: 0.25s ease;
    font-family: inherit;
}

.upload-btn {
    background: var(--coral);
    color: #ffffff;
}

.remove-image-btn {
    background: #fef2f2;
    color: var(--red);
}

.upload-btn:hover,
.remove-image-btn:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-hover);
}

.hidden-file-input {
    display: none;
}

.image-url-box {
    margin-top: 14px;
    display: grid;
    gap: 8px;
}

.image-url-box label {
    color: #374151;
    font-size: 13px;
    font-weight: 900;
}

.form-help {
    color: var(--muted);
    font-size: 12px;
    font-weight: 700;
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

            <a href="{{ route('admin.vendors') }}" class="admin-nav-link active">
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
                <p class="admin-eyebrow">Vendor Management</p>
                <h1>Vendors</h1>
                <span>Add, edit, publish, and manage wedding service vendors.</span>
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

        <section class="vendor-stats">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-store"></i>
                </div>

                <div>
                    <span>Total Vendors</span>
                    <strong>{{ $totalVendors }}</strong>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-circle-check"></i>
                </div>

                <div>
                    <span>Active Vendors</span>
                    <strong>{{ $activeVendors }}</strong>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-circle-xmark"></i>
                </div>

                <div>
                    <span>Inactive Vendors</span>
                    <strong>{{ $inactiveVendors }}</strong>
                </div>
            </div>
        </section>

        <section class="filter-card">
            <div class="filter-top">
                <div>
                    <h2>Search & Filter</h2>
                    <p>Find vendors by name, category, state, or publication status.</p>
                </div>

                <button type="button" class="add-btn" onclick="openAddModal()">
                    <i class="fa-solid fa-plus"></i>
                    Add Vendor
                </button>
            </div>

            <form method="GET" action="{{ route('admin.vendors') }}" class="filter-form" id="vendorFilterForm">
    <div class="search-box">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input
            type="text"
            name="search"
            id="vendorSearchInput"
            value="{{ request('search') }}"
            placeholder="Search vendors..."
            autocomplete="off"
        >
    </div>

    <select name="category" id="vendorCategoryFilter">
        <option value="all">All Categories</option>
        @foreach ($categories as $key => $label)
            <option value="{{ $key }}" @selected(request('category') === $key)>{{ $label }}</option>
        @endforeach
    </select>

    <select name="state" id="vendorStateFilter">
        <option value="all">All States</option>
        @foreach ($states as $state)
            <option value="{{ $state }}" @selected(request('state') === $state)>{{ $state }}</option>
        @endforeach
    </select>

    <select name="status" id="vendorStatusFilter">
        <option value="all">All Status</option>
        <option value="active" @selected(request('status') === 'active')>Active</option>
        <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
    </select>

    <a href="{{ route('admin.vendors') }}" class="reset-btn" id="vendorSearchReset">
        <i class="fa-solid fa-rotate-left"></i>
        Reset
    </a>
</form>
        </section>

        <section class="vendors-panel">
            <div class="filter-top">
                <div>
                    <h2>Vendor List</h2>
                    <p id="vendorFoundText">
    {{ $vendors->count() }} vendor{{ $vendors->count() === 1 ? '' : 's' }} found.
</p>
                </div>
            </div>

            <div class="table-wrap">
                <table class="vendors-table">
                    <thead>
                        <tr>
                            <th>Vendor</th>
                            <th>Category</th>
                            <th>State</th>
                            <th>Price</th>
                            <th>Contact</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody id="vendorsTableBody">
                        @forelse ($vendors as $vendor)
                            <tr>
                                <td>
                                    <div class="vendor-main">
                                        @if ($vendor->image_url)
                                            <img src="{{ $vendor->image_url }}" alt="{{ $vendor->name }}" class="vendor-image">
                                        @else
                                            <div class="vendor-avatar">
                                                {{ strtoupper(substr($vendor->name, 0, 1)) }}
                                            </div>
                                        @endif

                                        <div>
                                            <div class="vendor-name">{{ $vendor->name }}</div>
                                            <div class="vendor-desc">{{ $vendor->description ?: 'No description added.' }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <span class="badge category">
                                        {{ ucfirst($vendor->category) }}
                                    </span>
                                </td>

                                <td>{{ $vendor->state ?: '-' }}</td>

                                <td>RM {{ number_format($vendor->price ?? 0, 2) }}</td>

                                <td>
                                    <div>{{ $vendor->phone ?: '-' }}</div>
                                    <div style="color: var(--muted); font-size: 12px;">{{ $vendor->email ?: '-' }}</div>
                                </td>

                                <td>
                                    @if ($vendor->is_active)
                                        <span class="badge active">
                                            <i class="fa-solid fa-circle-check"></i>
                                            Active
                                        </span>
                                    @else
                                        <span class="badge inactive">
                                            <i class="fa-solid fa-circle-xmark"></i>
                                            Inactive
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <div class="action-group">
                                        <button
                                            type="button"
                                            class="icon-btn edit"
                                            onclick='openEditModal(@json($vendor))'
                                            title="Edit vendor"
                                        >
                                            <i class="fa-solid fa-pen"></i>
                                        </button>

                                        <form method="POST" action="{{ route('admin.vendors.toggle-status', $vendor) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="small-btn {{ $vendor->is_active ? 'active-toggle' : 'inactive-toggle' }}">
                                                {{ $vendor->is_active ? 'Active' : 'Inactive' }}
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.vendors.destroy', $vendor) }}" onsubmit="return confirm('Delete this vendor?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="icon-btn delete" title="Delete vendor">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="fa-solid fa-store-slash"></i>
                                        <h3>No vendors found</h3>
                                        <p>Add a new vendor or adjust your filters.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <div class="modal-backdrop" id="vendorModal">
        <div class="vendor-modal">
            <div class="modal-head">
                <div>
                    <h2 id="modalTitle">Add Vendor</h2>
                    <p id="modalSubtitle">Create a new vendor for users to browse.</p>
                </div>

                <button type="button" class="modal-close" onclick="closeVendorModal()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form method="POST" id="vendorForm" action="{{ route('admin.vendors.store') }}" class="vendor-form" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div class="form-grid">
                    <div class="form-group">
                        <label>Vendor Name</label>
                        <input type="text" name="name" id="vendorName" value="{{ old('name') }}" required>
                        @error('name') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label>Category</label>
                        <select name="category" id="vendorCategory" required>
                            @foreach ($categories as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('category') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label>State</label>
                        <select name="state" id="vendorState" required>
                            @foreach ($states as $state)
                                <option value="{{ $state }}">{{ $state }}</option>
                            @endforeach
                        </select>
                        @error('state') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label>Location</label>
                        <select name="location" id="vendorLocation">
                            <option value="">Select location</option>
                        </select>
                        @error('location') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label>Price</label>
                        <input type="number" name="price" id="vendorPrice" min="0" step="0.01" required>
                        @error('price') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" id="vendorPhone">
                        @error('phone') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" id="vendorEmail"
                            pattern="[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}"
                            title="Please enter a valid email address, like name@example.com.">
                        @error('email') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group full">
    <label>Vendor Image</label>

    <input type="hidden" name="remove_image" id="removeImageInput" value="0">

    <div class="image-uploader" id="imageDropArea">
        <div class="upload-preview">
            <img src="" alt="Vendor image preview" id="imagePreviewImg">

            <div class="upload-placeholder" id="imagePlaceholder">
                <i class="fa-regular fa-image"></i>
                <strong>Drag & drop image here</strong>
                <span>or choose an image from your device</span>
            </div>
        </div>

        <div class="upload-actions">
            <label for="vendorImageUpload" class="upload-btn">
                <i class="fa-solid fa-upload"></i>
                Choose Image
            </label>

            <button type="button" class="remove-image-btn" onclick="removeVendorImage()">
                <i class="fa-solid fa-trash"></i>
                Remove Image
            </button>
        </div>

        <input
            type="file"
            name="image_upload"
            id="vendorImageUpload"
            class="hidden-file-input"
            accept="image/png,image/jpeg,image/jpg,image/webp"
        >

        <small class="form-help">
            Upload JPG, PNG, or WEBP. Maximum size 2MB.
        </small>

        @error('image_upload') <span class="error-msg">{{ $message }}</span> @enderror
    </div>

    <div class="image-url-box">
        <label>Or paste image URL</label>
        <input type="text" name="image_url" id="vendorImageUrl" placeholder="https://...">
        <small class="form-help">
            Use this only for online image links. Uploaded images will appear as preview above.
        </small>
        @error('image_url') <span class="error-msg">{{ $message }}</span> @enderror
    </div>
</div>

                    <div class="form-group full">
                        <label>Description</label>
                        <textarea name="description" id="vendorDescription" placeholder="Describe this vendor..."></textarea>
                        @error('description') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group full">
                        <label class="checkbox-line">
                            <input type="checkbox" name="is_active" id="vendorIsActive" checked>
                            <span>Publish vendor as active</span>
                        </label>
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="button" class="cancel-btn" onclick="closeVendorModal()">Cancel</button>
                    <button type="submit" class="save-btn">Save Vendor</button>
                </div>
            </form>
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

    const modal = document.getElementById('vendorModal');
    const vendorForm = document.getElementById('vendorForm');
    const formMethod = document.getElementById('formMethod');

    const imageDropArea = document.getElementById('imageDropArea');
    const imageUploadInput = document.getElementById('vendorImageUpload');
    const imagePreviewImg = document.getElementById('imagePreviewImg');
    const imagePlaceholder = document.getElementById('imagePlaceholder');
    const imageUrlInput = document.getElementById('vendorImageUrl');
    const removeImageInput = document.getElementById('removeImageInput');

    const locationsByState = {
        Penang: [
            'Georgetown',
            'Bayan Lepas',
            'Butterworth',
            'Bukit Mertajam',
            'Kepala Batas',
            'Nibong Tebal',
            'Balik Pulau',
            'Seberang Jaya'
        ],
        Kedah: [
            'Alor Setar',
            'Sungai Petani',
            'Kulim',
            'Langkawi',
            'Jitra',
            'Baling',
            'Yan',
            'Pendang'
        ],
        Perlis: [
            'Kangar',
            'Arau',
            'Padang Besar',
            'Kuala Perlis',
            'Simpang Empat',
            'Beseri'
        ],
        Perak: [
            'Ipoh',
            'Taiping',
            'Manjung',
            'Teluk Intan',
            'Kuala Kangsar',
            'Lumut',
            'Batu Gajah',
            'Kampar'
        ]
    };

    const vendorStateSelect = document.getElementById('vendorState');
    const vendorLocationSelect = document.getElementById('vendorLocation');

    function populateLocationOptions(state, selectedLocation = '') {
        const locations = locationsByState[state] || [];

        vendorLocationSelect.innerHTML = '<option value="">Select location</option>';

        locations.forEach(function (location) {
            const option = document.createElement('option');
            option.value = location;
            option.textContent = location;

            if (location === selectedLocation) {
                option.selected = true;
            }

            vendorLocationSelect.appendChild(option);
        });

        if (selectedLocation && !locations.includes(selectedLocation)) {
            const customOption = document.createElement('option');
            customOption.value = selectedLocation;
            customOption.textContent = selectedLocation;
            customOption.selected = true;
            vendorLocationSelect.appendChild(customOption);
        }
    }

    if (vendorStateSelect && vendorLocationSelect) {
        vendorStateSelect.addEventListener('change', function () {
            populateLocationOptions(this.value);
        });

        populateLocationOptions(vendorStateSelect.value || 'Penang');
    }

    function showImagePreview(src) {
        if (!src) {
            clearImagePreview(false);
            return;
        }

        imagePreviewImg.src = src;
        imagePreviewImg.style.display = 'block';
        imagePlaceholder.style.display = 'none';
    }

    function clearImagePreview(clearInputs = true) {
        imagePreviewImg.src = '';
        imagePreviewImg.style.display = 'none';
        imagePlaceholder.style.display = 'block';

        if (clearInputs) {
            imageUploadInput.value = '';
            imageUrlInput.value = '';
        }
    }

    function handleSelectedImage(file) {
        if (!file) {
            return;
        }

        if (!file.type.startsWith('image/')) {
            alert('Please choose an image file.');
            return;
        }

        removeImageInput.value = '0';
        imageUrlInput.value = '';

        const reader = new FileReader();

        reader.onload = function (event) {
            showImagePreview(event.target.result);
        };

        reader.readAsDataURL(file);
    }

    function removeVendorImage() {
        removeImageInput.value = '1';
        clearImagePreview(true);
    }

    if (imageUploadInput) {
        imageUploadInput.addEventListener('change', function () {
            handleSelectedImage(this.files[0]);
        });
    }

    if (imageUrlInput) {
        imageUrlInput.addEventListener('input', function () {
            removeImageInput.value = '0';
            imageUploadInput.value = '';

            if (this.value.trim()) {
                showImagePreview(this.value.trim());
            } else {
                clearImagePreview(false);
            }
        });
    }

    if (imageDropArea) {
        ['dragenter', 'dragover'].forEach(function (eventName) {
            imageDropArea.addEventListener(eventName, function (event) {
                event.preventDefault();
                imageDropArea.classList.add('dragging');
            });
        });

        ['dragleave', 'drop'].forEach(function (eventName) {
            imageDropArea.addEventListener(eventName, function (event) {
                event.preventDefault();
                imageDropArea.classList.remove('dragging');
            });
        });

        imageDropArea.addEventListener('drop', function (event) {
            const file = event.dataTransfer.files[0];

            if (file) {
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                imageUploadInput.files = dataTransfer.files;

                handleSelectedImage(file);
            }
        });
    }

    function openAddModal() {
        document.getElementById('modalTitle').textContent = 'Add Vendor';
        document.getElementById('modalSubtitle').textContent = 'Create a new vendor for users to browse.';

        vendorForm.action = "{{ route('admin.vendors.store') }}";
        formMethod.value = 'POST';

        document.getElementById('vendorName').value = '';
        document.getElementById('vendorCategory').value = 'photography';
        document.getElementById('vendorState').value = 'Penang';
        populateLocationOptions('Penang');
        document.getElementById('vendorPrice').value = '';
        document.getElementById('vendorPhone').value = '';
        document.getElementById('vendorEmail').value = '';
        document.getElementById('vendorDescription').value = '';
        document.getElementById('vendorIsActive').checked = true;

        removeImageInput.value = '0';
        clearImagePreview(true);

        modal.classList.add('show');
    }

    function openEditModal(vendor) {
        document.getElementById('modalTitle').textContent = 'Edit Vendor';
        document.getElementById('modalSubtitle').textContent = 'Update vendor details and visibility.';

        vendorForm.action = `/admin/vendors/${vendor.id}`;
        formMethod.value = 'PUT';

        const state = vendor.state || 'Penang';
        const location = vendor.location || '';
        const imageUrl = vendor.image_url || '';

        document.getElementById('vendorName').value = vendor.name ?? '';
        document.getElementById('vendorCategory').value = vendor.category ?? 'others';
        document.getElementById('vendorState').value = state;
        populateLocationOptions(state, location);
        document.getElementById('vendorPrice').value = vendor.price ?? 0;
        document.getElementById('vendorPhone').value = vendor.phone ?? '';
        document.getElementById('vendorEmail').value = vendor.email ?? '';
        document.getElementById('vendorDescription').value = vendor.description ?? '';
        document.getElementById('vendorIsActive').checked = Boolean(vendor.is_active);

        removeImageInput.value = '0';
        imageUploadInput.value = '';

        if (imageUrl.startsWith('/storage/')) {
            imageUrlInput.value = '';
            showImagePreview(imageUrl);
        } else {
            imageUrlInput.value = imageUrl;
            showImagePreview(imageUrl);
        }

        modal.classList.add('show');
    }

    function closeVendorModal() {
        modal.classList.remove('show');
    }

    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            closeVendorModal();
        }
    });
    const vendorFilterForm = document.getElementById('vendorFilterForm');
    const vendorSearchInput = document.getElementById('vendorSearchInput');
    const vendorCategoryFilter = document.getElementById('vendorCategoryFilter');
    const vendorStateFilter = document.getElementById('vendorStateFilter');
    const vendorStatusFilter = document.getElementById('vendorStatusFilter');
    const vendorSearchReset = document.getElementById('vendorSearchReset');
    const vendorsTableBody = document.getElementById('vendorsTableBody');
    const vendorFoundText = document.getElementById('vendorFoundText');

    let vendorSearchTimer = null;

    function updateVendorsTable() {
        const searchValue = vendorSearchInput.value.trim();
        const categoryValue = vendorCategoryFilter.value;
        const stateValue = vendorStateFilter.value;
        const statusValue = vendorStatusFilter.value;

        const url = new URL("{{ route('admin.vendors') }}", window.location.origin);

        if (searchValue.length > 0) {
            url.searchParams.set('search', searchValue);
        }

        if (categoryValue && categoryValue !== 'all') {
            url.searchParams.set('category', categoryValue);
        }

        if (stateValue && stateValue !== 'all') {
            url.searchParams.set('state', stateValue);
        }

        if (statusValue && statusValue !== 'all') {
            url.searchParams.set('status', statusValue);
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

                const newTableBody = doc.getElementById('vendorsTableBody');
                const newFoundText = doc.getElementById('vendorFoundText');

                if (newTableBody && vendorsTableBody) {
                    vendorsTableBody.innerHTML = newTableBody.innerHTML;
                }

                if (newFoundText && vendorFoundText) {
                    vendorFoundText.innerHTML = newFoundText.innerHTML;
                }

                const newUrl = url.search ? `${window.location.pathname}${url.search}` : window.location.pathname;
                window.history.replaceState({}, '', newUrl);
            })
            .catch(error => {
                console.error('Vendor search failed:', error);
            });
    }

    if (vendorFilterForm) {
        vendorFilterForm.addEventListener('submit', function (event) {
            event.preventDefault();
            updateVendorsTable();
        });
    }

    if (vendorSearchInput) {
        vendorSearchInput.addEventListener('input', function () {
            clearTimeout(vendorSearchTimer);

            vendorSearchTimer = setTimeout(function () {
                updateVendorsTable();
            }, 250);
        });
    }

    [vendorCategoryFilter, vendorStateFilter, vendorStatusFilter].forEach(function (filter) {
        if (filter) {
            filter.addEventListener('change', function () {
                updateVendorsTable();
            });
        }
    });

    if (vendorSearchReset) {
        vendorSearchReset.addEventListener('click', function (event) {
            event.preventDefault();

            vendorSearchInput.value = '';
            vendorCategoryFilter.value = 'all';
            vendorStateFilter.value = 'all';
            vendorStatusFilter.value = 'all';

            updateVendorsTable();
        });
    }
</script>
</body>
</html>
