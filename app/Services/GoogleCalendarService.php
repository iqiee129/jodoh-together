<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleCalendarService
{
    public function syncAllDayEvent(User $user, array $eventData, ?string $googleEventId = null): array
    {
        $token = $this->validAccessToken($user);

        $date = Carbon::parse($eventData['date']);

        $payload = [
            'summary' => $eventData['title'],
            'description' => $eventData['description'] ?? '',
            'start' => [
                'date' => $date->toDateString(),
                'timeZone' => 'Asia/Kuala_Lumpur',
            ],
            'end' => [
                'date' => $date->copy()->addDay()->toDateString(),
                'timeZone' => 'Asia/Kuala_Lumpur',
            ],
            'reminders' => [
                'useDefault' => false,
                'overrides' => [
                    [
                        'method' => 'popup',
                        'minutes' => 1440,
                    ],
                ],
            ],
        ];

        if ($googleEventId) {
            $response = Http::withToken($token)
                ->patch(
                    'https://www.googleapis.com/calendar/v3/calendars/primary/events/' . rawurlencode($googleEventId),
                    $payload
                );
        } else {
            $response = Http::withToken($token)
                ->post('https://www.googleapis.com/calendar/v3/calendars/primary/events', $payload);
        }

        if ($response->failed()) {
            Log::warning('Google Calendar sync failed.', [
                'user_id' => $user->id,
                'status' => $response->status(),
            ]);

            throw new \RuntimeException('Google Calendar sync failed. Please reconnect Google and try again.');
        }

        return $response->json();
    }

    public function deleteEvent(User $user, ?string $googleEventId): void
    {
        if (! $googleEventId) {
            return;
        }

        $token = $this->validAccessToken($user);

        $response = Http::withToken($token)
            ->delete('https://www.googleapis.com/calendar/v3/calendars/primary/events/' . rawurlencode($googleEventId));

        if ($response->failed() && $response->status() !== 404 && $response->status() !== 410) {
            Log::warning('Google Calendar delete failed.', [
                'user_id' => $user->id,
                'status' => $response->status(),
            ]);

            throw new \RuntimeException('Google Calendar delete failed. Please reconnect Google and try again.');
        }
    }

    private function validAccessToken(User $user): string
    {
        if (
            $user->google_token &&
            $user->google_token_expires_at &&
            $user->google_token_expires_at->isFuture()
        ) {
            return $user->google_token;
        }

        if (! $user->google_refresh_token) {
            throw new \Exception('Google Calendar is not connected. Please reconnect Google.');
        }

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'client_id' => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
            'refresh_token' => $user->google_refresh_token,
            'grant_type' => 'refresh_token',
        ]);

        if ($response->failed()) {
            Log::warning('Unable to refresh Google token.', [
                'user_id' => $user->id,
                'status' => $response->status(),
            ]);

            throw new \RuntimeException('Unable to refresh Google token. Please reconnect Google and try again.');
        }

        $data = $response->json();

        $user->forceFill([
            'google_token' => $data['access_token'],
            'google_token_expires_at' => now()->addSeconds($data['expires_in'] ?? 3600),
        ])->save();

        return $data['access_token'];
    }
}
