<?php

use function Livewire\Volt\layout;
use function Livewire\Volt\state;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

layout('layouts.app');

state([
    'email' => '',
    'password' => '',
    'remember' => false,
]);

$login = function () {
    $this->validate([
        'email' => ['required', 'email', 'regex:/^[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}$/'],
        'password' => ['required', 'string'],
    ], [
        'email.regex' => 'Please enter a valid email address, like name@example.com.',
    ]);

    $credentials = [
        'email' => $this->email,
        'password' => $this->password,
    ];

    $rateLimitKey = 'admin-login:' . strtolower($this->email) . '|' . request()->ip();

    if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
        $this->addError('email', 'Too many login attempts. Please try again later.');
        return;
    }

    if (! Auth::attempt($credentials, $this->remember)) {
        RateLimiter::hit($rateLimitKey, 60);

        $this->addError('email', 'Invalid email or password.');
        return;
    }

    request()->session()->regenerate();

    if (Auth::user()->role !== 'admin') {
        RateLimiter::hit($rateLimitKey, 60);

        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        $this->addError('email', 'This account is not allowed to access admin panel.');
        return;
    }

    RateLimiter::clear($rateLimitKey);

    return redirect()->route('admin.dashboard');
};

?>

<div class="admin-login-page">
    <div class="login-card">
        <div class="brand">
            <div class="brand-icon">
                <i class="fa-solid fa-heart"></i>
            </div>

            <div>
                <h1>Jodoh Admin</h1>
                <p>System Panel Login</p>
            </div>
        </div>

        <div class="login-heading">
            <p class="eyebrow">Administrator Access</p>
            <h2>Welcome Back</h2>
            <p>Login to manage users, vendors, and system data.</p>
        </div>

        @if (session('status'))
            <div class="success-box">
                <i class="fa-solid fa-circle-check"></i>
                {{ session('status') }}
            </div>
        @endif

        <form wire:submit.prevent="login" class="login-form">
            <div class="form-group">
                <label>Email Address</label>
                <div class="input-wrap">
                    <i class="fa-regular fa-envelope"></i>
                    <input type="email" wire:model="email" placeholder="admin@gmail.com"
                        pattern="[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}"
                        title="Please enter a valid email address, like name@example.com.">
                </div>
                @error('email') <span class="error-msg">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label>Password</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" wire:model="password" placeholder="Enter admin password">
                </div>
                @error('password') <span class="error-msg">{{ $message }}</span> @enderror
            </div>

            <div class="login-options">
                

                <a href="{{ route('password.request', ['from' => 'admin']) }}" class="forgot-link" wire:navigate>
                    Forgot Password?
                </a>
            </div>

            <button type="submit" class="login-btn" wire:loading.attr="disabled" wire:target="login">
                <span wire:loading.remove wire:target="login">
                    Login as Admin
                    <i class="fa-solid fa-arrow-right"></i>
                </span>

                <span wire:loading wire:target="login">
                    Logging in...
                </span>
            </button>
        </form>

        <div class="back-link">
            <a href="{{ route('login') }}" wire:navigate>
                <i class="fa-solid fa-arrow-left"></i>
                Back to User Login
            </a>
        </div>
    </div>

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
            --green-bg: #ecfdf5;
            --shadow: 0 16px 45px rgba(31, 41, 55, 0.12);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: var(--bg);
        }

        .admin-login-page {
            min-height: 100vh;
            width: 100vw;
            margin-left: calc(50% - 50vw);
            margin-right: calc(50% - 50vw);
            background:
                radial-gradient(circle at top right, rgba(217, 95, 74, 0.20), transparent 34%),
                radial-gradient(circle at bottom left, rgba(185, 78, 62, 0.12), transparent 32%),
                var(--bg);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px;
            font-family: 'Inter', sans-serif;
        }

        .login-card {
            width: 100%;
            max-width: 480px;
            background: #ffffff;
            border: 1px solid rgba(225, 225, 225, 0.9);
            border-radius: 30px;
            padding: 36px;
            box-shadow: var(--shadow);
            transition: 0.25s ease;
        }

        .login-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 22px 60px rgba(31, 41, 55, 0.16);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 30px;
        }

        .brand-icon {
            width: 60px;
            height: 60px;
            border-radius: 19px;
            background: linear-gradient(135deg, var(--coral), var(--coral-dark));
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 25px;
            box-shadow: 0 14px 34px rgba(217, 95, 74, 0.28);
        }

        .brand h1 {
            margin: 0;
            color: var(--text);
            font-size: 27px;
            font-weight: 950;
            line-height: 1.1;
            letter-spacing: -0.04em;
        }

        .brand p {
            margin: 5px 0 0;
            color: var(--muted);
            font-size: 14px;
            font-weight: 800;
        }

        .login-heading {
            margin-bottom: 24px;
        }

        .eyebrow {
            margin: 0 0 8px;
            color: var(--coral);
            font-size: 12px;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: 0.12em;
        }

        .login-heading h2 {
            margin: 0 0 8px;
            font-size: 34px;
            font-weight: 950;
            color: var(--text);
            letter-spacing: -0.05em;
        }

        .login-heading p:not(.eyebrow) {
            margin: 0;
            color: var(--muted);
            font-size: 15px;
            font-weight: 750;
            line-height: 1.5;
        }

        .success-box {
            margin-bottom: 18px;
            background: var(--green-bg);
            color: var(--green);
            border-radius: 16px;
            padding: 13px 14px;
            font-size: 13px;
            font-weight: 850;
            display: flex;
            align-items: center;
            gap: 8px;
            line-height: 1.4;
        }

        .login-form {
            display: grid;
            gap: 17px;
        }

        .form-group {
            display: grid;
            gap: 8px;
        }

        .form-group label {
            font-size: 13px;
            font-weight: 900;
            color: #374151;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            pointer-events: none;
        }

        .input-wrap input {
            width: 100%;
            height: 50px;
            border: 1px solid #e5e7eb;
            border-radius: 15px;
            padding: 0 14px 0 43px;
            font-size: 14px;
            outline: none;
            font-family: inherit;
            color: var(--text);
            background: #ffffff;
            transition: 0.2s ease;
        }

        .input-wrap input:focus {
            border-color: var(--coral);
            box-shadow: 0 0 0 4px rgba(217, 95, 74, 0.12);
        }

        .login-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: -2px;
        }

        .remember-line {
            display: flex;
            align-items: center;
            gap: 9px;
            color: #374151;
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
        }

        .remember-line input {
            width: 16px;
            height: 16px;
            accent-color: var(--coral);
        }

        .forgot-link {
            color: var(--coral);
            font-size: 13px;
            font-weight: 900;
            text-decoration: none;
            white-space: nowrap;
        }

        .forgot-link:hover {
            color: var(--coral-dark);
            text-decoration: underline;
        }

        .error-msg {
            color: var(--red);
            font-size: 12px;
            font-weight: 800;
        }

        .login-btn {
            width: 100%;
            height: 52px;
            border: none;
            border-radius: 16px;
            background: var(--coral);
            color: #ffffff;
            font-size: 15px;
            font-weight: 950;
            cursor: pointer;
            box-shadow: 0 14px 32px rgba(217, 95, 74, 0.26);
            transition: 0.22s ease;
            font-family: inherit;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
        }

        .login-btn:hover {
            background: #c94f3d;
            transform: translateY(-2px);
            box-shadow: 0 18px 42px rgba(217, 95, 74, 0.32);
        }

        .login-btn:disabled {
            opacity: 0.75;
            cursor: not-allowed;
            transform: none;
        }

        .back-link {
            margin-top: 24px;
            padding-top: 22px;
            border-top: 1px solid #f0f0f0;
            text-align: center;
        }

        .back-link a {
            color: var(--coral);
            text-decoration: none;
            font-size: 14px;
            font-weight: 900;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .back-link a:hover {
            color: var(--coral-dark);
            text-decoration: underline;
        }

        @media (max-width: 600px) {
            .admin-login-page {
                padding: 20px;
            }

            .login-card {
                padding: 28px;
                border-radius: 26px;
            }

            .login-heading h2 {
                font-size: 29px;
            }

            .login-options {
                align-items: flex-start;
                flex-direction: column;
            }

            .forgot-link {
                align-self: flex-end;
            }
        }
    </style>
</div>
