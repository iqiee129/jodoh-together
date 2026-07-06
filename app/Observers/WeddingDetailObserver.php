<?php

namespace App\Observers;

use App\Models\User;
use App\Models\WeddingDetail;
use App\Services\GoogleCalendarService;

class WeddingDetailObserver
{
    public function saved(WeddingDetail $weddingDetail): void
    {
        if (! $weddingDetail->wedding_date) {
            return;
        }

        $user = User::find($weddingDetail->user_id);

        if (! $user || ! $user->google_calendar_connected_at) {
            return;
        }

        if (! $user->google_token && ! $user->google_refresh_token) {
            return;
        }

        try {
            $event = app(GoogleCalendarService::class)->syncAllDayEvent($user, [
                'title' => 'Wedding Day',
                'date' => $weddingDetail->wedding_date,
                'description' => 'Wedding day with ' . ($weddingDetail->partner_name ?? 'your partner') .
                    '. Venue: ' . ($weddingDetail->venue ?? '-'),
            ], $weddingDetail->google_event_id);

            $weddingDetail->forceFill([
                'google_event_id' => $event['id'] ?? $weddingDetail->google_event_id,
            ])->saveQuietly();
        } catch (\Throwable $e) {
            report($e);
        }
    }

    public function deleted(WeddingDetail $weddingDetail): void
    {
        $user = User::find($weddingDetail->user_id);

        if (! $user || ! $weddingDetail->google_event_id) {
            return;
        }

        try {
            app(GoogleCalendarService::class)->deleteEvent($user, $weddingDetail->google_event_id);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}