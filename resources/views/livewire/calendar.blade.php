<?php

use function Livewire\Volt\layout;
use function Livewire\Volt\mount;
use function Livewire\Volt\state;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Task;
use App\Models\WeddingDetail;

layout('layouts.app');

state([
    'user' => null,
    'currentMonth' => null,
    'showDayModal' => false,
    'selectedDate' => null,
]);

mount(function () {
    $this->user = Auth::user();
    $this->currentMonth = now()->startOfMonth()->toDateString();
});

$previousMonth = function () {
    $this->currentMonth = Carbon::parse($this->currentMonth)->subMonth()->startOfMonth()->toDateString();
};

$nextMonth = function () {
    $this->currentMonth = Carbon::parse($this->currentMonth)->addMonth()->startOfMonth()->toDateString();
};

$goToday = function () {
    $this->currentMonth = now()->startOfMonth()->toDateString();
};
$openDayDetails = function ($date) {
    $this->selectedDate = $date;
    $this->showDayModal = true;
};

$closeDayDetails = function () {
    $this->showDayModal = false;
    $this->selectedDate = null;
};
?>

@php
    $monthDate = Carbon::parse($currentMonth)->startOfMonth();
    $monthTitle = $monthDate->format('F Y');

    $startOfCalendar = $monthDate->copy()->startOfWeek(Carbon::SUNDAY);
    $endOfCalendar = $monthDate->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);

    $calendarDays = [];
    $dayPointer = $startOfCalendar->copy();

    while ($dayPointer <= $endOfCalendar) {
        $calendarDays[] = $dayPointer->copy();
        $dayPointer->addDay();
    }

    $weddingDetail = WeddingDetail::where('user_id', $user?->id)->first();
    $weddingDate = $weddingDetail?->wedding_date ?? $user?->wedding_date ?? null;

    $tasks = Task::where('user_id', $user?->id)
        ->whereNotNull('deadline')
        ->orderBy('deadline', 'asc')
        ->get();

    $tasksByDate = $tasks->groupBy(function ($task) {
        return Carbon::parse($task->deadline)->format('Y-m-d');
    });
$selectedDayTasks = $selectedDate
    ? ($tasksByDate[$selectedDate] ?? collect())
    : collect();

$selectedDateFormatted = $selectedDate
    ? Carbon::parse($selectedDate)->format('d F Y')
    : null;
    $upcomingTasks = $tasks
        ->filter(fn ($task) => Carbon::parse($task->deadline)->isToday() || Carbon::parse($task->deadline)->isFuture())
        ->take(6);

    $totalTasksThisMonth = $tasks->filter(function ($task) use ($monthDate) {
        return Carbon::parse($task->deadline)->isSameMonth($monthDate);
    })->count();

    $completedTasksThisMonth = $tasks->filter(function ($task) use ($monthDate) {
        return Carbon::parse($task->deadline)->isSameMonth($monthDate)
            && $task->status === 'completed';
    })->count();

    $pendingTasksThisMonth = max($totalTasksThisMonth - $completedTasksThisMonth, 0);

    $weddingInThisMonth = $weddingDate
        ? Carbon::parse($weddingDate)->isSameMonth($monthDate)
        : false;

    $daysUntilWedding = $weddingDate
        ? now()->startOfDay()->diffInDays(Carbon::parse($weddingDate)->startOfDay(), false)
        : null;
@endphp

<div class="calendar-page-wrapper">

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

            <a href="{{ url('calendar') }}" class="nav-link active" wire:navigate>
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
                <h1>Calendar</h1>
                <p>View your wedding date, task deadlines, and upcoming planning schedule.</p>
            </div>

            <div class="header-right">
                <button class="today-btn" type="button" wire:click="goToday">
                    <i class="fa-regular fa-calendar-check"></i> Today
                </button>

                <x-app-notifications />

                <div class="profile-wrap" id="profileWrap">
                    <button class="profile-btn" id="profileBtn" type="button" aria-haspopup="true" aria-expanded="false">
                        <div class="avatar-initials">
                            {{ strtoupper(substr($user?->name ?? 'U', 0, 1)) }}
                        </div>

                        <span>{{ $user?->name ?? 'User' }}</span>
                        <i class="fa-solid fa-chevron-down chevron"></i>
                    </button>

                    <div class="profile-dropdown">
                        <div class="profile-summary">
                            <strong>{{ $user?->name ?? 'User' }}</strong>
                            <span>{{ $user?->email ?? 'No email' }}</span>
                        </div>

                        <a href="{{ url('profile') }}" class="dropdown-link" wire:navigate>
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

<div class="google-calendar-card">
    <div>
        <p class="eyebrow">Google Calendar</p>
        <h3>Google Calendar Auto Sync</h3>

        @if (auth()->user()?->google_calendar_connected_at)
            <p>
                Connected as <strong>{{ auth()->user()->email }}</strong>.
                Your wedding date and task deadlines will automatically sync to Google Calendar.
            </p>
        @else
            <p>
                Sign up or reconnect with Google to enable automatic Google Calendar sync.
            </p>
        @endif

        @if (session('success'))
            <div class="sync-success">
                <i class="fa-solid fa-circle-check"></i>
                {{ session('success') }}
            </div>
        @endif

        @if (session('calendar_error'))
            <div class="sync-error">
                <i class="fa-solid fa-triangle-exclamation"></i>
                {{ session('calendar_error') }}
            </div>
        @endif
    </div>

    <div class="google-calendar-actions">
        @if (auth()->user()?->google_calendar_connected_at)
            <div class="auto-sync-badge">
                <i class="fa-solid fa-circle-check"></i>
                Auto Sync Enabled
            </div>

            <a href="{{ route('google.redirect') }}" class="google-sync-btn secondary">
                <i class="fa-solid fa-rotate"></i>
                Reconnect
            </a>
        @else
            <a href="{{ route('google.redirect') }}" class="google-sync-btn">
                <i class="fa-brands fa-google"></i>
                Connect Google Calendar
            </a>
        @endif
    </div>
</div>

        <section class="summary-grid">
            <article class="summary-card">
                <div class="summary-icon">
                    <i class="fa-solid fa-calendar-days"></i>
                </div>

                <div>
                    <p>Wedding Date</p>
                    <h2>
                        {{ $weddingDate ? Carbon::parse($weddingDate)->format('d M Y') : 'Not Set' }}
                    </h2>
                    <span>
                        {{ $weddingDate ? 'Your big day' : 'Add date in My Wedding' }}
                    </span>
                </div>
            </article>

            <article class="summary-card">
                <div class="summary-icon">
                    <i class="fa-regular fa-square-check"></i>
                </div>

                <div>
                    <p>This Month Tasks</p>
                    <h2>{{ $totalTasksThisMonth }}</h2>
                    <span>{{ $completedTasksThisMonth }} completed</span>
                </div>
            </article>

            <article class="summary-card">
                <div class="summary-icon">
                    <i class="fa-solid fa-hourglass-half"></i>
                </div>

                <div>
                    <p>Days Until Wedding</p>
                    <h2>
                        @if ($daysUntilWedding === null)
                            -
                        @elseif ($daysUntilWedding < 0)
                            Passed
                        @else
                            {{ $daysUntilWedding }}
                        @endif
                    </h2>
                    <span>Countdown from today</span>
                </div>
            </article>
        </section>

        <section class="content-grid">
            <div class="calendar-card">
                <div class="calendar-header">
                    <button class="month-btn" type="button" wire:click="previousMonth">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>

                    <div>
                        <h2>{{ $monthTitle }}</h2>
                        <p>
                            {{ $totalTasksThisMonth }} task{{ $totalTasksThisMonth === 1 ? '' : 's' }}
                            @if ($weddingInThisMonth)
                                • Wedding month
                            @endif
                        </p>
                    </div>

                    <button class="month-btn" type="button" wire:click="nextMonth">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>

                <div class="calendar-weekdays">
                    <div>Sun</div>
                    <div>Mon</div>
                    <div>Tue</div>
                    <div>Wed</div>
                    <div>Thu</div>
                    <div>Fri</div>
                    <div>Sat</div>
                </div>

                <div class="calendar-grid">
                    @foreach ($calendarDays as $day)
                        @php
                            $dateKey = $day->format('Y-m-d');
                            $dayTasks = $tasksByDate[$dateKey] ?? collect();
                            $isCurrentMonth = $day->isSameMonth($monthDate);
                            $isToday = $day->isToday();
                            $isWeddingDay = $weddingDate && $dateKey === Carbon::parse($weddingDate)->format('Y-m-d');
                        @endphp

                        <div
    class="calendar-day {{ !$isCurrentMonth ? 'muted-day' : '' }} {{ $isToday ? 'today' : '' }} {{ $isWeddingDay ? 'wedding-day' : '' }} {{ $dayTasks->count() > 0 ? 'clickable-day' : '' }}"
    @if ($dayTasks->count() > 0)
        wire:click="openDayDetails('{{ $dateKey }}')"
        role="button"
        tabindex="0"
    @endif
>
                            <div class="day-number">
                                {{ $day->format('j') }}
                            </div>

                            <div class="day-events">
                                @if ($isWeddingDay)
                                    <div class="event-pill wedding">
                                        <i class="fa-solid fa-heart"></i>
                                        Wedding Day
                                    </div>
                                @endif

                                @foreach ($dayTasks->take(2) as $task)
                                    <div class="event-pill {{ $task->status === 'completed' ? 'completed' : 'pending' }}">
                                        {{ Str::limit($task->title, 18) }}
                                    </div>
                                @endforeach

                                @if ($dayTasks->count() > 2)
                                    <div class="more-events">
                                        +{{ $dayTasks->count() - 2 }} more
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <aside class="side-panel">
                <div class="panel-card">
                    <div class="panel-title">
                        <h3>Upcoming Schedule</h3>
                        <a href="{{ url('tasks') }}" wire:navigate>View Tasks</a>
                    </div>

                    <div class="schedule-list">
                        @forelse ($upcomingTasks as $task)
                            @php
                                $deadline = Carbon::parse($task->deadline);
                            @endphp

                            <div class="schedule-item">
                                <div class="date-box">
                                    <strong>{{ $deadline->format('d') }}</strong>
                                    <span>{{ $deadline->format('M') }}</span>
                                </div>

                                <div class="schedule-info">
                                    <h4>{{ $task->title }}</h4>
                                    <p>
                                        {{ ucfirst($task->category ?? 'General') }}
                                        •
                                        {{ ucfirst($task->priority ?? 'Medium') }} priority
                                    </p>
                                </div>

                                <span class="status-badge {{ $task->status === 'completed' ? 'completed' : 'pending' }}">
                                    {{ ucfirst($task->status) }}
                                </span>
                            </div>
                        @empty
                            <div class="empty-state">
                                <i class="fa-regular fa-calendar-check"></i>
                                <h4>No upcoming tasks</h4>
                                <p>Add tasks with deadlines to see them here.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="panel-card">
                    <h3 class="legend-title">Legend</h3>

                    <div class="legend-list">
                        <div class="legend-item">
                            <span class="legend-dot wedding"></span>
                            Wedding Day
                        </div>

                        <div class="legend-item">
                            <span class="legend-dot pending"></span>
                            Pending Task
                        </div>

                        <div class="legend-item">
                            <span class="legend-dot completed"></span>
                            Completed Task
                        </div>

                        <div class="legend-item">
                            <span class="legend-dot today"></span>
                            Today
                        </div>
                    </div>
                </div>
            </aside>
        </section>
        @if ($showDayModal && $selectedDate)
    <div class="modal-backdrop" wire:click="closeDayDetails">
        <div class="task-modal" wire:click.stop>
            <div class="task-modal-header">
                <div>
                    <p class="eyebrow">Task Details</p>
                    <h2>{{ $selectedDateFormatted }}</h2>
                    <span>
                        {{ $selectedDayTasks->count() }}
                        task{{ $selectedDayTasks->count() === 1 ? '' : 's' }} scheduled
                    </span>
                </div>

                <button type="button" class="modal-close-btn" wire:click="closeDayDetails">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="task-detail-list">
                @forelse ($selectedDayTasks as $task)
                    <div class="task-detail-card">
                        <div class="task-detail-top">
                            <div>
                                <h3>{{ $task->title }}</h3>

                                <p>
                                    {{ $task->description ?: 'No description added for this task.' }}
                                </p>
                            </div>

                            <span class="status-badge {{ $task->status === 'completed' ? 'completed' : 'pending' }}">
                                {{ ucfirst($task->status) }}
                            </span>
                        </div>

                        <div class="task-detail-meta">
                            <div>
                                <i class="fa-solid fa-layer-group"></i>
                                <span>{{ ucfirst($task->category ?? 'General') }}</span>
                            </div>

                            <div>
                                <i class="fa-solid fa-flag"></i>
                                <span>{{ ucfirst($task->priority ?? 'Medium') }} Priority</span>
                            </div>

                            <div>
                                <i class="fa-regular fa-calendar"></i>
                                <span>{{ Carbon::parse($task->deadline)->format('d M Y') }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <i class="fa-regular fa-calendar-check"></i>
                        <h4>No tasks found</h4>
                        <p>There are no tasks on this date.</p>
                    </div>
                @endforelse
            </div>

            <div class="modal-actions">
                <button type="button" class="cancel-btn" wire:click="closeDayDetails">
                    Close
                </button>

                <a href="{{ url('tasks') }}" class="solid-btn" wire:navigate>
                    View All Tasks
                </a>
            </div>
        </div>
    </div>
@endif
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
            --yellow: #b45309;
            --yellow-bg: #fef3c7;
            --blue: #2563eb;
            --blue-bg: #dbeafe;
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

        .calendar-page-wrapper {
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

        .today-btn {
            border: none;
            background: var(--coral);
            color: #ffffff;
            padding: 13px 22px;
            border-radius: 12px;
            font-weight: 900;
            font-family: inherit;
            cursor: pointer;
            box-shadow: 0 12px 28px rgba(217, 95, 74, 0.24);
        }

        .today-btn:hover {
            background: #c94f3d;
            transform: translateY(-1px);
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
            color: var(--text);
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

        .dropdown-link:hover {
            background: var(--coral-light);
            color: var(--coral);
        }

        .dropdown-link.logout {
            color: #e3342f;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 22px;
            margin-bottom: 26px;
        }

        .summary-card {
            min-height: 135px;
            padding: 24px;
            border-radius: var(--radius);
            background: linear-gradient(135deg, var(--coral), var(--coral-dark));
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 16px;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .summary-card::after {
            content: "";
            position: absolute;
            right: -45px;
            top: -55px;
            width: 170px;
            height: 170px;
            background: rgba(255, 255, 255, 0.12);
            border-radius: 50%;
        }

        .summary-icon {
            width: 58px;
            height: 58px;
            min-width: 58px;
            border-radius: 17px;
            background: #ffffff;
            color: var(--coral);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 23px;
            position: relative;
            z-index: 2;
        }

        .summary-card div:last-child {
            position: relative;
            z-index: 2;
        }

        .summary-card p,
        .summary-card h2,
        .summary-card span {
            color: #ffffff;
        }

        .summary-card p {
            margin: 0 0 5px;
            font-size: 14px;
            font-weight: 800;
            opacity: 0.95;
        }

        .summary-card h2 {
            margin: 0;
            font-size: 31px;
            font-weight: 900;
            line-height: 1.1;
        }

        .summary-card span {
            display: block;
            margin-top: 6px;
            font-size: 13px;
            font-weight: 700;
            opacity: 0.95;
        }

        .content-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.7fr) minmax(320px, 0.75fr);
            gap: 26px;
            align-items: start;
        }

        .calendar-card,
        .panel-card {
            background: rgba(255, 255, 255, 0.97);
            border: 1px solid rgba(225, 225, 225, 0.9);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        .calendar-card {
            padding: 24px;
            min-width: 0;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 18px;
            margin-bottom: 22px;
        }

        .calendar-header h2 {
            margin: 0;
            font-size: 26px;
            font-weight: 900;
            color: var(--text);
            text-align: center;
        }

        .calendar-header p {
            margin: 5px 0 0;
            color: var(--muted);
            font-size: 14px;
            font-weight: 700;
            text-align: center;
        }

        .month-btn {
            width: 42px;
            height: 42px;
            border: none;
            border-radius: 14px;
            background: var(--coral-light);
            color: var(--coral);
            cursor: pointer;
            font-size: 16px;
            transition: 0.2s ease;
        }

        .month-btn:hover {
            background: var(--coral);
            color: #ffffff;
        }

        .calendar-weekdays,
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
        }

        .calendar-weekdays {
            margin-bottom: 10px;
        }

        .calendar-weekdays div {
            text-align: center;
            color: var(--muted);
            font-size: 13px;
            font-weight: 900;
            padding: 10px;
        }

        .calendar-grid {
            gap: 10px;
        }

        .calendar-day {
            min-height: 118px;
            border: 1px solid #eeeeee;
            border-radius: 16px;
            background: #ffffff;
            padding: 10px;
            transition: 0.2s ease;
            overflow: hidden;
        }

        .calendar-day:hover {
            border-color: #ffd6cf;
            background: #fff7f4;
        }

        .muted-day {
            opacity: 0.45;
            background: #fafafa;
        }

        .calendar-day.today {
            border: 2px solid var(--blue);
            background: #eff6ff;
        }

        .calendar-day.wedding-day {
            border: 2px solid var(--coral);
            background: #fff1ee;
        }

        .day-number {
            width: 30px;
            height: 30px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            color: var(--text);
            margin-bottom: 8px;
        }

        .calendar-day.today .day-number {
            background: var(--blue);
            color: #ffffff;
        }

        .calendar-day.wedding-day .day-number {
            background: var(--coral);
            color: #ffffff;
        }

        .day-events {
            display: grid;
            gap: 5px;
        }

        .event-pill {
            border-radius: 999px;
            padding: 5px 8px;
            font-size: 11px;
            font-weight: 900;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .event-pill.wedding {
            background: var(--coral);
            color: #ffffff;
        }

        .event-pill.pending {
            background: var(--yellow-bg);
            color: var(--yellow);
        }

        .event-pill.completed {
            background: var(--green-bg);
            color: var(--green);
            text-decoration: line-through;
        }

        .more-events {
            color: var(--muted);
            font-size: 11px;
            font-weight: 800;
            padding-left: 4px;
        }

        .side-panel {
            display: grid;
            gap: 26px;
        }

        .panel-card {
            padding: 22px;
        }

        .panel-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 18px;
        }

        .panel-title h3,
        .legend-title {
            margin: 0;
            font-size: 21px;
            font-weight: 900;
            color: var(--text);
        }

        .panel-title a {
            color: var(--coral);
            text-decoration: none;
            font-size: 13px;
            font-weight: 900;
        }

        .schedule-list {
            display: grid;
            gap: 14px;
        }

        .schedule-item {
            display: grid;
            grid-template-columns: 54px minmax(0, 1fr) auto;
            gap: 12px;
            align-items: center;
            padding: 14px;
            border: 1px solid var(--border);
            border-radius: 16px;
            background: #fafafa;
        }

        .date-box {
            width: 54px;
            height: 54px;
            border-radius: 16px;
            background: var(--coral-light);
            color: var(--coral);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .date-box strong {
            font-size: 18px;
            font-weight: 900;
            line-height: 1;
        }

        .date-box span {
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            margin-top: 3px;
        }

        .schedule-info {
            min-width: 0;
        }

        .schedule-info h4 {
            margin: 0 0 4px;
            font-size: 14px;
            font-weight: 900;
            color: var(--text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .schedule-info p {
            margin: 0;
            font-size: 12px;
            color: var(--muted);
            font-weight: 700;
        }

        .status-badge {
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 11px;
            font-weight: 900;
            white-space: nowrap;
        }

        .status-badge.pending {
            background: var(--yellow-bg);
            color: var(--yellow);
        }

        .status-badge.completed {
            background: var(--green-bg);
            color: var(--green);
        }

        .legend-list {
            display: grid;
            gap: 14px;
            margin-top: 18px;
        }

        .legend-item {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    gap: 10px;
    color: #374151;
    font-size: 14px;
    font-weight: 800;
}

        .legend-dot {
            width: 13px;
            height: 13px;
            border-radius: 50%;
        }

        .legend-dot.wedding { background: var(--coral); }
        .legend-dot.pending { background: #f59e0b; }
        .legend-dot.completed { background: var(--green); }
        .legend-dot.today { background: var(--blue); }

        .empty-state {
            text-align: center;
            padding: 26px 12px;
            color: var(--muted);
        }

        .empty-state i {
            color: var(--coral);
            font-size: 32px;
            margin-bottom: 10px;
        }

        .empty-state h4 {
            margin: 0 0 6px;
            color: var(--text);
            font-size: 16px;
            font-weight: 900;
        }

        .empty-state p {
            margin: 0;
            font-size: 13px;
            font-weight: 700;
        }

        @media (max-width: 1250px) {
            .content-grid {
                grid-template-columns: 1fr;
            }

            .summary-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 900px) {
            .calendar-page-wrapper {
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

            .calendar-card {
                padding: 16px;
            }

            .calendar-grid {
                gap: 6px;
            }

            .calendar-day {
                min-height: 92px;
                padding: 7px;
                border-radius: 12px;
            }

            .calendar-weekdays div {
                font-size: 11px;
                padding: 8px 2px;
            }

            .event-pill {
                font-size: 10px;
                padding: 4px 6px;
            }

            .schedule-item {
                grid-template-columns: 48px minmax(0, 1fr);
            }

            .status-badge {
                grid-column: 2;
                width: fit-content;
            }

            .header-right,
            .today-btn {
                width: 100%;
            }
        }
        /* Floating hover effect */
.summary-card,
.calendar-card,
.panel-card {
    transition: 0.25s ease;
}

.summary-card:hover,
.calendar-card:hover,
.panel-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-hover);
}

.calendar-day.clickable-day {
    cursor: pointer;
}

.calendar-day.clickable-day:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 28px rgba(31, 41, 55, 0.12);
    border-color: var(--coral);
}

/* Task details modal */
.modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(17, 24, 39, 0.58);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    padding: 24px;
}

.task-modal {
    width: 100%;
    max-width: 720px;
    max-height: 88vh;
    overflow-y: auto;
    background: #ffffff;
    border-radius: 24px;
    padding: 28px;
    box-shadow: 0 25px 80px rgba(0, 0, 0, 0.25);
}

.task-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 18px;
    margin-bottom: 22px;
}

.task-modal-header h2 {
    margin: 0 0 6px;
    font-size: 27px;
    font-weight: 900;
    color: var(--text);
}

.task-modal-header span {
    color: var(--muted);
    font-size: 14px;
    font-weight: 800;
}

.eyebrow {
    margin: 0 0 6px;
    color: var(--coral);
    font-size: 12px;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 0.8px;
}

.modal-close-btn {
    width: 42px;
    height: 42px;
    border-radius: 14px;
    border: none;
    background: var(--coral-light);
    color: var(--coral);
    cursor: pointer;
    font-size: 18px;
    transition: 0.2s ease;
}

.modal-close-btn:hover {
    background: var(--coral);
    color: #ffffff;
}

.task-detail-list {
    display: grid;
    gap: 14px;
}

.task-detail-card {
    border: 1px solid #eeeeee;
    background: #fafafa;
    border-radius: 18px;
    padding: 18px;
    transition: 0.25s ease;
}

.task-detail-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 28px rgba(31, 41, 55, 0.08);
    background: #fff7f4;
    border-color: #ffd6cf;
}

.task-detail-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 14px;
    margin-bottom: 14px;
}

.task-detail-top h3 {
    margin: 0 0 6px;
    font-size: 18px;
    font-weight: 900;
    color: var(--text);
}

.task-detail-top p {
    margin: 0;
    color: #4b5563;
    font-size: 14px;
    line-height: 1.5;
}

.task-detail-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.task-detail-meta div {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: #ffffff;
    border: 1px solid #eeeeee;
    border-radius: 999px;
    padding: 8px 12px;
    color: #374151;
    font-size: 13px;
    font-weight: 800;
}

.task-detail-meta i {
    color: var(--coral);
}

.modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    border-top: 1px solid #f0f0f0;
    padding-top: 18px;
    margin-top: 22px;
}

.cancel-btn,
.solid-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    padding: 12px 20px;
    border: none;
    font-family: inherit;
    font-weight: 900;
    cursor: pointer;
    text-decoration: none;
}

.cancel-btn {
    background: #f3f4f6;
    color: #374151;
}

.cancel-btn:hover {
    background: #e5e7eb;
}

.solid-btn {
    background: var(--coral);
    color: #ffffff;
}

.solid-btn:hover {
    background: #c94f3d;
}

@media (max-width: 700px) {
    .task-modal {
        padding: 22px;
    }

    .task-detail-top,
    .modal-actions {
        flex-direction: column;
        align-items: stretch;
    }

    .cancel-btn,
    .solid-btn {
        width: 100%;
    }
}
/* ===== Make calendar legend more compact ===== */

.panel-card .legend-title {
    margin-bottom: 12px;
}

.legend-list {
    gap: 8px !important;
    margin-top: 10px !important;
}

.legend-item {
    justify-content: flex-start !important;
    gap: 10px !important;
    font-size: 14px !important;
    line-height: 1.2;
}

.legend-dot {
    width: 12px !important;
    height: 12px !important;
    min-width: 12px !important;
}

.panel-card {
    padding: 20px 22px !important;
}
.google-calendar-card {
    background: #ffffff;
    border: 1px solid rgba(225, 225, 225, 0.9);
    border-radius: 22px;
    padding: 22px;
    box-shadow: var(--shadow);
    display: flex;
    justify-content: space-between;
    gap: 18px;
    align-items: center;
    margin-bottom: 24px;
    transition: 0.25s ease;
}

.google-calendar-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-hover);
}

.google-calendar-card .eyebrow {
    margin: 0 0 6px;
    color: var(--coral);
    font-size: 12px;
    font-weight: 900;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}

.google-calendar-card h3 {
    margin: 0 0 7px;
    font-size: 21px;
    font-weight: 900;
    color: var(--text);
}

.google-calendar-card p {
    margin: 0;
    color: var(--muted);
    font-size: 14px;
    font-weight: 700;
    line-height: 1.5;
}

.google-calendar-card p strong {
    color: var(--text);
}

.google-calendar-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.google-sync-btn {
    height: 46px;
    padding: 0 16px;
    border-radius: 14px;
    border: none;
    background: var(--coral);
    color: #ffffff;
    text-decoration: none;
    font-size: 14px;
    font-weight: 900;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    cursor: pointer;
    transition: 0.25s ease;
    font-family: inherit;
    white-space: nowrap;
}

.google-sync-btn.secondary {
    background: #f3f4f6;
    color: #374151;
}

.google-sync-btn:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-hover);
}

.google-sync-btn.secondary:hover {
    background: var(--coral-light);
    color: var(--coral);
}

.sync-success,
.sync-error {
    margin-top: 12px;
    border-radius: 14px;
    padding: 11px 13px;
    font-size: 13px;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.sync-success {
    background: #ecfdf5;
    color: #047857;
}

.sync-error {
    background: #fef2f2;
    color: #dc2626;
}

@media (max-width: 850px) {
    .google-calendar-card {
        flex-direction: column;
        align-items: flex-start;
    }

    .google-calendar-actions,
    .google-calendar-actions form,
    .google-sync-btn {
        width: 100%;
    }
}
</style>
</div>
