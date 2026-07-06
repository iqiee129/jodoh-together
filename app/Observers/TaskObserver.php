<?php

namespace App\Observers;

use App\Models\Task;
use App\Models\User;
use App\Services\GoogleCalendarService;

class TaskObserver
{
    public function saved(Task $task): void
    {
        if (! $task->deadline) {
            return;
        }

        $user = User::find($task->user_id);

        if (! $user || ! $user->google_calendar_connected_at) {
            return;
        }

        if (! $user->google_token && ! $user->google_refresh_token) {
            return;
        }

        try {
            $event = app(GoogleCalendarService::class)->syncAllDayEvent($user, [
                'title' => 'Wedding Task: ' . $task->title,
                'date' => $task->deadline,
                'description' =>
                    'Status: ' . ucfirst($task->status ?? 'pending') . "\n" .
                    'Category: ' . ucfirst($task->category ?? 'General') . "\n" .
                    'Priority: ' . ucfirst($task->priority ?? 'Medium') . "\n" .
                    'Description: ' . ($task->description ?? '-'),
            ], $task->google_event_id);

            $task->forceFill([
                'google_event_id' => $event['id'] ?? $task->google_event_id,
            ])->saveQuietly();
        } catch (\Throwable $e) {
            report($e);
        }
    }

    public function deleted(Task $task): void
    {
        $user = User::find($task->user_id);

        if (! $user || ! $task->google_event_id) {
            return;
        }

        try {
            app(GoogleCalendarService::class)->deleteEvent($user, $task->google_event_id);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}