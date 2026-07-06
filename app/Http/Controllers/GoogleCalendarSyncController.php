<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\WeddingDetail;
use App\Services\GoogleCalendarService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class GoogleCalendarSyncController extends Controller
{
    public function sync(GoogleCalendarService $calendar)
    {
        $user = Auth::user();

        if (! $user->google_token && ! $user->google_refresh_token) {
            return redirect()->route('google.redirect');
        }

        $syncedCount = 0;

        try {
            if (Schema::hasTable('wedding_details')) {
                $wedding = WeddingDetail::where('user_id', $user->id)->first();

                if ($wedding && $wedding->wedding_date) {
                    $event = $calendar->syncAllDayEvent($user, [
                        'title' => 'Wedding Day',
                        'date' => $wedding->wedding_date,
                        'description' => 'Wedding day with ' . ($wedding->partner_name ?? 'your partner') .
                            '. Venue: ' . ($wedding->venue ?? '-'),
                    ], $wedding->google_event_id);

                    $wedding->update([
                        'google_event_id' => $event['id'] ?? $wedding->google_event_id,
                    ]);

                    $syncedCount++;
                }
            }

            if (Schema::hasTable('tasks')) {
                $tasks = Task::where('user_id', $user->id)
                    ->whereNotNull('deadline')
                    ->get();

                foreach ($tasks as $task) {
                    $event = $calendar->syncAllDayEvent($user, [
                        'title' => 'Wedding Task: ' . $task->title,
                        'date' => $task->deadline,
                        'description' =>
                            'Status: ' . ucfirst($task->status ?? 'pending') . "\n" .
                            'Category: ' . ucfirst($task->category ?? 'General') . "\n" .
                            'Priority: ' . ucfirst($task->priority ?? 'Medium') . "\n" .
                            'Description: ' . ($task->description ?? '-'),
                    ], $task->google_event_id);

                    $task->update([
                        'google_event_id' => $event['id'] ?? $task->google_event_id,
                    ]);

                    $syncedCount++;
                }
            }

            return back()->with('success', $syncedCount . ' item(s) synced to Google Calendar.');
        } catch (\Throwable $e) {
            report($e);

            return back()->with('calendar_error', 'Google Calendar sync failed. Please reconnect Google and try again.');
        }
    }
}
