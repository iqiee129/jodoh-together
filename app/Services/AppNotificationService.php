<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\Task;
use App\Models\User;
use App\Models\WeddingDetail;
use Carbon\Carbon;

class AppNotificationService
{
    public function getNotificationsForUser(User $user): array
    {
        $notifications = [];

        $weddingDetail = WeddingDetail::where('user_id', $user->id)->first();

        /*
        |--------------------------------------------------------------------------
        | Budget Notifications
        |--------------------------------------------------------------------------
        */

        $totalBudget = $weddingDetail?->total_budget ?? 0;

        $totalSpent = Expense::where('user_id', $user->id)
            ->sum('amount');

        $budgetPercentage = $totalBudget > 0
            ? round(($totalSpent / $totalBudget) * 100)
            : 0;

        if ($totalBudget > 0 && $budgetPercentage >= 100) {
            $notifications[] = [
                'key' => 'budget_exceeded',
                'type' => 'danger',
                'title' => 'Budget Exceeded',
                'message' => 'Your wedding expenses have reached RM ' . number_format($totalSpent, 0) . ', which is ' . $budgetPercentage . '% of your total budget.',
                'link' => url('/budget'),
            ];
        } elseif ($totalBudget > 0 && $budgetPercentage >= 80) {
            $notifications[] = [
                'key' => 'budget_warning_80',
                'type' => 'warning',
                'title' => 'Budget Warning',
                'message' => 'You have used ' . $budgetPercentage . '% of your total wedding budget.',
                'link' => url('/budget'),
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

        $overdueTasks = Task::where('user_id', $user->id)
            ->where('status', '!=', 'completed')
            ->whereNotNull('deadline')
            ->whereDate('deadline', '<', $today)
            ->count();

        if ($overdueTasks > 0) {
            $notifications[] = [
                'key' => 'overdue_tasks',
                'type' => 'danger',
                'title' => 'Tasks Overdue',
                'message' => $overdueTasks . ' wedding task' . ($overdueTasks === 1 ? ' is' : 's are') . ' overdue.',
                'link' => url('/tasks'),
            ];
        }

        $dueTodayTasks = Task::where('user_id', $user->id)
            ->where('status', '!=', 'completed')
            ->whereNotNull('deadline')
            ->whereDate('deadline', '=', $today)
            ->count();

        if ($dueTodayTasks > 0) {
            $notifications[] = [
                'key' => 'tasks_due_today',
                'type' => 'warning',
                'title' => 'Tasks Due Today',
                'message' => $dueTodayTasks . ' wedding task' . ($dueTodayTasks === 1 ? ' is' : 's are') . ' due today.',
                'link' => url('/tasks'),
            ];
        }

        $dueSoonTasks = Task::where('user_id', $user->id)
            ->where('status', '!=', 'completed')
            ->whereNotNull('deadline')
            ->whereDate('deadline', '>', $today)
            ->whereDate('deadline', '<=', $nextSevenDays)
            ->count();

        if ($dueSoonTasks > 0) {
            $notifications[] = [
                'key' => 'tasks_due_soon',
                'type' => 'info',
                'title' => 'Tasks Due Soon',
                'message' => $dueSoonTasks . ' wedding task' . ($dueSoonTasks === 1 ? ' is' : 's are') . ' due within the next 7 days.',
                'link' => url('/tasks'),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Wedding Date Notification
        |--------------------------------------------------------------------------
        */

        $weddingDate = $weddingDetail?->wedding_date ?? null;

        if ($weddingDate) {
            $daysUntilWedding = now()
                ->startOfDay()
                ->diffInDays(Carbon::parse($weddingDate)->startOfDay(), false);

            if ($daysUntilWedding >= 0 && $daysUntilWedding <= 30) {
                $notifications[] = [
                    'key' => 'wedding_day_near',
                    'type' => 'love',
                    'title' => 'Wedding Day Is Near',
                    'message' => $daysUntilWedding === 0
                        ? 'Your wedding day is today.'
                        : 'Only ' . $daysUntilWedding . ' day' . ($daysUntilWedding === 1 ? '' : 's') . ' left until your wedding.',
                    'link' => url('/my/wedding'),
                ];
            }
        }

        return $notifications;
    }
}