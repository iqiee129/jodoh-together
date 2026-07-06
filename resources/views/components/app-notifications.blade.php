@php
    use Illuminate\Support\Facades\Auth;
    use App\Models\Expense;
    use App\Models\Task;
    use App\Models\WeddingDetail;
    use Carbon\Carbon;

    $user = Auth::user();

    $notifications = [];

    $weddingDetail = $user
        ? WeddingDetail::where('user_id', $user->id)->first()
        : null;

    /*
    |--------------------------------------------------------------------------
    | Budget Notification
    |--------------------------------------------------------------------------
    | Pending expenses are counted too because they still affect the wedding budget.
    */

    $totalBudget = $weddingDetail?->total_budget ?? $user?->budget ?? 0;

    $totalSpent = $user
        ? Expense::where('user_id', $user->id)->sum('amount')
        : 0;

    $budgetPercentage = $totalBudget > 0
        ? round(($totalSpent / $totalBudget) * 100)
        : 0;

    if ($totalBudget > 0 && $budgetPercentage >= 100) {
        $notifications[] = [
            'type' => 'danger',
            'icon' => 'fa-triangle-exclamation',
            'title' => 'Budget exceeded',
            'message' => 'Your expenses have reached RM ' . number_format($totalSpent, 0) . ', which is ' . $budgetPercentage . '% of your budget.',
            'link' => route('budget'),
        ];
    } elseif ($totalBudget > 0 && $budgetPercentage >= 80) {
        $notifications[] = [
            'type' => 'warning',
            'icon' => 'fa-wallet',
            'title' => 'Budget reached 80%',
            'message' => 'You have used ' . $budgetPercentage . '% of your total wedding budget.',
            'link' => route('budget'),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Task Notifications
    |--------------------------------------------------------------------------
    | Due Today = deadline is today
    | Overdue = deadline is before today
    | Due Soon = deadline is tomorrow until next 7 days
    */

    $today = now()->toDateString();
    $nextSevenDays = now()->addDays(7)->toDateString();

    $overdueTasks = $user
        ? Task::where('user_id', $user->id)
            ->where('status', '!=', 'completed')
            ->whereNotNull('deadline')
            ->whereDate('deadline', '<', $today)
            ->count()
        : 0;

    if ($overdueTasks > 0) {
        $notifications[] = [
            'type' => 'danger',
            'icon' => 'fa-clock',
            'title' => 'Tasks overdue',
            'message' => $overdueTasks . ' task' . ($overdueTasks === 1 ? ' is' : 's are') . ' overdue.',
            'link' => route('tasks'),
        ];
    }

    $dueTodayTasks = $user
        ? Task::where('user_id', $user->id)
            ->where('status', '!=', 'completed')
            ->whereNotNull('deadline')
            ->whereDate('deadline', '=', $today)
            ->count()
        : 0;

    if ($dueTodayTasks > 0) {
        $notifications[] = [
            'type' => 'today',
            'icon' => 'fa-calendar-day',
            'title' => 'Tasks due today',
            'message' => $dueTodayTasks . ' task' . ($dueTodayTasks === 1 ? ' is' : 's are') . ' due today.',
            'link' => route('tasks'),
        ];
    }

    $dueSoonTasks = $user
        ? Task::where('user_id', $user->id)
            ->where('status', '!=', 'completed')
            ->whereNotNull('deadline')
            ->whereDate('deadline', '>', $today)
            ->whereDate('deadline', '<=', $nextSevenDays)
            ->count()
        : 0;

    if ($dueSoonTasks > 0) {
        $notifications[] = [
            'type' => 'info',
            'icon' => 'fa-list-check',
            'title' => 'Tasks due soon',
            'message' => $dueSoonTasks . ' task' . ($dueSoonTasks === 1 ? ' is' : 's are') . ' due within the next 7 days.',
            'link' => route('tasks'),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Wedding Date Notification
    |--------------------------------------------------------------------------
    */

    $weddingDate = $weddingDetail?->wedding_date ?? $user?->wedding_date ?? null;

    if ($weddingDate) {
        $daysUntilWedding = now()
            ->startOfDay()
            ->diffInDays(Carbon::parse($weddingDate)->startOfDay(), false);

        if ($daysUntilWedding >= 0 && $daysUntilWedding <= 30) {
            $notifications[] = [
                'type' => 'love',
                'icon' => 'fa-heart',
                'title' => 'Wedding day is near',
                'message' => $daysUntilWedding === 0
                    ? 'Your wedding day is today.'
                    : 'Only ' . $daysUntilWedding . ' day' . ($daysUntilWedding === 1 ? '' : 's') . ' left until your wedding.',
                'link' => route('my-wedding'),
            ];
        }
    }

    $notificationCount = count($notifications);
@endphp

<div class="app-notification-wrap">
    <button type="button" class="notification app-notification-btn" aria-label="Notifications">
        <i class="fa-regular fa-bell"></i>

        @if ($notificationCount > 0)
            <span class="badge">{{ $notificationCount }}</span>
        @endif
    </button>

    <div class="app-notification-dropdown">
        <div class="notification-dropdown-header">
            <div>
                <h3>Notifications</h3>
                <p>{{ $notificationCount }} update{{ $notificationCount === 1 ? '' : 's' }}</p>
            </div>
        </div>

        <div class="notification-list">
            @forelse ($notifications as $item)
                <a href="{{ $item['link'] }}" class="notification-item {{ $item['type'] }}" wire:navigate>
                    <div class="notification-item-icon">
                        <i class="fa-solid {{ $item['icon'] }}"></i>
                    </div>

                    <div class="notification-item-content">
                        <strong>{{ $item['title'] }}</strong>
                        <span>{{ $item['message'] }}</span>
                    </div>
                </a>
            @empty
                <div class="empty-notification">
                    <i class="fa-regular fa-circle-check"></i>
                    <strong>No notifications</strong>
                    <span>Everything looks good for now.</span>
                </div>
            @endforelse
        </div>
    </div>
</div>

<script>
    function initAppNotifications() {
        document.querySelectorAll(".app-notification-wrap").forEach((wrap) => {
            const button = wrap.querySelector(".app-notification-btn");

            if (!button || button.dataset.ready === "true") {
                return;
            }

            button.dataset.ready = "true";

            button.addEventListener("click", (event) => {
                event.stopPropagation();

                document.querySelectorAll(".app-notification-wrap.open").forEach((openWrap) => {
                    if (openWrap !== wrap) {
                        openWrap.classList.remove("open");
                    }
                });

                wrap.classList.toggle("open");
            });
        });
    }

    document.addEventListener("click", () => {
        document.querySelectorAll(".app-notification-wrap.open").forEach((wrap) => {
            wrap.classList.remove("open");
        });
    });

    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape") {
            document.querySelectorAll(".app-notification-wrap.open").forEach((wrap) => {
                wrap.classList.remove("open");
            });
        }
    });

    document.addEventListener("DOMContentLoaded", initAppNotifications);
    document.addEventListener("livewire:navigated", initAppNotifications);
</script>

<style>
    .app-notification-wrap {
        position: relative;
    }

    .app-notification-wrap .notification {
        position: relative;
        width: 42px;
        height: 42px;
        border-radius: 50%;
        border: none;
        background: transparent;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: 0.2s ease;
    }

    .app-notification-wrap .notification:hover {
        background: #ffffff;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
    }

    .app-notification-wrap .notification i {
        font-size: 22px;
        color: #111827;
    }

    .app-notification-wrap .badge {
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

    .app-notification-dropdown {
        position: absolute;
        top: 56px;
        right: 0;
        width: 380px;
        max-width: calc(100vw - 32px);
        background: #ffffff;
        border: 1px solid #eeeeee;
        border-radius: 20px;
        box-shadow: 0 20px 55px rgba(0, 0, 0, 0.14);
        padding: 14px;
        display: none;
        z-index: 100;
    }

    .app-notification-wrap.open .app-notification-dropdown {
        display: block;
    }

    .notification-dropdown-header {
        padding: 10px 10px 14px;
        border-bottom: 1px solid #f0f0f0;
        margin-bottom: 10px;
    }

    .notification-dropdown-header h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 900;
        color: #111827;
    }

    .notification-dropdown-header p {
        margin: 4px 0 0;
        color: #6b7280;
        font-size: 13px;
        font-weight: 700;
    }

    .notification-list {
        display: grid;
        gap: 8px;
        max-height: 360px;
        overflow-y: auto;
        padding-right: 2px;
    }

    .notification-list::-webkit-scrollbar {
        width: 6px;
    }

    .notification-list::-webkit-scrollbar-thumb {
        background: #e5e7eb;
        border-radius: 999px;
    }

    .notification-item {
        display: flex;
        gap: 12px;
        padding: 12px;
        border-radius: 15px;
        text-decoration: none;
        border: 1px solid #f0f0f0;
        background: #fafafa;
        transition: 0.2s ease;
    }

    .notification-item:hover {
        background: #fff7f4;
        border-color: #ffd6cf;
        transform: translateY(-2px);
    }

    .notification-item-icon {
        width: 40px;
        height: 40px;
        min-width: 40px;
        border-radius: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
    }

    .notification-item.warning .notification-item-icon {
        background: #f59e0b;
    }

    .notification-item.danger .notification-item-icon {
        background: #dc2626;
    }

    .notification-item.info .notification-item-icon {
        background: #2563eb;
    }

    .notification-item.today .notification-item-icon {
        background: #d95f4a;
    }

    .notification-item.love .notification-item-icon {
        background: #d95f4a;
    }

    .notification-item-content {
        min-width: 0;
    }

    .notification-item-content strong {
        display: block;
        color: #111827;
        font-size: 14px;
        font-weight: 900;
        margin-bottom: 4px;
    }

    .notification-item-content span {
        display: block;
        color: #6b7280;
        font-size: 13px;
        font-weight: 700;
        line-height: 1.4;
    }

    .empty-notification {
        padding: 26px 16px;
        text-align: center;
        color: #6b7280;
    }

    .empty-notification i {
        font-size: 32px;
        color: #15803d;
        margin-bottom: 10px;
    }

    .empty-notification strong {
        display: block;
        color: #111827;
        font-size: 15px;
        font-weight: 900;
        margin-bottom: 4px;
    }

    .empty-notification span {
        font-size: 13px;
        font-weight: 700;
    }

    @media (max-width: 650px) {
        .app-notification-dropdown {
            right: -80px;
            width: 320px;
        }
    }
</style>