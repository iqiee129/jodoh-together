<?php

use function Livewire\Volt\layout;
use function Livewire\Volt\state;
use App\Models\User;
use App\Models\WeddingDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

layout('layouts.app');

state([
    'currentStep' => 1,

    'name' => '',
    'email' => '',
    'password' => '',
    'password_confirmation' => '',

    'partner_name' => '',
    'wedding_date' => '',
    'venue' => '',
    'theme' => '',
    'estimated_guests' => '',
    'total_budget' => '',
]);

$nextStep = function () {
    $this->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'regex:/^[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}$/', 'max:255', 'unique:users,email'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ], [
        'email.regex' => 'Please enter a valid email address, like name@example.com.',
    ]);

    $this->currentStep = 2;
};

$previousStep = function () {
    $this->currentStep = 1;
};

$register = function () {
    $this->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'regex:/^[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}$/', 'max:255', 'unique:users,email'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],

        'partner_name' => ['nullable', 'string', 'max:255'],
        'wedding_date' => ['nullable', 'date'],
        'venue' => ['nullable', 'string', 'max:255'],
        'theme' => ['nullable', 'string', 'max:255'],
        'estimated_guests' => ['nullable', 'integer', 'min:0'],
        'total_budget' => ['nullable', 'numeric', 'min:0'],
    ], [
        'email.regex' => 'Please enter a valid email address, like name@example.com.',
    ]);

    $user = User::create([
        'name' => $this->name,
        'email' => $this->email,
        'password' => Hash::make($this->password),
    ]);

    if (Schema::hasTable('wedding_details')) {
        $weddingData = [
            'user_id' => $user->id,
            'partner_name' => $this->partner_name ?: null,
            'wedding_date' => $this->wedding_date ?: null,
            'venue' => $this->venue ?: null,
            'theme' => $this->theme ?: null,
            'total_budget' => $this->total_budget ?: 0,
        ];

        if (Schema::hasColumn('wedding_details', 'estimated_guests')) {
            $weddingData['estimated_guests'] = $this->estimated_guests ?: 0;
        }

        if (Schema::hasColumn('wedding_details', 'guest_count')) {
            $weddingData['guest_count'] = $this->estimated_guests ?: 0;
        }

        WeddingDetail::create($weddingData);
    }

    Auth::login($user);

    request()->session()->regenerate();

    return redirect()->route('dashboard');
};

?>

<div class="auth-page">
    <div class="auth-overlay"></div>

    <div class="auth-shell">
        <div class="brand-title">
            <div class="brand-icon">
                <i class="fa-solid fa-heart"></i>
            </div>

            <div>
                <h1>Jodoh Together</h1>
                <p>Your wedding planning companion</p>
            </div>
        </div>

        <div class="auth-card register-card">
            <div class="auth-right">
                <div>
                    <p class="side-eyebrow">Already Registered?</p>
                    <h3>Welcome back to your wedding planner</h3>
                    <p>Login to continue managing your wedding details, budget, tasks, vendors, and calendar.</p>

                    <a href="{{ route('login') }}" class="outline-btn" wire:navigate>
                        Login
                    </a>
                </div>
            </div>

            <div class="auth-left">
                <p class="eyebrow">
                    {{ $currentStep === 1 ? 'Start Planning' : 'Wedding Setup' }}
                </p>

                <h2>Sign Up</h2>

                <p class="auth-subtitle">
                    {{ $currentStep === 1
                        ? 'Create your account first. Wedding details will be added in the next step.'
                        : 'Add your wedding details so your dashboard is ready from the start.' }}
                </p>

                <form wire:submit.prevent="register" class="auth-form">
                    <div class="step-indicator">
                        <div class="step-item {{ $currentStep === 1 ? 'active' : '' }}">
                            <span>1</span>
                            <p>Account</p>
                        </div>

                        <div class="step-line"></div>

                        <div class="step-item {{ $currentStep === 2 ? 'active' : '' }}">
                            <span>2</span>
                            <p>Wedding</p>
                        </div>
                    </div>

                    @if ($currentStep === 1)
                        @if (\Illuminate\Support\Facades\Route::has('google.redirect'))
                            <a href="{{ route('google.redirect') }}" class="google-btn">
                                <i class="fa-brands fa-google"></i>
                                Continue with Google
                            </a>

                            <div class="divider">
                                <span>or sign up with email</span>
                            </div>
                        @endif

                        <div class="form-group">
                            <label>Full Name</label>
                            <div class="input-wrap">
                                <i class="fa-regular fa-user"></i>
                                <input type="text" wire:model="name" placeholder="Enter your name">
                            </div>
                            @error('name') <span class="error-msg">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label>Email Address</label>
                            <div class="input-wrap">
                                <i class="fa-regular fa-envelope"></i>
                                <input type="email" wire:model="email" placeholder="Enter your email"
                                    pattern="[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}"
                                    title="Please enter a valid email address, like name@example.com.">
                            </div>
                            @error('email') <span class="error-msg">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label>Password</label>
                            <div class="input-wrap">
                                <i class="fa-solid fa-lock"></i>
                                <input type="password" wire:model="password" placeholder="Minimum 8 characters">
                            </div>
                            @error('password') <span class="error-msg">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label>Confirm Password</label>
                            <div class="input-wrap">
                                <i class="fa-solid fa-lock"></i>
                                <input type="password" wire:model="password_confirmation" placeholder="Confirm password">
                            </div>
                        </div>

                        <button type="button" class="main-btn" wire:click="nextStep" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="nextStep">
                                Continue
                                <i class="fa-solid fa-arrow-right"></i>
                            </span>

                            <span wire:loading wire:target="nextStep">
                                Checking...
                            </span>
                        </button>
                    @endif

                    @if ($currentStep === 2)
                        <div class="form-row">
                            <div class="form-group">
                                <label>Partner Name</label>
                                <div class="input-wrap">
                                    <i class="fa-solid fa-heart"></i>
                                    <input type="text" wire:model="partner_name" placeholder="Partner name">
                                </div>
                                @error('partner_name') <span class="error-msg">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Wedding Date</label>
                                <div class="input-wrap">
                                    <i class="fa-regular fa-calendar"></i>
                                    <input type="date" wire:model="wedding_date">
                                </div>
                                @error('wedding_date') <span class="error-msg">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Venue</label>
                            <div class="input-wrap">
                                <i class="fa-solid fa-location-dot"></i>
                                <input type="text" wire:model="venue" placeholder="Wedding venue">
                            </div>
                            @error('venue') <span class="error-msg">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label>Wedding Theme</label>
                            <div class="input-wrap">
                                <i class="fa-solid fa-palette"></i>
                                <input type="text" wire:model="theme" placeholder="Classic, garden, modern...">
                            </div>
                            @error('theme') <span class="error-msg">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Estimated Guests</label>
                                <div class="input-wrap">
                                    <i class="fa-solid fa-users"></i>
                                    <input type="number" wire:model="estimated_guests" placeholder="Example: 300" min="0">
                                </div>
                                @error('estimated_guests') <span class="error-msg">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Total Budget</label>
                                <div class="input-wrap">
                                    <i class="fa-solid fa-wallet"></i>
                                    <input type="number" wire:model="total_budget" placeholder="Example: 20000" min="0" step="0.01">
                                </div>
                                @error('total_budget') <span class="error-msg">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="step-actions">
                            <button type="button" class="secondary-btn" wire:click="previousStep">
                                <i class="fa-solid fa-arrow-left"></i>
                                Back
                            </button>

                            <button type="submit" class="main-btn" wire:loading.attr="disabled">
                                <span wire:loading.remove>Create Account</span>
                                <span wire:loading>Creating...</span>
                            </button>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <style>
        :root {
            --bg: #f7f3ef;
            --dark: #1b1c22;
            --text: #111827;
            --muted: #6b7280;
            --coral: #d95f4a;
            --coral-dark: #b94e3e;
            --coral-light: #fff1ee;
            --red: #dc2626;
            --shadow: 0 25px 80px rgba(0, 0, 0, 0.25);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
        }

        .auth-page {
            min-height: 100vh;
            width: 100vw;
            margin-left: calc(50% - 50vw);
            margin-right: calc(50% - 50vw);
            position: relative;
            background:
                linear-gradient(rgba(17, 24, 39, 0.45), rgba(17, 24, 39, 0.45)),
                url('{{ asset('images/auth-bg.jpg') }}');
            background-size: cover;
            background-position: center;
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 34px;
            overflow-x: hidden;
        }

        .auth-overlay {
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at top left, rgba(217, 95, 74, 0.24), transparent 35%),
                radial-gradient(circle at bottom right, rgba(255, 255, 255, 0.22), transparent 32%);
            pointer-events: none;
        }

        .auth-shell {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 1080px;
        }

        .brand-title {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            color: #ffffff;
            margin-bottom: 22px;
        }

        .brand-icon {
            width: 54px;
            height: 54px;
            border-radius: 18px;
            background: linear-gradient(135deg, var(--coral), var(--coral-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 23px;
            box-shadow: 0 14px 35px rgba(217, 95, 74, 0.3);
        }

        .brand-title h1 {
            margin: 0;
            font-size: 32px;
            font-weight: 900;
            letter-spacing: -0.04em;
        }

        .brand-title p {
            margin: 4px 0 0;
            font-size: 14px;
            font-weight: 700;
            opacity: 0.9;
        }

        .auth-card {
            display: grid;
            grid-template-columns: 0.92fr 1.08fr;
            background: rgba(255, 255, 255, 0.96);
            border-radius: 28px;
            overflow: hidden;
            box-shadow: var(--shadow);
            backdrop-filter: blur(12px);
            min-height: 630px;
        }

        .auth-left {
            padding: 42px 46px;
            background: rgba(255, 255, 255, 0.97);
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .auth-right {
            background: linear-gradient(135deg, #6f3d2a, #4a271b);
            color: #ffffff;
            padding: 54px 46px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .eyebrow {
            margin: 0 0 8px;
            color: var(--coral);
            font-size: 13px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.12em;
        }

        .auth-left h2 {
            margin: 0;
            color: var(--text);
            font-size: 38px;
            font-weight: 900;
            letter-spacing: -0.05em;
        }

        .auth-subtitle {
            margin: 10px 0 24px;
            color: var(--muted);
            font-size: 15px;
            font-weight: 700;
            line-height: 1.5;
        }

        .auth-form {
            display: grid;
            gap: 14px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
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

        .input-wrap {
            position: relative;
        }

        .input-wrap i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            pointer-events: none;
        }

        .input-wrap input {
            width: 100%;
            height: 48px;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 0 16px 0 45px;
            font-size: 14px;
            outline: none;
            font-family: inherit;
            color: var(--text);
            background: #ffffff;
        }

        .input-wrap input:focus {
            border-color: var(--coral);
            box-shadow: 0 0 0 4px rgba(217, 95, 74, 0.12);
        }

        .input-wrap input[type="date"] {
            color: var(--text);
        }

        .main-btn,
        .secondary-btn,
        .google-btn {
            height: 50px;
            border-radius: 16px;
            font-size: 15px;
            font-weight: 900;
            cursor: pointer;
            transition: 0.25s ease;
            font-family: inherit;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            text-decoration: none;
        }

        .main-btn {
            border: none;
            background: var(--coral);
            color: #ffffff;
            box-shadow: 0 14px 30px rgba(217, 95, 74, 0.26);
            width: 100%;
        }

        .main-btn:hover {
            background: #c94f3d;
            transform: translateY(-3px);
            box-shadow: 0 18px 40px rgba(217, 95, 74, 0.32);
        }

        .secondary-btn {
            border: none;
            background: #f3f4f6;
            color: #374151;
        }

        .secondary-btn:hover {
            background: var(--coral-light);
            color: var(--coral);
            transform: translateY(-3px);
        }

        .google-btn {
            width: 100%;
            border: 1px solid #e5e7eb;
            background: #ffffff;
            color: var(--text);
        }

        .google-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 30px rgba(31, 41, 55, 0.12);
            border-color: var(--coral);
            color: var(--coral);
        }

        .google-btn i {
            font-size: 17px;
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 800;
            margin: 2px 0;
        }

        .divider::before,
        .divider::after {
            content: "";
            height: 1px;
            background: #e5e7eb;
            flex: 1;
        }

        .step-indicator {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 4px;
        }

        .step-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--muted);
            font-size: 13px;
            font-weight: 900;
        }

        .step-item span {
            width: 28px;
            height: 28px;
            border-radius: 10px;
            background: #f3f4f6;
            color: var(--muted);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .step-item.active {
            color: var(--coral);
        }

        .step-item.active span {
            background: var(--coral);
            color: #ffffff;
        }

        .step-item p {
            margin: 0;
        }

        .step-line {
            flex: 1;
            height: 2px;
            background: #eeeeee;
        }

        .step-actions {
            display: grid;
            grid-template-columns: 0.7fr 1fr;
            gap: 12px;
        }

        .side-eyebrow {
            margin: 0 0 10px;
            font-size: 13px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            opacity: 0.85;
        }

        .auth-right h3 {
            margin: 0;
            font-size: 30px;
            font-weight: 900;
            letter-spacing: -0.04em;
        }

        .auth-right p {
            margin: 14px 0 26px;
            color: rgba(255, 255, 255, 0.84);
            font-size: 15px;
            font-weight: 700;
            line-height: 1.6;
        }

        .outline-btn {
            height: 46px;
            padding: 0 28px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.85);
            color: #ffffff;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 900;
            transition: 0.25s ease;
        }

        .outline-btn:hover {
            background: #ffffff;
            color: #5d3223;
            transform: translateY(-3px);
        }

        .error-msg {
            color: var(--red);
            font-size: 12px;
            font-weight: 800;
        }

        @media (max-width: 900px) {
            .auth-page {
                padding: 24px;
            }

            .auth-card {
                grid-template-columns: 1fr;
                min-height: auto;
            }

            .auth-right {
                order: 2;
                padding: 34px 28px;
            }

            .auth-left {
                order: 1;
                padding: 34px 28px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .brand-title h1 {
                font-size: 27px;
            }

            .auth-left h2 {
                font-size: 32px;
            }
        }
    </style>
</div>
