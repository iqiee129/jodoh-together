<?php

use function Livewire\Volt\layout;
use function Livewire\Volt\mount;
use function Livewire\Volt\state;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\WeddingDetail;
use App\Services\GoogleCalendarService;

layout('layouts.app');

state([
    'partner_name' => '',
    'wedding_date' => '',
    'venue' => '',
    'theme' => '',
    'estimated_guests' => '',
    'total_budget' => '',
]);

mount(function () {
    $user = Auth::user();

    $wedding = WeddingDetail::where('user_id', $user->id)->first();

    if ($wedding) {
        $this->partner_name = $wedding->partner_name ?? '';
        $this->wedding_date = $wedding->wedding_date ?? '';
        $this->venue = $wedding->venue ?? '';
        $this->theme = $wedding->theme ?? '';
        $this->estimated_guests = $wedding->estimated_guests ?? $wedding->guest_count ?? '';
        $this->total_budget = $wedding->total_budget ?? '';
    }
});

$saveWeddingSetup = function (GoogleCalendarService $googleCalendar) {
    $this->validate([
        'partner_name' => ['nullable', 'string', 'max:255'],
        'wedding_date' => ['nullable', 'date'],
        'venue' => ['nullable', 'string', 'max:255'],
        'theme' => ['nullable', 'string', 'max:255'],
        'estimated_guests' => ['nullable', 'integer', 'min:0'],
        'total_budget' => ['nullable', 'numeric', 'min:0'],
    ]);

    $user = Auth::user();

    $data = [
        'user_id' => $user->id,
        'partner_name' => $this->partner_name ?: null,
        'wedding_date' => $this->wedding_date ?: null,
        'venue' => $this->venue ?: null,
        'theme' => $this->theme ?: null,
        'total_budget' => $this->total_budget ?: 0,
    ];

    if (Schema::hasColumn('wedding_details', 'estimated_guests')) {
        $data['estimated_guests'] = $this->estimated_guests ?: 0;
    }

    if (Schema::hasColumn('wedding_details', 'guest_count')) {
        $data['guest_count'] = $this->estimated_guests ?: 0;
    }

    $wedding = WeddingDetail::updateOrCreate(
    ['user_id' => $user->id],
    $data
);

if ($user->google_calendar_connected_at && $wedding->wedding_date) {
    try {
        $event = $googleCalendar->syncAllDayEvent($user, [
            'title' => 'Wedding Day',
            'date' => $wedding->wedding_date,
            'description' => 'Wedding day with ' . ($wedding->partner_name ?? 'your partner') .
                '. Venue: ' . ($wedding->venue ?? '-'),
        ], $wedding->google_event_id ?? null);

        $wedding->update([
            'google_event_id' => $event['id'] ?? $wedding->google_event_id,
        ]);
    } catch (\Throwable $e) {
        report($e);

        session()->flash('calendar_error', 'Wedding details saved, but Google Calendar sync failed. Please reconnect Google and try again.');
    }
}

    return redirect()->route('dashboard');
};

$skipSetup = function () {
    return redirect()->route('dashboard');
};

?>

<div class="setup-page">
    <div class="setup-overlay"></div>

    <div class="setup-shell">
        <div class="brand-title">
            <div class="brand-icon">
                <i class="fa-solid fa-heart"></i>
            </div>

            <div>
                <h1>Jodoh Together</h1>
                <p>Your wedding planning companion</p>
            </div>
        </div>

        <div class="setup-card">
            <div class="setup-left">
                <p class="eyebrow">Step 2</p>
                <h2>Wedding Details</h2>
                <p>
                    Add your wedding information so your dashboard, budget, and wedding profile are ready from the start.
                </p>

                <div class="setup-progress">
                    <div class="progress-item done">
                        <span>1</span>
                        <p>Account</p>
                    </div>

                    <div class="progress-line"></div>

                    <div class="progress-item active">
                        <span>2</span>
                        <p>Wedding</p>
                    </div>
                </div>

                <div class="setup-note">
                    <i class="fa-solid fa-circle-info"></i>
                    <span>You can update these details later in the My Wedding page.</span>
                </div>
            </div>

            <div class="setup-right">
                <form wire:submit.prevent="saveWeddingSetup" class="setup-form">
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

                    <div class="form-actions">
                        <button type="button" class="secondary-btn" wire:click="skipSetup">
                            Skip for Now
                        </button>

                        <button type="submit" class="main-btn" wire:loading.attr="disabled">
                            <span wire:loading.remove>Save & Continue</span>
                            <span wire:loading>Saving...</span>
                            <i class="fa-solid fa-arrow-right" wire:loading.remove></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        :root {
            --bg: #f7f3ef;
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

        .setup-page {
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

        .setup-overlay {
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at top left, rgba(217, 95, 74, 0.24), transparent 35%),
                radial-gradient(circle at bottom right, rgba(255, 255, 255, 0.22), transparent 32%);
            pointer-events: none;
        }

        .setup-shell {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 1050px;
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

        .setup-card {
            display: grid;
            grid-template-columns: 0.85fr 1.15fr;
            background: rgba(255, 255, 255, 0.96);
            border-radius: 28px;
            overflow: hidden;
            box-shadow: var(--shadow);
            backdrop-filter: blur(12px);
        }

        .setup-left {
            background: linear-gradient(135deg, #6f3d2a, #4a271b);
            color: #ffffff;
            padding: 54px 46px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .setup-left .eyebrow {
            margin: 0 0 10px;
            color: rgba(255, 255, 255, 0.8);
            font-size: 13px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.12em;
        }

        .setup-left h2 {
            margin: 0;
            font-size: 34px;
            font-weight: 900;
            letter-spacing: -0.04em;
        }

        .setup-left p {
            margin: 14px 0 0;
            color: rgba(255, 255, 255, 0.84);
            font-size: 15px;
            font-weight: 700;
            line-height: 1.6;
        }

        .setup-right {
            background: rgba(255, 255, 255, 0.97);
            padding: 46px;
        }

        .setup-progress {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 32px;
        }

        .progress-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: rgba(255, 255, 255, 0.75);
            font-size: 13px;
            font-weight: 900;
        }

        .progress-item span {
            width: 30px;
            height: 30px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.16);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .progress-item.done span,
        .progress-item.active span {
            background: #ffffff;
            color: var(--coral);
        }

        .progress-item p {
            margin: 0;
        }

        .progress-line {
            flex: 1;
            height: 2px;
            background: rgba(255, 255, 255, 0.25);
        }

        .setup-note {
            margin-top: 28px;
            padding: 14px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.12);
            display: flex;
            gap: 10px;
            align-items: flex-start;
            font-size: 13px;
            font-weight: 700;
            line-height: 1.5;
        }

        .setup-form {
            display: grid;
            gap: 16px;
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

        .form-actions {
            display: grid;
            grid-template-columns: 0.8fr 1.2fr;
            gap: 12px;
            margin-top: 4px;
        }

        .main-btn,
        .secondary-btn {
            height: 50px;
            border-radius: 16px;
            border: none;
            font-size: 15px;
            font-weight: 900;
            cursor: pointer;
            transition: 0.25s ease;
            font-family: inherit;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
        }

        .main-btn {
            background: var(--coral);
            color: #ffffff;
            box-shadow: 0 14px 30px rgba(217, 95, 74, 0.26);
        }

        .main-btn:hover {
            background: #c94f3d;
            transform: translateY(-3px);
            box-shadow: 0 18px 40px rgba(217, 95, 74, 0.32);
        }

        .secondary-btn {
            background: #f3f4f6;
            color: #374151;
        }

        .secondary-btn:hover {
            background: var(--coral-light);
            color: var(--coral);
            transform: translateY(-3px);
        }

        .error-msg {
            color: var(--red);
            font-size: 12px;
            font-weight: 800;
        }

        @media (max-width: 900px) {
            .setup-card {
                grid-template-columns: 1fr;
            }

            .setup-left,
            .setup-right {
                padding: 34px 28px;
            }

            .form-row,
            .form-actions {
                grid-template-columns: 1fr;
            }

            .brand-title h1 {
                font-size: 27px;
            }
        }
    </style>
</div>
