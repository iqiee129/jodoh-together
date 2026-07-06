<?php

use function Livewire\Volt\layout;
use function Livewire\Volt\mount;
use function Livewire\Volt\state;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

layout('layouts.app');

state([
    'authMode' => 'login',

    'email' => '',
    'password' => '',
    'remember' => false,

    'name' => '',
    'register_email' => '',
    'register_password' => '',
    'register_password_confirmation' => '',
]);

mount(function () {
    if (request()->routeIs('register') || request()->query('mode') === 'register') {
        $this->authMode = 'register';
    }
});

$setMode = function (string $mode) {
    $this->resetValidation();

    $this->authMode = $mode === 'register' ? 'register' : 'login';
};

$login = function () {
    $credentials = $this->validate([
        'email' => ['required', 'email', 'regex:/^[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}$/'],
        'password' => ['required', 'string'],
    ], [
        'email.regex' => 'Please enter a valid email address, like name@example.com.',
    ]);

    $rateLimitKey = 'login:' . strtolower($this->email) . '|' . request()->ip();

    if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
        throw ValidationException::withMessages([
            'email' => 'Too many login attempts. Please try again later.',
        ]);
    }

    if (! Auth::attempt($credentials, $this->remember)) {
        RateLimiter::hit($rateLimitKey, 60);

        throw ValidationException::withMessages([
            'email' => 'The email or password is incorrect.',
        ]);
    }

    RateLimiter::clear($rateLimitKey);

    request()->session()->regenerate();

    if (Auth::user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('dashboard');
};

$register = function () {
    $this->validate([
        'name' => ['required', 'string', 'max:255'],
        'register_email' => ['required', 'email', 'regex:/^[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}$/', 'max:255', 'unique:users,email'],
        'register_password' => ['required', 'string', 'min:8', 'confirmed'],
    ], [
        'register_email.regex' => 'Please enter a valid email address, like name@example.com.',
    ]);

    $user = User::create([
        'name' => $this->name,
        'email' => $this->register_email,
        'password' => Hash::make($this->register_password),
    ]);

    Auth::login($user);

    request()->session()->regenerate();

    if (\Illuminate\Support\Facades\Route::has('wedding.setup')) {
        return redirect()->route('wedding.setup');
    }

    return redirect()->route('dashboard');
};

?>

<div class="sliding-auth-page">
    <div class="auth-bg-overlay"></div>

    <div class="auth-brand">
        <div class="brand-icon">
            <i class="fa-solid fa-heart"></i>
        </div>

        <div>
            <h1>Jodoh Together</h1>
            <p>Your wedding planning companion</p>
        </div>
    </div>

    <div class="auth-container {{ $authMode === 'register' ? 'register-mode' : '' }}">
        <div class="form-panel sign-in-panel">
            <form wire:submit.prevent="login" class="auth-form">
                <p class="eyebrow">Welcome Back</p>
                <h2>Login</h2>
                <p class="subtitle">Continue planning your perfect wedding day.</p>

                @if (\Illuminate\Support\Facades\Route::has('google.redirect'))
                    <a href="{{ route('google.redirect') }}" class="google-btn">
                        <i class="fa-brands fa-google"></i>
                        Continue with Google
                    </a>

                    <div class="divider">
                        <span>or login with email</span>
                    </div>
                @endif

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
                        <input type="password" wire:model="password" placeholder="Enter your password">
                    </div>
                    @error('password') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-options">
    <label class="remember-box">
        <input type="checkbox" wire:model="remember">
        <span>Remember me</span>
    </label>

    <a href="{{ route('password.request') }}" class="forgot-link" wire:navigate>
        Forgot Password?
    </a>
</div>

                <button type="submit" class="main-btn" wire:loading.attr="disabled" wire:target="login">
                    <span wire:loading.remove wire:target="login">
                        Login
                        <i class="fa-solid fa-arrow-right"></i>
                    </span>

                    <span wire:loading wire:target="login">
                        Logging in...
                    </span>
                </button>

@if (\Illuminate\Support\Facades\Route::has('admin.login'))
    <div class="admin-access">
        <span>Administrator?</span>
        <a href="{{ route('admin.login') }}" wire:navigate>
            Go to Admin Login
        </a>
    </div>
@endif

                <a href="{{ route('register') }}" class="mobile-switch">
    New here? Create an account
</a>
            </form>
        </div>

        <div class="form-panel sign-up-panel">
            <form wire:submit.prevent="register" class="auth-form">
                <p class="eyebrow">Start Planning</p>
                <h2>Register</h2>
                <p class="subtitle">Create your account first. Wedding details come next.</p>

                @if (\Illuminate\Support\Facades\Route::has('google.redirect'))
                    <a href="{{ route('google.redirect') }}" class="google-btn">
                        <i class="fa-brands fa-google"></i>
                        Sign up with Google
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
                        <input type="email" wire:model="register_email" placeholder="Enter your email"
                            pattern="[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}"
                            title="Please enter a valid email address, like name@example.com.">
                    </div>
                    @error('register_email') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" wire:model="register_password" placeholder="Minimum 8 characters">
                    </div>
                    @error('register_password') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>Confirm Password</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" wire:model="register_password_confirmation" placeholder="Confirm password">
                    </div>
                </div>

                <button type="submit" class="main-btn" wire:loading.attr="disabled" wire:target="register">
                    <span wire:loading.remove wire:target="register">
                        Create Account
                        <i class="fa-solid fa-arrow-right"></i>
                    </span>

                    <span wire:loading wire:target="register">
                        Creating...
                    </span>
                </button>

                <a href="{{ route('login') }}" class="mobile-switch">
    Already have an account? Login
</a>
            </form>
        </div>

        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <div class="overlay-content">
                        <div class="small-icon">
                            <i class="fa-solid fa-ring"></i>
                        </div>

                        <h2>Already have an account?</h2>
                        <p>Login to continue managing your wedding checklist, budget, vendors, and calendar.</p>

                        <a href="{{ route('login') }}" class="ghost-btn">
    Login
</a>
                    </div>
                </div>

                <div class="overlay-panel overlay-right">
                    <div class="overlay-content">
                        <div class="small-icon">
                            <i class="fa-solid fa-calendar-check"></i>
                        </div>

                        <h2>New to Jodoh Together?</h2>
                        <p>Create your account and start planning your wedding in one beautiful dashboard.</p>

                        <a href="{{ route('register') }}" class="ghost-btn">
    Register
</a>
                    </div>
                </div>
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

        .sliding-auth-page {
            min-height: 100vh;
            width: 100vw;
            margin-left: calc(50% - 50vw);
            margin-right: calc(50% - 50vw);
            position: relative;
            background:
                linear-gradient(rgba(17, 24, 39, 0.48), rgba(17, 24, 39, 0.48)),
                url('{{ asset('images/auth-bg.jpg') }}');
            background-size: cover;
            background-position: center;
            font-family: 'Inter', sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 34px;
            overflow-x: hidden;
        }

        .auth-bg-overlay {
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at top left, rgba(217, 95, 74, 0.24), transparent 35%),
                radial-gradient(circle at bottom right, rgba(255, 255, 255, 0.22), transparent 32%);
            pointer-events: none;
        }

        .auth-brand {
            position: relative;
            z-index: 2;
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

        .auth-brand h1 {
            margin: 0;
            font-size: 32px;
            font-weight: 900;
            letter-spacing: -0.04em;
        }

        .auth-brand p {
            margin: 4px 0 0;
            font-size: 14px;
            font-weight: 700;
            opacity: 0.9;
        }

        .auth-container {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 1040px;
            min-height: 650px;
            background: rgba(255, 255, 255, 0.96);
            border-radius: 30px;
            overflow: hidden;
            box-shadow: var(--shadow);
            backdrop-filter: blur(12px);
        }

        .form-panel {
            position: absolute;
            top: 0;
            height: 100%;
            width: 50%;
            transition: all 0.65s ease-in-out;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 42px 48px;
            background: rgba(255, 255, 255, 0.98);
        }

        .sign-in-panel {
            left: 0;
            z-index: 2;
        }

        .sign-up-panel {
            left: 0;
            z-index: 1;
            opacity: 0;
        }

        .auth-container.register-mode .sign-in-panel {
            transform: translateX(100%);
            opacity: 0;
        }

        .auth-container.register-mode .sign-up-panel {
            transform: translateX(100%);
            opacity: 1;
            z-index: 5;
        }

        .overlay-container {
            position: absolute;
            top: 0;
            left: 50%;
            width: 50%;
            height: 100%;
            overflow: hidden;
            transition: transform 0.65s ease-in-out;
            z-index: 20;
        }

        .auth-container.register-mode .overlay-container {
            transform: translateX(-100%);
        }

        .overlay {
            position: relative;
            left: -100%;
            height: 100%;
            width: 200%;
            background: linear-gradient(135deg, #6f3d2a, #4a271b);
            color: #ffffff;
            transform: translateX(0);
            transition: transform 0.65s ease-in-out;
        }

        .auth-container.register-mode .overlay {
            transform: translateX(50%);
        }

        .overlay-panel {
            position: absolute;
            top: 0;
            height: 100%;
            width: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 56px;
            text-align: center;
            transition: transform 0.65s ease-in-out;
        }

        .overlay-left {
            transform: translateX(-20%);
        }

        .overlay-right {
            right: 0;
            transform: translateX(0);
        }

        .auth-container.register-mode .overlay-left {
            transform: translateX(0);
        }

        .auth-container.register-mode .overlay-right {
            transform: translateX(20%);
        }

        .overlay-content {
            max-width: 330px;
        }

        .small-icon {
            width: 70px;
            height: 70px;
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.16);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 20px;
        }

        .overlay h2 {
            margin: 0;
            font-size: 34px;
            font-weight: 900;
            letter-spacing: -0.05em;
        }

        .overlay p {
            margin: 16px 0 28px;
            color: rgba(255, 255, 255, 0.84);
            font-size: 15px;
            font-weight: 700;
            line-height: 1.6;
        }

        .ghost-btn {
    height: 46px;
    padding: 0 34px;
    border-radius: 999px;
    border: 1px solid rgba(255, 255, 255, 0.85);
    background: transparent;
    color: #ffffff;
    font-size: 14px;
    font-weight: 900;
    font-family: inherit;
    cursor: pointer;
    transition: 0.25s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

        .ghost-btn:hover {
            background: #ffffff;
            color: #5d3223;
            transform: translateY(-3px);
        }

        .auth-form {
            width: 100%;
            max-width: 370px;
            display: grid;
            gap: 13px;
        }

        .eyebrow {
            margin: 0;
            color: var(--coral);
            font-size: 13px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.12em;
        }

        .auth-form h2 {
            margin: 0;
            color: var(--text);
            font-size: 38px;
            font-weight: 900;
            letter-spacing: -0.05em;
        }

        .subtitle {
            margin: -4px 0 8px;
            color: var(--muted);
            font-size: 14px;
            font-weight: 700;
            line-height: 1.5;
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

        .google-btn,
        .main-btn,
        .mobile-switch {
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

        .google-btn {
    position: relative;
    width: 100%;
    height: 50px;
    border-radius: 16px;
    border: 2px solid #e5e7eb;
    background:
        linear-gradient(#ffffff, #ffffff) padding-box,
        linear-gradient(90deg, #4285F4, #DB4437, #F4B400, #0F9D58) border-box;
    color: #374151;
    text-decoration: none;
    font-size: 15px;
    font-weight: 900;
    font-family: inherit;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    cursor: pointer;
    transition: 0.25s ease;
}

.google-btn i {
    color: #DB4437;
    font-size: 17px;
    transition: 0.25s ease;
}

.google-btn span {
    transition: 0.25s ease;
}

.google-btn:hover {
    border-color: transparent;
    background:
        linear-gradient(#ffffff, #ffffff) padding-box,
        linear-gradient(90deg, #4285F4, #DB4437, #F4B400, #0F9D58) border-box;
    transform: translateY(-3px);
    box-shadow: none;
}

.google-btn:hover i,
.google-btn:hover span {
    background: linear-gradient(90deg, #4285F4, #DB4437, #F4B400, #0F9D58);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
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

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 800;
            margin: 1px 0;
        }

        .divider::before,
        .divider::after {
            content: "";
            height: 1px;
            background: #e5e7eb;
            flex: 1;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin: 2px 0 4px;
        }

        .remember-box {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--muted);
            font-size: 13px;
            font-weight: 800;
        }

        .remember-box input {
            accent-color: var(--coral);
        }

        .form-options a {
            color: var(--coral);
            font-size: 13px;
            font-weight: 900;
            text-decoration: none;
        }

        .form-options a:hover {
            color: var(--coral-dark);
        }

        .error-msg {
            color: var(--red);
            font-size: 12px;
            font-weight: 800;
        }

        .mobile-switch {
            display: none;
            width: 100%;
            border: none;
            background: #f3f4f6;
            color: #374151;
        }

        .mobile-switch:hover {
            background: var(--coral-light);
            color: var(--coral);
        }

        @media (max-width: 900px) {
            .sliding-auth-page {
                padding: 24px;
            }

            .auth-brand h1 {
                font-size: 27px;
            }

            .auth-container {
                min-height: auto;
                max-width: 520px;
            }

            .form-panel {
                position: relative;
                width: 100%;
                height: auto;
                min-height: auto;
                padding: 34px 28px;
                transform: none !important;
                opacity: 1;
            }

            .sign-up-panel {
                display: none;
            }

            .auth-container.register-mode .sign-in-panel {
                display: none;
            }

            .auth-container.register-mode .sign-up-panel {
                display: flex;
            }

            .overlay-container {
                display: none;
            }

            .mobile-switch {
                display: inline-flex;
            }

            .auth-form h2 {
                font-size: 32px;
            }
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
}

.admin-access {
    margin-top: -2px;
    padding: 12px 14px;
    border-radius: 14px;
    background: #f9fafb;
    border: 1px solid #eef0f3;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-size: 13px;
    font-weight: 800;
    color: var(--muted);
}

.admin-access a {
    color: var(--coral);
    font-weight: 900;
    text-decoration: none;
}

.admin-access a:hover {
    color: var(--coral-dark);
    text-decoration: underline;
}
    </style>
</div>
