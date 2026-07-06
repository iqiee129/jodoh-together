<?php

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app')] class extends Component
{
    public int $step = 1;

    public string $email = '';

    public string $otp = '';

    public string $password = '';

    public string $password_confirmation = '';

public int $resendCooldown = 0;

public int $otpErrorKey = 0;

    public function sendOtp(): void
    {
        $this->validate([
            'email' => ['required', 'email', 'regex:/^[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}$/'],
        ], [
            'email.regex' => 'Please enter a valid email address, like name@example.com.',
        ]);

        $this->email = strtolower(trim($this->email));
        $rateLimitKey = 'password-reset-otp:' . $this->email . '|' . request()->ip();

        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            $this->addError('email', 'Too many OTP requests. Please try again later.');
            return;
        }

        RateLimiter::hit($rateLimitKey, 600);

        $user = User::where('email', $this->email)->first();
        $otp = (string) random_int(100000, 999999);
        $emailAddress = $this->email;

        if (! $user) {
            $this->step = 2;
            $this->otp = '';
            $this->resendCooldown = 60;

            session()->flash('status', 'If the email exists, a 6-digit OTP has been sent.');
            return;
        }

        DB::table('password_reset_otps')
            ->where('email', $emailAddress)
            ->delete();

        DB::table('password_reset_otps')->insert([
            'email' => $emailAddress,
            'otp_hash' => Hash::make($otp),
            'expires_at' => now()->addMinutes(10),
            'verified_at' => null,
            'attempts' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Mail::raw(
            "Your Jodoh Together password reset OTP is: {$otp}\n\nThis code will expire in 10 minutes.\n\nIf you did not request this, please ignore this email.",
            function ($message) use ($emailAddress) {
                $message->to($emailAddress)
                    ->subject('Jodoh Together Password Reset OTP');
            }
        );

        $this->step = 2;
        $this->otp = '';
        $this->resendCooldown = 60;

        session()->flash('status', 'If the email exists, a 6-digit OTP has been sent.');
    }

    public function verifyOtp(): void
    {
        $this->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        $record = DB::table('password_reset_otps')
            ->where('email', $this->email)
            ->latest()
            ->first();

        if (! $record) {
    $this->otp = '';
    $this->otpErrorKey++;
    $this->addError('otp', 'Invalid or expired OTP. Please request a new code.');
    return;
}

        if (now()->greaterThan($record->expires_at)) {
    $this->otp = '';
    $this->otpErrorKey++;
    $this->addError('otp', 'Invalid or expired OTP. Please request a new code.');
    return;
}

        if (($record->attempts ?? 0) >= 5) {
    $this->otp = '';
    $this->otpErrorKey++;
    $this->addError('otp', 'Too many invalid OTP attempts. Please request a new code.');
    return;
}

        if (! Hash::check($this->otp, $record->otp_hash)) {
    DB::table('password_reset_otps')
        ->where('id', $record->id)
        ->increment('attempts');

    $this->otp = '';
    $this->otpErrorKey++;
    $this->addError('otp', 'Invalid OTP. Please try again.');
    return;
}

        DB::table('password_reset_otps')
            ->where('id', $record->id)
            ->update([
                'verified_at' => now(),
                'updated_at' => now(),
            ]);

        $this->step = 3;
$this->password = '';
$this->password_confirmation = '';
$this->resendCooldown = 0;

        session()->flash('status', 'Email verified. You can now create a new password.');
    }

    public function resetPassword()
    {
        $this->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $record = DB::table('password_reset_otps')
            ->where('email', $this->email)
            ->whereNotNull('verified_at')
            ->latest()
            ->first();

        if (! $record) {
            $this->step = 1;
            $this->addError('email', 'Please verify your email first.');
            return null;
        }

        if (now()->greaterThan($record->expires_at)) {
            $this->step = 1;
            $this->addError('email', 'Your verification session expired. Please request a new OTP.');
            return null;
        }

        $user = User::where('email', $this->email)->first();

        if (! $user) {
            $this->step = 1;
            $this->addError('email', 'Account not found.');
            return null;
        }

        $user->forceFill([
            'password' => Hash::make($this->password),
            'remember_token' => Str::random(60),
        ])->save();

        event(new PasswordReset($user));

        DB::table('password_reset_otps')
            ->where('email', $this->email)
            ->delete();

        return redirect()->route('login')->with('status', 'Password reset successfully. You may now login.');
    }

    public function resendOtp(): void
{
    if ($this->resendCooldown > 0) {
        return;
    }

    $this->sendOtp();
}

public function tickResendTimer(): void
{
    if ($this->step === 2 && $this->resendCooldown > 0) {
        $this->resendCooldown--;
    }
}

    public function backToEmail(): void
{
    $this->step = 1;
    $this->otp = '';
    $this->password = '';
    $this->password_confirmation = '';
    $this->resendCooldown = 0;
    $this->otpErrorKey = 0;
}
};

?>

<div class="password-page">
    <div class="auth-bg-overlay"></div>

    <div class="password-shell">
        <div class="brand-title">
            <div class="brand-icon">
                <i class="fa-solid fa-heart"></i>
            </div>

            <div>
                <h1>Jodoh Together</h1>
                <p>Your wedding planning companion</p>
            </div>
        </div>

        <div class="password-card">
            <div class="password-side">
                <div>
                    <div class="small-icon">
                        @if ($step === 1)
                            <i class="fa-solid fa-envelope-open-text"></i>
                        @elseif ($step === 2)
                            <i class="fa-solid fa-shield-halved"></i>
                        @else
                            <i class="fa-solid fa-lock-open"></i>
                        @endif
                    </div>

                    <h2>
                        @if ($step === 1)
                            Forgot password?
                        @elseif ($step === 2)
                            Verify your email
                        @else
                            Create new password
                        @endif
                    </h2>

                    <p>
                        @if ($step === 1)
                            Enter your registered email. We will send a secure 6-digit OTP to verify your account.
                        @elseif ($step === 2)
                            Check your email and enter the 6-digit OTP. The code will expire in 10 minutes.
                        @else
                            Your email has been verified. Now create a new password for your account.
                        @endif
                    </p>

                    <a href="{{ route('login') }}" class="ghost-btn" wire:navigate>
                        Back to Login
                    </a>
                </div>
            </div>

            <div class="password-form-wrap">
                <div class="password-form">
                    <div class="step-indicator">
                        <div class="step-item {{ $step >= 1 ? 'active' : '' }}">
                            <span>1</span>
                            <p>Email</p>
                        </div>

                        <div class="step-line"></div>

                        <div class="step-item {{ $step >= 2 ? 'active' : '' }}">
                            <span>2</span>
                            <p>OTP</p>
                        </div>

                        <div class="step-line"></div>

                        <div class="step-item {{ $step >= 3 ? 'active' : '' }}">
                            <span>3</span>
                            <p>Password</p>
                        </div>
                    </div>

                    @if (session('status'))
                        <div class="success-box">
                            <i class="fa-solid fa-circle-check"></i>
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($step === 1)
                        <form wire:submit.prevent="sendOtp" class="inner-form">
                            <p class="eyebrow">Password Recovery</p>
                            <h2>Enter Email</h2>
                            <p class="subtitle">
                                We will send a 6-digit OTP to your registered email.
                            </p>

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

                            <button type="submit" class="main-btn" wire:loading.attr="disabled" wire:target="sendOtp">
                                <span wire:loading.remove wire:target="sendOtp">
                                    Send OTP
                                    <i class="fa-solid fa-arrow-right"></i>
                                </span>

                                <span wire:loading wire:target="sendOtp">
                                    Sending...
                                </span>
                            </button>
                        </form>
                    @endif

                    @if ($step === 2)
                        <form wire:submit.prevent="verifyOtp" class="inner-form" wire:poll.1s="tickResendTimer">
                            <p class="eyebrow">Email Verification</p>
                            <h2>Enter OTP</h2>
                            <p class="subtitle">
                                OTP sent to <strong>{{ $email }}</strong>.
                            </p>

                            <div class="form-group otp-group {{ $errors->has('otp') ? 'otp-has-error' : '' }}" wire:key="otp-group-{{ $otpErrorKey }}">
    <label>6-Digit OTP</label>

    <div class="otp-wrapper" wire:ignore>
        <div class="otp-boxes">
            <input type="text" maxlength="1" inputmode="numeric" class="otp-box" data-otp-index="0" oninput="jtOtpInput(event, 0)" onkeydown="jtOtpKeydown(event, 0)" onpaste="jtOtpPaste(event)">
            <input type="text" maxlength="1" inputmode="numeric" class="otp-box" data-otp-index="1" oninput="jtOtpInput(event, 1)" onkeydown="jtOtpKeydown(event, 1)" onpaste="jtOtpPaste(event)">
            <input type="text" maxlength="1" inputmode="numeric" class="otp-box" data-otp-index="2" oninput="jtOtpInput(event, 2)" onkeydown="jtOtpKeydown(event, 2)" onpaste="jtOtpPaste(event)">
            <input type="text" maxlength="1" inputmode="numeric" class="otp-box" data-otp-index="3" oninput="jtOtpInput(event, 3)" onkeydown="jtOtpKeydown(event, 3)" onpaste="jtOtpPaste(event)">
            <input type="text" maxlength="1" inputmode="numeric" class="otp-box" data-otp-index="4" oninput="jtOtpInput(event, 4)" onkeydown="jtOtpKeydown(event, 4)" onpaste="jtOtpPaste(event)">
            <input type="text" maxlength="1" inputmode="numeric" class="otp-box" data-otp-index="5" oninput="jtOtpInput(event, 5)" onkeydown="jtOtpKeydown(event, 5)" onpaste="jtOtpPaste(event)">
        </div>
    </div>

    @error('otp') <span class="error-msg">{{ $message }}</span> @enderror
</div>

                            <button type="submit" class="main-btn" wire:loading.attr="disabled" wire:target="verifyOtp">
                                <span wire:loading.remove wire:target="verifyOtp">
                                    Verify OTP
                                    <i class="fa-solid fa-check"></i>
                                </span>

                                <span wire:loading wire:target="verifyOtp">
                                    Verifying...
                                </span>
                            </button>

                            <div class="secondary-actions">
                                <button
    type="button"
    wire:click="resendOtp"
    wire:loading.attr="disabled"
    wire:target="resendOtp"
    @disabled($resendCooldown > 0)
    class="{{ $resendCooldown > 0 ? 'disabled-link' : '' }}"
>
    @if ($resendCooldown > 0)
        Resend OTP in {{ $resendCooldown }}s
    @else
        Resend OTP
    @endif
</button>

                                <button type="button" wire:click="backToEmail">
                                    Change Email
                                </button>
                            </div>
                        </form>
                    @endif

                    @if ($step === 3)
                        <form wire:submit.prevent="resetPassword" class="inner-form">
                            <p class="eyebrow">Password Reset</p>
                            <h2>New Password</h2>
                            <p class="subtitle">
                                Your new password must be at least 8 characters.
                            </p>

                            <div class="form-group">
                                <label>New Password</label>
                                <div class="input-wrap">
                                    <i class="fa-solid fa-lock"></i>
                                    <input type="password" wire:model="password" placeholder="Minimum 8 characters">
                                </div>
                                @error('password') <span class="error-msg">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Confirm New Password</label>
                                <div class="input-wrap">
                                    <i class="fa-solid fa-lock"></i>
                                    <input type="password" wire:model="password_confirmation" placeholder="Confirm password">
                                </div>
                            </div>

                            <button type="submit" class="main-btn" wire:loading.attr="disabled" wire:target="resetPassword">
                                <span wire:loading.remove wire:target="resetPassword">
                                    Reset Password
                                    <i class="fa-solid fa-arrow-right"></i>
                                </span>

                                <span wire:loading wire:target="resetPassword">
                                    Resetting...
                                </span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        :root {
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

        .password-page {
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

        .password-shell {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 1040px;
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

        .password-card {
            display: grid;
            grid-template-columns: 0.9fr 1.1fr;
            background: rgba(255, 255, 255, 0.96);
            border-radius: 30px;
            overflow: hidden;
            box-shadow: var(--shadow);
            backdrop-filter: blur(12px);
        }

        .password-side {
            background: linear-gradient(135deg, #6f3d2a, #4a271b);
            color: #ffffff;
            padding: 56px 46px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
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

        .password-side h2 {
            margin: 0;
            font-size: 34px;
            font-weight: 900;
            letter-spacing: -0.05em;
        }

        .password-side p {
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

        .password-form-wrap {
            padding: 46px 48px;
            background: rgba(255, 255, 255, 0.98);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .password-form {
            width: 100%;
            max-width: 430px;
            display: grid;
            gap: 16px;
        }

        .inner-form {
            display: grid;
            gap: 15px;
        }

        .eyebrow {
            margin: 0;
            color: var(--coral);
            font-size: 13px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.12em;
        }

        .password-form h2 {
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

        .subtitle strong {
            color: var(--text);
        }

        .step-indicator {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 4px;
        }

        .step-item {
            display: flex;
            align-items: center;
            gap: 7px;
            color: var(--muted);
            font-size: 12px;
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
            height: 50px;
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

        .otp-wrapper {
            width: 100%;
        }

        .otp-boxes {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 12px;
            margin-top: 8px;
        }

        .otp-box {
            width: 100%;
            height: 58px;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            text-align: center;
            font-size: 24px;
            font-weight: 900;
            outline: none;
            background: #ffffff;
            color: var(--text);
            transition: 0.2s ease;
            font-family: inherit;
        }

        .otp-box:focus {
            border-color: var(--coral);
            box-shadow: 0 0 0 4px rgba(217, 95, 74, 0.12);
        }

        .main-btn {
            height: 50px;
            border-radius: 16px;
            border: none;
            background: var(--coral);
            color: #ffffff;
            font-size: 15px;
            font-weight: 900;
            cursor: pointer;
            transition: 0.25s ease;
            font-family: inherit;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            box-shadow: 0 14px 30px rgba(217, 95, 74, 0.26);
        }

        .main-btn:hover {
            background: #c94f3d;
            transform: translateY(-3px);
            box-shadow: 0 18px 40px rgba(217, 95, 74, 0.32);
        }

        .secondary-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .secondary-actions button {
            border: none;
            background: transparent;
            color: var(--coral);
            font-size: 13px;
            font-weight: 900;
            cursor: pointer;
            font-family: inherit;
            padding: 0;
        }

        .secondary-actions button:hover {
            color: var(--coral-dark);
            text-decoration: underline;
        }

        .success-box {
            background: #ecfdf5;
            color: #047857;
            border-radius: 16px;
            padding: 13px 14px;
            font-size: 13px;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 8px;
            line-height: 1.4;
        }

        .error-msg {
            color: var(--red);
            font-size: 12px;
            font-weight: 800;
        }

        @media (max-width: 850px) {
            .password-card {
                grid-template-columns: 1fr;
            }

            .password-side,
            .password-form-wrap {
                padding: 34px 28px;
            }

            .brand-title h1 {
                font-size: 27px;
            }

            .password-form h2 {
                font-size: 32px;
            }

            .step-indicator {
                gap: 7px;
            }

            .step-item p {
                display: none;
            }
        }

        @media (max-width: 640px) {
            .otp-boxes {
                gap: 8px;
            }

            .otp-box {
                height: 50px;
                font-size: 20px;
                border-radius: 12px;
            }
        }
        .otp-has-error .otp-boxes {
    animation: otpShake 0.38s ease-in-out;
}

.otp-has-error .otp-box {
    border-color: var(--red);
    background: #fff5f5;
}

@keyframes otpShake {
    0% {
        transform: translateX(0);
    }

    20% {
        transform: translateX(-8px);
    }

    40% {
        transform: translateX(8px);
    }

    60% {
        transform: translateX(-6px);
    }

    80% {
        transform: translateX(6px);
    }

    100% {
        transform: translateX(0);
    }
}

.secondary-actions button:disabled,
.secondary-actions button.disabled-link {
    color: #9ca3af;
    cursor: not-allowed;
    text-decoration: none;
}

.secondary-actions button:disabled:hover,
.secondary-actions button.disabled-link:hover {
    color: #9ca3af;
    text-decoration: none;
}
    </style>

    <script>
    function jtOtpBoxes() {
        return Array.from(document.querySelectorAll('.otp-box'));
    }

    function jtOtpUpdateLivewire() {
        const otp = jtOtpBoxes().map(box => box.value).join('');

        @this.set('otp', otp);
    }

    function jtOtpInput(event, index) {
        const boxes = jtOtpBoxes();
        const currentBox = boxes[index];

        let value = event.target.value.replace(/\D/g, '');

        if (value.length > 1) {
            value = value.slice(0, 6);

            for (let i = 0; i < boxes.length; i++) {
                boxes[i].value = value[i] ?? '';
            }

            jtOtpUpdateLivewire();

            const nextIndex = Math.min(value.length, boxes.length - 1);
            boxes[nextIndex].focus();
            boxes[nextIndex].select();

            return;
        }

        currentBox.value = value;
        jtOtpUpdateLivewire();

        if (value !== '' && index < boxes.length - 1) {
            setTimeout(function () {
                boxes[index + 1].focus();
                boxes[index + 1].select();
            }, 10);
        }
    }

    function jtOtpKeydown(event, index) {
        const boxes = jtOtpBoxes();

        if (event.key === 'Backspace') {
            if (boxes[index].value === '' && index > 0) {
                event.preventDefault();

                boxes[index - 1].value = '';
                boxes[index - 1].focus();
                boxes[index - 1].select();

                jtOtpUpdateLivewire();
            }

            return;
        }

        if (event.key === 'ArrowLeft' && index > 0) {
            event.preventDefault();
            boxes[index - 1].focus();
            boxes[index - 1].select();
        }

        if (event.key === 'ArrowRight' && index < boxes.length - 1) {
            event.preventDefault();
            boxes[index + 1].focus();
            boxes[index + 1].select();
        }
    }

    function jtOtpPaste(event) {
        event.preventDefault();

        const boxes = jtOtpBoxes();

        const pasted = (event.clipboardData || window.clipboardData)
            .getData('text')
            .replace(/\D/g, '')
            .slice(0, 6);

        if (!pasted) {
            return;
        }

        for (let i = 0; i < boxes.length; i++) {
            boxes[i].value = pasted[i] ?? '';
        }

        jtOtpUpdateLivewire();

        const nextIndex = Math.min(pasted.length, boxes.length - 1);
        boxes[nextIndex].focus();
        boxes[nextIndex].select();
    }

    document.addEventListener('DOMContentLoaded', function () {
        const boxes = jtOtpBoxes();

        if (boxes.length) {
            setTimeout(function () {
                boxes[0].focus();
            }, 200);
        }
    });

    document.addEventListener('livewire:navigated', function () {
        const boxes = jtOtpBoxes();

        if (boxes.length) {
            setTimeout(function () {
                boxes[0].focus();
            }, 200);
        }
    });
</script>
</div>
