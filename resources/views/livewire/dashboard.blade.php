<?php

use function Livewire\Volt\computed;
use function Livewire\Volt\layout;
use function Livewire\Volt\state;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

layout('layouts.app');

state([
    'calendarWeekStart' => now()->startOfWeek(Carbon::MONDAY)->format('Y-m-d'),
]);

$previousWeek = function () {
    $this->calendarWeekStart = Carbon::parse($this->calendarWeekStart)
        ->subWeek()
        ->format('Y-m-d');
};

$nextWeek = function () {
    $this->calendarWeekStart = Carbon::parse($this->calendarWeekStart)
        ->addWeek()
        ->format('Y-m-d');
};

$calendarHeaderLabel = computed(function () {
    return Carbon::parse($this->calendarWeekStart)->format('F Y');
});

$calendarWeekDays = computed(function () {
    $start = Carbon::parse($this->calendarWeekStart)->startOfDay();
    $end = $start->copy()->addDays(6)->endOfDay();

    $tasks = collect();

    if (Schema::hasTable('tasks') && Schema::hasColumn('tasks', 'deadline')) {
        $query = DB::table('tasks')
            ->whereBetween('deadline', [$start->toDateString(), $end->toDateString()]);

        if (Schema::hasColumn('tasks', 'user_id')) {
            $query->where('user_id', auth()->id());
        }

        $tasks = $query->get();
    }

    $days = [];

    for ($i = 0; $i < 7; $i++) {
        $currentDay = $start->copy()->addDays($i);

        $dayTasks = $tasks->filter(function ($task) use ($currentDay) {
            return !empty($task->deadline)
                && Carbon::parse($task->deadline)->isSameDay($currentDay);
        });

        $hasHighPriority = $dayTasks->filter(function ($task) {
            return ($task->priority ?? null) === 'high';
        })->count() > 0;

        $days[] = [
            'name' => $currentDay->format('D'),
            'date_num' => $currentDay->format('j'),
            'is_today' => $currentDay->isToday(),
            'has_task' => $dayTasks->count() > 0,
            'has_high_priority' => $hasHighPriority,
        ];
    }

    return $days;
});

$upcomingTasks = computed(function () {
    if (!Schema::hasTable('tasks') || !Schema::hasColumn('tasks', 'deadline')) {
        return collect();
    }

    $query = DB::table('tasks')
        ->whereDate('deadline', '>=', now())
        ->orderBy('deadline', 'asc')
        ->limit(3);

    if (Schema::hasColumn('tasks', 'user_id')) {
        $query->where('user_id', auth()->id());
    }

    if (Schema::hasColumn('tasks', 'status')) {
        $query->where('status', 'pending');
    }

    return $query->get();
});

$logout = function () {
    Auth::logout();

    session()->invalidate();
    session()->regenerateToken();

    return $this->redirect(url('login'), navigate: true);
};

?>

@php
    $user = auth()->user();
    $weddingDetail = $user?->weddingDetail;

    $weddingDateRaw = $weddingDetail?->wedding_date ?? $user?->wedding_date;
    $weddingDate = $weddingDateRaw ? Carbon::parse($weddingDateRaw) : null;

    $daysToGo = $weddingDate
        ? max((int) now()->startOfDay()->diffInDays($weddingDate->copy()->startOfDay(), false), 0)
        : 0;

    $weddingShortDate = $weddingDate ? $weddingDate->format('d/m/y') : 'Not set';
    $weddingLongDate = $weddingDate ? $weddingDate->format('d F Y') : 'Not set';

    $totalBudget = $weddingDetail?->total_budget ?? $user?->budget ?? 0;

    $spentBudget = 0;

    if (Schema::hasTable('expenses') && Schema::hasColumn('expenses', 'amount')) {
        $expenseQuery = DB::table('expenses');

        if (Schema::hasColumn('expenses', 'user_id')) {
            $expenseQuery->where('user_id', auth()->id());
        }

        $spentBudget = $expenseQuery->sum('amount');
    }

    $remainingBudget = max($totalBudget - $spentBudget, 0);

    $totalTasks = 0;
    $completedTasks = 0;
    $pendingTasks = 0;

    if (Schema::hasTable('tasks')) {
        $taskBase = DB::table('tasks');

        if (Schema::hasColumn('tasks', 'user_id')) {
            $taskBase->where('user_id', auth()->id());
        }

        $totalTasks = (clone $taskBase)->count();

        if (Schema::hasColumn('tasks', 'status')) {
            $completedTasks = (clone $taskBase)->where('status', 'completed')->count();
            $pendingTasks = (clone $taskBase)->where('status', 'pending')->count();
        } else {
            $pendingTasks = $totalTasks;
        }
    }

    $bookedVendors = 0;

    if (
        Schema::hasTable('vendors') &&
        Schema::hasColumn('vendors', 'user_id') &&
        Schema::hasColumn('vendors', 'is_booked')
    ) {
        $bookedVendors = DB::table('vendors')
            ->where('user_id', auth()->id())
            ->where('is_booked', true)
            ->count();
    }

    $notificationCount = $this->upcomingTasks->count();
@endphp

<div class="app-wrapper">

    <aside class="sidebar">
        <div class="logo">
            <i class="fa-solid fa-heart"></i>
            <span>Jodoh Together</span>
        </div>

        <nav class="nav-menu">
            <a href="{{ url('dashboard') }}" class="nav-link active" wire:navigate>
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

        <button type="button" wire:click="logout" class="nav-link logout-link">
            <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
        </button>
    </aside>

    <main class="main-content">

        <header class="page-header">
            <div>
                <p class="eyebrow">Dashboard</p>
                <h1>Hello, {{ $user?->name ?? 'Guest' }}!</h1>
                <p>Here is your wedding planning overview.</p>
            </div>

            <div class="user-controls">
                <x-app-notifications />

                <div class="profile-wrap" id="profileWrap">
                    <button class="profile-btn" id="profileBtn" type="button" aria-haspopup="true" aria-expanded="false">
                        <div class="avatar-initials">
                            {{ strtoupper(substr($user?->name ?? 'U', 0, 1)) }}
                        </div>

                        <span>{{ $user?->name ?? 'User' }}</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </button>

                    <div class="profile-dropdown" id="profileMenu">
                        <div class="profile-summary">
                            <strong>{{ $user?->name ?? 'User' }}</strong>
                            <span>{{ $user?->email ?? 'No email' }}</span>
                        </div>

                        <a href="{{ url('profile') }}" class="dropdown-link" wire:navigate>
                            <i class="fa-regular fa-user"></i> My Profile
                        </a>

                        

                        <button type="button" wire:click="logout" class="dropdown-link logout">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <section class="hero-grid">
            <article class="hero-card wedding-hero" onclick="window.location.href='{{ url('my/wedding') }}'">
                <div>
                    <p class="card-label">Wedding Day</p>

                    <h2>
                        {{ $user?->name ?? 'Groom' }} &amp; {{ $weddingDetail?->partner_name ?? 'Bride' }}
                    </h2>

                    <div class="days-count">
                        <strong>{{ $daysToGo }}</strong>
                        <span>{{ $daysToGo == 1 ? 'day to go' : 'days to go' }}</span>
                    </div>
                </div>

                <div class="hero-date">
                    <span>{{ $weddingShortDate }}</span>
                    <i class="fa-regular fa-calendar"></i>
                </div>
            </article>

            <article class="summary-card clickable" onclick="window.location.href='{{ url('budget') }}'">
                <div class="summary-icon">
                    <i class="fa-solid fa-dollar-sign"></i>
                </div>

                <div>
                    <p>Budget Spent</p>
                    <h2>RM {{ number_format($spentBudget, 0) }}</h2>
                    <span class="small-text">Total budget RM {{ number_format($totalBudget, 0) }}</span>
                </div>
            </article>

            <article class="summary-card clickable" onclick="window.location.href='{{ url('tasks') }}'">
                <div class="summary-icon">
                    <i class="fa-regular fa-square-check"></i>
                </div>

                <div>
                    <p>Tasks</p>
                    <h2>{{ $completedTasks }} / {{ $totalTasks }}</h2>
                    <span class="small-text">{{ $pendingTasks }} pending</span>
                </div>
            </article>
        </section>

        <section class="content-grid">
            <article class="card calendar-card">
                <div class="card-header">
                    <div>
                        <p class="eyebrow">Schedule</p>
                        <h2>Calendar</h2>
                    </div>

                    <div class="calendar-controls">
                        <button type="button" wire:click="previousWeek">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>

                        <span>{{ $this->calendarHeaderLabel }}</span>

                        <button type="button" wire:click="nextWeek">
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                    </div>
                </div>

                <div class="calendar-days">
                    @foreach ($this->calendarWeekDays as $day)
                        <div class="day-col">
                            <span class="day-name">{{ $day['name'] }}</span>

                            <div class="day-number {{ $day['is_today'] ? 'active' : '' }}">
                                {{ $day['date_num'] }}

                                @if ($day['has_high_priority'])
                                    <span class="day-dot red"></span>
                                @elseif ($day['has_task'])
                                    <span class="day-dot teal"></span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <a href="{{ url('calendar') }}" class="outline-btn" wire:navigate>
                    View Full Calendar
                </a>
            </article>

            <article class="card upcoming-card">
                <div class="card-header">
                    <div>
                        <p class="eyebrow">Planning Timeline</p>
                        <h2>Upcoming Tasks</h2>
                    </div>

                    <a href="{{ url('tasks') }}" class="outline-btn small" wire:navigate>
                        View All
                    </a>
                </div>

                <div class="upcoming-list">
                    @forelse ($this->upcomingTasks as $task)
                        <div class="upcoming-item">
                            <div class="task-date">
                                {{ $task->deadline ? Carbon::parse($task->deadline)->format('d M') : 'No date' }}
                            </div>

                            <div class="task-dot {{ ($task->priority ?? '') === 'high' ? 'red' : 'teal' }}"></div>

                            <div class="task-info">
                                <h3>{{ $task->title }}</h3>
                                <p>{{ $task->category ? ucfirst($task->category) : 'Wedding Task' }}</p>
                            </div>

                            <span class="status-badge teal">Pending</span>
                        </div>
                    @empty
                        <div class="empty-state">
                            <i class="fa-regular fa-circle-check"></i>
                            <h3>All caught up</h3>
                            <p>No upcoming tasks yet.</p>
                        </div>
                    @endforelse
                </div>
            </article>
        </section>

        <section class="bottom-grid dashboard-bottom-row">
            <article class="bottom-card clickable" onclick="window.location.href='{{ url('my/wedding') }}'">
                <div class="icon-circle">
                    <i class="fa-regular fa-calendar"></i>
                </div>

                <div>
                    <p>Wedding Date</p>
                    <h3>{{ $weddingLongDate }}</h3>
                </div>
            </article>

            <article class="bottom-card clickable" onclick="window.location.href='{{ url('vendors') }}'">
                <div class="icon-circle">
                    <i class="fa-solid fa-store"></i>
                </div>

                <div>
                    <p>Vendors Booked</p>
                    <h3>{{ $bookedVendors }}</h3>
                </div>
            </article>

            <article class="bottom-card clickable" onclick="window.location.href='{{ url('budget') }}'">
                <div class="icon-circle">
                    <i class="fa-solid fa-wallet"></i>
                </div>

                <div>
                    <p>Budget Remaining</p>
                    <h3>RM {{ number_format($remainingBudget, 0) }}</h3>
                </div>
            </article>
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
    </script>

    <style>
        * {
            box-sizing: border-box;
        }

        .app-wrapper {
            display: flex;
            min-height: 100vh;
            width: 100%;
            background: #f7f3ef;
            color: #111827;
        }

        .sidebar {
            width: 275px;
            min-height: 100vh;
            background: #1b1c22;
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

        .logout-link {
            margin-top: auto;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
            font-family: inherit;
        }

        .main-content {
            flex: 1;
            min-width: 0;
            min-height: 100vh;
            padding: 42px 48px 60px;
            overflow-x: hidden;
            background: #f7f3ef;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            margin-bottom: 26px;
        }

        .page-header h1 {
            font-size: 38px;
            font-weight: 900;
            margin: 0 0 8px;
            color: #111827;
            letter-spacing: -0.7px;
        }

        .page-header p {
            margin: 0;
            color: #6b7280;
            font-size: 16px;
        }

        .eyebrow {
            margin: 0 0 6px;
            color: #d95f4a;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .user-controls {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .notification {
            position: relative;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .notification:hover {
            background: #ffffff;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
        }

        .notification i {
            font-size: 22px;
        }

        .badge {
            position: absolute;
            top: 2px;
            right: 2px;
            min-width: 18px;
            height: 18px;
            border-radius: 999px;
            background: #ff4e42;
            color: #ffffff;
            font-size: 11px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 5px;
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
            color: #111827;
        }

        .avatar-initials {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, #d95f4a, #f5b4a8);
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
            border: 1px solid #eeeeee;
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
            color: #6b7280;
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

        .dropdown-link:hover {
            background: #fff1ee;
            color: #d95f4a;
        }

        .dropdown-link.logout {
            color: #e3342f;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.4fr) minmax(0, 1fr) minmax(0, 1fr);
            gap: 22px;
            margin-bottom: 26px;
            width: 100%;
        }

        .hero-card,
        .summary-card,
        .card,
        .bottom-card {
            border-radius: 20px;
            box-shadow: 0 12px 35px rgba(31, 41, 55, 0.07);
            transition: 0.25s ease;
            min-width: 0;
        }

        .clickable,
        .wedding-hero {
            cursor: pointer;
        }

        .hero-card:hover,
        .summary-card:hover,
        .card:hover,
        .bottom-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 42px rgba(185, 78, 62, 0.22);
        }

        .wedding-hero,
        .hero-grid > .summary-card,
        .bottom-grid > .bottom-card {
            background: linear-gradient(135deg, #d95f4a, #b94e3e);
            color: #ffffff;
            border: none;
            position: relative;
            overflow: hidden;
        }

        .wedding-hero::after,
        .hero-grid > .summary-card::after,
        .bottom-grid > .bottom-card::after {
            content: "";
            position: absolute;
            right: -45px;
            top: -55px;
            width: 170px;
            height: 170px;
            background: rgba(255, 255, 255, 0.12);
            border-radius: 50%;
            pointer-events: none;
        }

        .wedding-hero {
            min-height: 220px;
            padding: 28px 30px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .card-label {
            margin: 0 0 8px;
            font-size: 14px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            opacity: 0.9;
        }

        .wedding-hero h2 {
            margin: 0 0 24px;
            font-size: 24px;
            font-weight: 900;
            word-break: break-word;
            position: relative;
            z-index: 2;
        }

        .days-count {
            display: flex;
            align-items: baseline;
            gap: 12px;
            flex-wrap: wrap;
            position: relative;
            z-index: 2;
        }

        .days-count strong {
            font-size: 76px;
            line-height: 1;
            font-weight: 950;
            letter-spacing: -3px;
        }

        .days-count span {
            font-size: 22px;
            font-weight: 800;
        }

        .hero-date {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 76px;
            font-weight: 800;
            flex-shrink: 0;
        }

        .hero-date i {
            font-size: 40px;
            opacity: 0.95;
        }

        .summary-card {
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 16px;
            overflow: hidden;
            min-height: 220px;
        }

        .summary-card > div:last-child {
            min-width: 0;
            flex: 1;
            position: relative;
            z-index: 2;
        }

        .summary-icon {
            width: 58px;
            height: 58px;
            min-width: 58px;
            border-radius: 17px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 23px;
            flex-shrink: 0;
            background: rgba(255, 255, 255, 0.18);
            color: #ffffff;
            position: relative;
            z-index: 2;
        }

        .summary-card p,
        .summary-card h2,
        .summary-card .small-text,
        .bottom-card p,
        .bottom-card h3 {
            color: #ffffff;
        }

        .summary-card p {
            margin: 0 0 5px;
            font-size: 14px;
            font-weight: 800;
            opacity: 0.9;
        }

        .summary-card h2 {
            margin: 0;
            font-size: 31px;
            font-weight: 900;
            word-break: break-word;
        }

        .small-text {
            display: block;
            margin-top: 6px;
            font-size: 13px;
            font-weight: 700;
            opacity: 0.9;
            word-break: break-word;
        }

        .content-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1.35fr);
            gap: 26px;
            margin-bottom: 26px;
            width: 100%;
            align-items: start;
        }

        .card {
            background: rgba(255, 255, 255, 0.97);
            border: 1px solid rgba(225, 225, 225, 0.9);
            padding: 24px 28px;
            overflow: hidden;
        }

        .calendar-card,
        .upcoming-card {
            min-height: 285px;
            height: auto;
            display: flex;
            flex-direction: column;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 16px;
        }

        .card-header h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 900;
            color: #111827;
        }

        .calendar-controls {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #111827;
            font-weight: 900;
            flex-wrap: wrap;
        }

        .calendar-controls button {
            width: 36px;
            height: 36px;
            border: none;
            border-radius: 11px;
            background: #fff1ee;
            color: #d95f4a;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .calendar-controls button:hover {
            background: #d95f4a;
            color: #ffffff;
        }

        .calendar-days {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 10px;
            margin: 8px 0 18px;
        }

        .day-col {
            text-align: center;
            min-width: 0;
        }

        .day-name {
            display: block;
            color: #6b7280;
            font-size: 13px;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .day-number {
            position: relative;
            width: 42px;
            height: 42px;
            border-radius: 14px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            color: #111827;
        }

        .day-number.active {
            background: #1b1c22;
            color: #ffffff;
        }

        .day-dot {
            position: absolute;
            bottom: 5px;
            width: 6px;
            height: 6px;
            border-radius: 50%;
        }

        .day-dot.red {
            background: #dc2626;
        }

        .day-dot.teal {
            background: #11a6a6;
        }

        .outline-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #ff6b5f;
            color: #e74c3c;
            background: #ffffff;
            padding: 11px 20px;
            border-radius: 12px;
            font-weight: 800;
            text-decoration: none;
            cursor: pointer;
            transition: 0.2s ease;
            font-family: inherit;
            width: 100%;
            margin-top: auto;
        }

        .outline-btn.small {
            width: auto;
            padding: 10px 18px;
            margin-top: 0;
        }

        .outline-btn:hover {
            background: #ff6b5f;
            color: #ffffff;
        }

        .upcoming-list {
            display: grid;
            gap: 12px;
        }

        .upcoming-item {
            display: grid;
            grid-template-columns: 70px 18px minmax(0, 1fr) auto;
            gap: 14px;
            align-items: center;
            padding: 13px 14px;
            border-radius: 16px;
            border: 1px solid #eeeeee;
            background: #fafafa;
            min-width: 0;
        }

        .task-date {
            background: #f4eee8;
            border-radius: 10px;
            padding: 10px;
            text-align: center;
            font-size: 13px;
            font-weight: 900;
        }

        .task-dot {
            width: 14px;
            height: 14px;
            border-radius: 50%;
        }

        .task-dot.teal {
            background: #11a6a6;
            box-shadow: 0 0 0 5px #e0f7f7;
        }

        .task-dot.red {
            background: #dc2626;
            box-shadow: 0 0 0 5px #fee2e2;
        }

        .task-info {
            min-width: 0;
        }

        .task-info h3 {
            margin: 0 0 4px;
            font-size: 16px;
            font-weight: 900;
            word-break: break-word;
        }

        .task-info p {
            margin: 0;
            color: #6b7280;
            font-size: 13px;
            font-weight: 700;
        }

        .status-badge {
            border-radius: 999px;
            padding: 7px 12px;
            font-size: 12px;
            font-weight: 900;
            white-space: nowrap;
        }

        .status-badge.teal {
            background: #d9f4f3;
            color: #087b7b;
        }

        .empty-state {
            text-align: center;
            padding: 24px 18px;
            background: #fafafa;
            border: 1px dashed #e5e7eb;
            border-radius: 16px;
        }

        .empty-state i {
            font-size: 34px;
            color: #11a6a6;
            margin-bottom: 10px;
        }

        .empty-state h3 {
            margin: 0 0 6px;
            font-size: 18px;
        }

        .empty-state p {
            margin: 0;
            color: #6b7280;
        }

        .bottom-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 22px;
            width: 100%;
            margin-top: 0;
            position: static;
        }

        .bottom-card {
            min-width: 0;
            padding: 22px;
            display: flex;
            align-items: center;
            gap: 16px;
            overflow: hidden;
            min-height: 105px;
        }

        .bottom-card > div:last-child {
            min-width: 0;
            flex: 1;
            position: relative;
            z-index: 2;
        }

        .icon-circle {
            width: 52px;
            height: 52px;
            min-width: 52px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.18);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
            position: relative;
            z-index: 2;
        }

        .bottom-card p {
            margin: 0 0 5px;
            font-size: 14px;
            font-weight: 800;
            white-space: nowrap;
            opacity: 0.9;
        }

        .bottom-card h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 900;
            line-height: 1.2;
            word-break: break-word;
        }

        @media (max-width: 1180px) {
            .hero-grid,
            .content-grid {
                grid-template-columns: 1fr;
            }

            .bottom-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .summary-card {
                min-height: 160px;
            }

            .calendar-card,
            .upcoming-card {
                min-height: 260px;
            }
        }

        @media (max-width: 900px) {
            .app-wrapper {
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

            .page-header {
                flex-direction: column;
            }

            .upcoming-item {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .task-dot {
                display: none;
            }
        }

        @media (max-width: 760px) {
            .bottom-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 600px) {
            .nav-menu {
                grid-template-columns: 1fr;
            }

            .calendar-days {
                gap: 5px;
            }

            .day-number {
                width: 36px;
                height: 36px;
            }

            .days-count strong {
                font-size: 58px;
            }

            .days-count span {
                font-size: 18px;
            }

            .hero-date {
                gap: 40px;
                align-items: flex-start;
            }

            .wedding-hero {
                flex-direction: column;
                gap: 20px;
            }
        }
        /* FINAL POSITION FIX ONLY — does not change card size */

.dashboard-bottom-row {
    margin-top: 36px !important;
    position: relative !important;
    top: auto !important;
    transform: translateY(0) !important;
    z-index: 5 !important;
}

/* Force the middle row to reserve visual space before bottom row */
.content-grid {
    margin-bottom: 280px !important;
}

/* Keep the three cards centered below */
.bottom-grid {
    display: grid !important;
    grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    gap: 22px !important;
    width: 100% !important;
}

/* Do not allow old card hover/position rules to pull it upward */
.bottom-card {
    position: relative !important;
    top: auto !important;
    transform: none !important;
}

.bottom-card:hover {
    transform: translateY(-2px) !important;
}
    </style>

</div>
