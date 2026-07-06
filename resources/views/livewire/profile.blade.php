<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app')] class extends Component
{
    public $user = null;

    public string $name = '';

    public string $email = '';

    public string $current_password = '';

    public string $new_password = '';

    public string $new_password_confirmation = '';

    public bool $dark_mode = false;

    public function mount(): void
    {
        $this->user = Auth::user();

        $this->name = $this->user?->name ?? '';
        $this->email = $this->user?->email ?? '';
        $this->dark_mode = ($this->user?->theme_mode ?? 'light') === 'dark';
    }

    public function updateProfile(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'regex:/^[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}$/',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->user->id),
            ],
        ], [
            'email.regex' => 'Please enter a valid email address, like name@example.com.',
        ]);

        $this->user->update([
            'name' => $this->name,
            'email' => $this->email,
        ]);

        $this->user = Auth::user()->fresh();

        session()->flash('profile_success', 'Profile updated successfully.');
    }

    public function updatePassword(): void
    {
        $this->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $this->user->update([
            'password' => Hash::make($this->new_password),
        ]);

        $this->current_password = '';
        $this->new_password = '';
        $this->new_password_confirmation = '';

        session()->flash('password_success', 'Password updated successfully.');
    }

    public function updateTheme(): void
    {
        $this->user->update([
            'theme_mode' => $this->dark_mode ? 'dark' : 'light',
        ]);

        $this->user = Auth::user()->fresh();

        session()->flash('theme_success', 'Theme updated successfully.');

        $this->dispatch('theme-updated', mode: $this->user->theme_mode);
    }
};

?>

@php
    $weddingDetail = \App\Models\WeddingDetail::where('user_id', $user?->id)->first();

    $partnerName = $weddingDetail?->partner_name ?? 'Not set';
    $weddingDate = $weddingDetail?->wedding_date ?? $user?->wedding_date ?? null;
    $venue = $weddingDetail?->venue ?? 'Not set';
    $theme = $weddingDetail?->theme ?? 'Not set';
    $totalBudget = $weddingDetail?->total_budget ?? $user?->budget ?? 0;

    $initial = strtoupper(substr($user?->name ?? 'U', 0, 1));
    $themeMode = ($user?->theme_mode ?? 'light') === 'dark' ? 'Dark' : 'Light';
@endphp

<div class="profile-page-wrapper">

    <aside class="sidebar">
        <div class="logo">
            <i class="fa-solid fa-heart"></i>
            <span>Jodoh Together</span>
        </div>

        <nav class="nav-menu">
            <a href="{{ url('dashboard') }}" class="nav-link" wire:navigate>
                <i class="fa-solid fa-house"></i> Dashboard
            </a>

            <a href="{{ url('my/wedding') }}" class="nav-link" wire:navigate>
                <i class="fa-regular fa-calendar-check"></i> My Wedding
            </a>

            <a href="{{ url('tasks') }}" class="nav-link" wire:navigate>
                <i class="fa-regular fa-square-check"></i> Tasks
            </a>

            <a href="{{ url('budget') }}" class="nav-link" wire:navigate>
                <i class="fa-solid fa-dollar-sign"></i> Budget
            </a>

            <a href="{{ url('vendors') }}" class="nav-link" wire:navigate>
                <i class="fa-solid fa-store"></i> Vendors
            </a>

            <a href="{{ url('calendar') }}" class="nav-link" wire:navigate>
                <i class="fa-regular fa-calendar"></i> Calendar
            </a>
        </nav>

        <form method="POST" action="{{ route('logout') }}" class="logout-form">
            @csrf
            <button type="submit" class="nav-link logout-link">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
            </button>
        </form>
    </aside>

    <main class="main-content">
        <header>
            <div class="page-title">
                <h1>My Profile</h1>
                <p>Manage your account information and profile settings.</p>
            </div>

            <div class="header-right">
                @include('components.app-notifications')

                <div class="profile-wrap" id="profileWrap">
                    <button class="profile-btn" id="profileBtn" type="button" aria-haspopup="true" aria-expanded="false">
                        <div class="avatar-initials">
                            {{ $initial }}
                        </div>

                        <span>{{ $user?->name ?? 'User' }}</span>
                        <i class="fa-solid fa-chevron-down chevron"></i>
                    </button>

                    <div class="profile-dropdown">
                        <div class="profile-summary">
                            <strong>{{ $user?->name ?? 'User' }}</strong>
                            <span>{{ $user?->email ?? 'No email' }}</span>
                        </div>

                        <a href="{{ url('profile') }}" class="dropdown-link active" wire:navigate>
                            <i class="fa-regular fa-user"></i> My Profile
                        </a>

                        

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-link logout">
                                <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <section class="profile-hero">
            <div class="profile-avatar-large">
                {{ $initial }}
            </div>

            <div class="profile-hero-info">
                <p class="eyebrow">Account Profile</p>
                <h2>{{ $user?->name ?? 'User' }}</h2>
                <span>{{ $user?->email ?? 'No email' }}</span>
            </div>

            <div class="profile-hero-badge">
                <i class="fa-solid fa-user-check"></i>
                Active Account
            </div>
        </section>

        <section class="profile-grid">
            <div class="profile-card">
                <div class="card-header">
                    <div>
                        <h3>Personal Information</h3>
                        <p>Update your account name and email address.</p>
                    </div>

                    <div class="card-icon">
                        <i class="fa-regular fa-user"></i>
                    </div>
                </div>

                @if (session('profile_success'))
                    <div class="success-message">
                        {{ session('profile_success') }}
                    </div>
                @endif

                <form wire:submit.prevent="updateProfile" class="profile-form">
                    <div class="form-group full-line">
                        <label>Name</label>
                        <input type="text" wire:model="name" placeholder="Enter your name">
                        @error('name') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group full-line">
                        <label>Email Address</label>
                        <input type="email" wire:model="email" placeholder="Enter your email"
                            pattern="[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}"
                            title="Please enter a valid email address, like name@example.com.">
                        @error('email') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="save-btn" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="updateProfile">
                                Save Changes
                            </span>
                            <span wire:loading wire:target="updateProfile">
                                Saving...
                            </span>
                        </button>
                    </div>
                </form>
            </div>

            <div class="profile-card">
                <div class="card-header">
                    <div>
                        <h3>Change Password</h3>
                        <p>Use a strong password to keep your account safe.</p>
                    </div>

                    <div class="card-icon">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                </div>

                @if (session('password_success'))
                    <div class="success-message">
                        {{ session('password_success') }}
                    </div>
                @endif

                <form wire:submit.prevent="updatePassword" class="profile-form">
                    <div class="form-group full-line">
                        <label>Current Password</label>
                        <input type="password" wire:model="current_password" placeholder="Enter current password">
                        @error('current_password') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group full-line">
                        <label>New Password</label>
                        <input type="password" wire:model="new_password" placeholder="Minimum 8 characters">
                        @error('new_password') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group full-line">
                        <label>Confirm New Password</label>
                        <input type="password" wire:model="new_password_confirmation" placeholder="Confirm new password">
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="save-btn" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="updatePassword">
                                Update Password
                            </span>
                            <span wire:loading wire:target="updatePassword">
                                Updating...
                            </span>
                        </button>
                    </div>
                </form>
            </div>

            

            <div class="profile-card wedding-summary-card">
                <div class="card-header">
                    <div>
                        <h3>Wedding Summary</h3>
                        <p>Your wedding information from My Wedding page.</p>
                    </div>

                    <div class="card-icon">
                        <i class="fa-solid fa-heart"></i>
                    </div>
                </div>

                <div class="summary-list">
                    <div class="summary-item">
                        <div>
                            <span>Partner Name</span>
                            <strong>{{ $partnerName }}</strong>
                        </div>
                        <i class="fa-regular fa-user"></i>
                    </div>

                    <div class="summary-item">
                        <div>
                            <span>Wedding Date</span>
                            <strong>
                                {{ $weddingDate ? \Carbon\Carbon::parse($weddingDate)->format('d M Y') : 'Not set' }}
                            </strong>
                        </div>
                        <i class="fa-regular fa-calendar"></i>
                    </div>

                    <div class="summary-item">
                        <div>
                            <span>Venue</span>
                            <strong>{{ $venue }}</strong>
                        </div>
                        <i class="fa-solid fa-location-dot"></i>
                    </div>

                    <div class="summary-item">
                        <div>
                            <span>Theme</span>
                            <strong>{{ $theme }}</strong>
                        </div>
                        <i class="fa-solid fa-palette"></i>
                    </div>

                    <div class="summary-item">
                        <div>
                            <span>Total Budget</span>
                            <strong>RM {{ number_format($totalBudget, 0) }}</strong>
                        </div>
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                </div>

                <a href="{{ url('my/wedding') }}" class="outline-btn" wire:navigate>
                    Edit Wedding Details
                </a>
            </div>
        </section>
    </main>

    <script>
        function initProfileDropdown() {
            const profileWrap = document.getElementById("profileWrap");
            const profileBtn = document.getElementById("profileBtn");

            if (!profileWrap || !profileBtn || profileBtn.dataset.ready === "true") {
                return;
            }

            profileBtn.dataset.ready = "true";

            profileBtn.addEventListener("click", (event) => {
                event.stopPropagation();
                const isOpen = profileWrap.classList.toggle("open");
                profileBtn.setAttribute("aria-expanded", isOpen);
            });

            document.addEventListener("click", (event) => {
                if (!profileWrap.contains(event.target)) {
                    profileWrap.classList.remove("open");
                    profileBtn.setAttribute("aria-expanded", "false");
                }
            });

            document.addEventListener("keydown", (event) => {
                if (event.key === "Escape") {
                    profileWrap.classList.remove("open");
                    profileBtn.setAttribute("aria-expanded", "false");
                }
            });
        }

        document.addEventListener("DOMContentLoaded", initProfileDropdown);
        document.addEventListener("livewire:navigated", initProfileDropdown);

        document.addEventListener("theme-updated", (event) => {
            const mode = event.detail?.mode === "dark" ? "dark" : "light";
            document.body.dataset.theme = mode;
        });
    </script>

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
            --green: #15803d;
            --green-bg: #dcfce7;
            --red: #dc2626;
            --shadow: 0 12px 35px rgba(31, 41, 55, 0.07);
            --shadow-hover: 0 16px 42px rgba(31, 41, 55, 0.1);
            --radius: 20px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: var(--bg);
            color: var(--text);
        }

        .profile-page-wrapper {
            display: flex;
            min-height: 100vh;
            width: 100%;
            background: var(--bg);
        }

        .sidebar {
            width: 275px;
            min-height: 100vh;
            background: var(--dark);
            color: #ffffff;
            padding: 30px 22px;
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
            flex-shrink: 0;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: 26px;
            font-weight: 800;
            line-height: 1.05;
            margin-bottom: 46px;
        }

        .logo i {
            color: #e5654f;
            font-size: 30px;
        }

        .nav-menu {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 15px 16px;
            border-radius: 16px;
            color: #c9cbd1;
            text-decoration: none;
            font-size: 16px;
            transition: 0.2s ease;
        }

        .nav-link i {
            width: 22px;
            text-align: center;
            font-size: 18px;
        }

        .nav-link:hover,
        .nav-link.active {
            background: #df6048;
            color: #ffffff;
        }

        .logout-form {
            margin-top: auto;
        }

        .logout-link {
            width: 100%;
            border: none;
            background: none;
            text-align: left;
            cursor: pointer;
            font-family: inherit;
        }

        .main-content {
            flex: 1;
            min-width: 0;
            padding: 44px 48px 60px;
            background: var(--bg);
            overflow-x: hidden;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 24px;
            margin-bottom: 28px;
        }

        .page-title h1 {
            margin: 0 0 8px;
            font-size: 38px;
            font-weight: 900;
            color: var(--text);
            letter-spacing: -0.7px;
        }

        .page-title p {
            margin: 0;
            color: var(--muted);
            font-size: 16px;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 18px;
            flex-wrap: wrap;
        }

        .profile-wrap {
            position: relative;
        }

        .profile-btn {
            border: none;
            background: #ffffff;
            border-radius: 999px;
            padding: 7px 12px 7px 7px;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            font-weight: 700;
            color: var(--text);
            font-family: inherit;
        }

        .avatar-initials {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--coral), #f5b4a8);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            flex-shrink: 0;
        }

        .profile-dropdown {
            position: absolute;
            top: 56px;
            right: 0;
            width: 230px;
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: 0 18px 45px rgba(0, 0, 0, 0.12);
            padding: 12px;
            display: none;
            z-index: 20;
        }

        .profile-wrap.open .profile-dropdown {
            display: block;
        }

        .profile-summary {
            padding: 12px;
            border-bottom: 1px solid #f0f0f0;
            margin-bottom: 8px;
        }

        .profile-summary strong,
        .profile-summary span {
            display: block;
        }

        .profile-summary span {
            color: var(--muted);
            font-size: 13px;
            margin-top: 4px;
        }

        .dropdown-link {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            padding: 11px 12px;
            border-radius: 10px;
            color: #374151;
            text-decoration: none;
            border: none;
            background: none;
            font-family: inherit;
            font-size: 14px;
            cursor: pointer;
        }

        .dropdown-link:hover,
        .dropdown-link.active {
            background: var(--coral-light);
            color: var(--coral);
        }

        .dropdown-link.logout {
            color: #e3342f;
        }

        .profile-hero {
            min-height: 170px;
            border-radius: 26px;
            background: linear-gradient(135deg, var(--coral), var(--coral-dark));
            color: #ffffff;
            padding: 28px;
            display: flex;
            align-items: center;
            gap: 22px;
            margin-bottom: 26px;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: 0.25s ease;
        }

        .profile-hero::after {
            content: "";
            position: absolute;
            right: -70px;
            top: -80px;
            width: 240px;
            height: 240px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.12);
        }

        .profile-hero:hover,
        .profile-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
        }

        .profile-avatar-large {
            width: 92px;
            height: 92px;
            min-width: 92px;
            border-radius: 28px;
            background: #ffffff;
            color: var(--coral);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 42px;
            font-weight: 900;
            position: relative;
            z-index: 2;
        }

        .profile-hero-info {
            position: relative;
            z-index: 2;
            min-width: 0;
        }

        .eyebrow {
            margin: 0 0 6px;
            color: inherit;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            opacity: 0.9;
        }

        .profile-hero h2 {
            margin: 0 0 6px;
            color: #ffffff;
            font-size: 34px;
            font-weight: 900;
        }

        .profile-hero span {
            color: #ffffff;
            opacity: 0.9;
            font-weight: 700;
        }

        .profile-hero-badge {
            margin-left: auto;
            background: rgba(255, 255, 255, 0.18);
            border-radius: 999px;
            padding: 10px 14px;
            color: #ffffff;
            font-weight: 900;
            position: relative;
            z-index: 2;
            white-space: nowrap;
        }

        .profile-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            gap: 26px;
            align-items: start;
        }

        .profile-card {
            background: rgba(255, 255, 255, 0.97);
            border: 1px solid rgba(225, 225, 225, 0.9);
            border-radius: var(--radius);
            padding: 26px;
            box-shadow: var(--shadow);
            transition: 0.25s ease;
        }

        .wedding-summary-card {
            grid-column: 1 / -1;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 18px;
            margin-bottom: 22px;
        }

        .card-header h3 {
            margin: 0 0 6px;
            color: var(--text);
            font-size: 22px;
            font-weight: 900;
        }

        .card-header p {
            margin: 0;
            color: var(--muted);
            font-size: 14px;
            font-weight: 700;
        }

        .card-icon {
            width: 50px;
            height: 50px;
            min-width: 50px;
            border-radius: 16px;
            background: var(--coral-light);
            color: var(--coral);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .success-message {
            background: var(--green-bg);
            color: var(--green);
            border: 1px solid #bbf7d0;
            padding: 12px 14px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 800;
            margin-bottom: 18px;
        }

        .profile-form {
            display: grid;
            gap: 16px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .form-group label {
            font-size: 13px;
            font-weight: 800;
            color: #374151;
        }

        .form-group input {
            width: 100%;
            height: 44px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 0 14px;
            font-size: 14px;
            outline: none;
            background: #ffffff;
            font-family: inherit;
        }

        .form-group input:focus {
            border-color: var(--coral);
            box-shadow: 0 0 0 4px rgba(217, 95, 74, 0.12);
        }

        .error-msg {
            color: var(--red);
            font-size: 12px;
            font-weight: 700;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 4px;
        }

        .save-btn,
        .outline-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            padding: 12px 20px;
            font-family: inherit;
            font-weight: 900;
            cursor: pointer;
            text-decoration: none;
            transition: 0.2s ease;
        }

        .save-btn {
            border: none;
            background: var(--coral);
            color: #ffffff;
            box-shadow: 0 12px 28px rgba(217, 95, 74, 0.18);
        }

        .save-btn:hover {
            background: #c94f3d;
        }

        .outline-btn {
            border: 1px solid var(--coral);
            background: #ffffff;
            color: var(--coral);
            width: fit-content;
            margin-top: 20px;
        }

        .outline-btn:hover {
            background: var(--coral);
            color: #ffffff;
        }

        .summary-list {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .summary-item {
            border: 1px solid #eeeeee;
            background: #fafafa;
            border-radius: 16px;
            padding: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            transition: 0.25s ease;
        }

        .summary-item:hover {
            background: #fff7f4;
            border-color: #ffd6cf;
            transform: translateY(-2px);
        }

        .summary-item span {
            display: block;
            color: var(--muted);
            font-size: 12px;
            font-weight: 800;
            margin-bottom: 5px;
        }

        .summary-item strong {
            display: block;
            color: var(--text);
            font-size: 15px;
            font-weight: 900;
            word-break: break-word;
        }

        .summary-item i {
            color: var(--coral);
            font-size: 18px;
        }

        @media (max-width: 1050px) {
            .profile-grid,
            .summary-list {
                grid-template-columns: 1fr;
            }

            .wedding-summary-card {
                grid-column: auto;
            }
        }

        @media (max-width: 900px) {
            .profile-page-wrapper {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                min-height: auto;
                position: static;
            }

            .nav-menu {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
            }

            .main-content {
                padding: 30px 22px 50px;
            }

            header {
                flex-direction: column;
            }
        }

        @media (max-width: 700px) {
            .nav-menu {
                grid-template-columns: 1fr;
            }

            .profile-hero {
                flex-direction: column;
                align-items: flex-start;
            }

            .profile-hero-badge {
                margin-left: 0;
            }

            .form-actions,
            .save-btn,
            .outline-btn,
            .header-right {
                width: 100%;
            }
        }
    </style>
</div>
