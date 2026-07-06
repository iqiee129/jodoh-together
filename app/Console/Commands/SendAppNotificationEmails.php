<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\AppEmailNotification;
use App\Services\AppNotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendAppNotificationEmails extends Command
{
    protected $signature = 'app:send-notification-emails';

    protected $description = 'Send daily Jodoh Together notification emails once per day.';

    public function handle(AppNotificationService $notificationService): int
    {
        $today = now()->toDateString();
        $sentCount = 0;

        User::query()
            ->where(function ($query) {
                $query->where('role', '!=', 'admin')
                    ->orWhereNull('role');
            })
            ->chunk(100, function ($users) use ($notificationService, $today, &$sentCount) {
                foreach ($users as $user) {
                    $notifications = $notificationService->getNotificationsForUser($user);

                    foreach ($notifications as $notification) {
                        $alreadySent = DB::table('sent_app_notifications')
                            ->where('user_id', $user->id)
                            ->where('notification_key', $notification['key'])
                            ->where('sent_for_date', $today)
                            ->exists();

                        if ($alreadySent) {
                            continue;
                        }

                        $user->notify(new AppEmailNotification(
                            title: $notification['title'],
                            message: $notification['message'],
                            url: $notification['link'],
                            type: $notification['type']
                        ));

                        DB::table('sent_app_notifications')->insert([
                            'user_id' => $user->id,
                            'notification_key' => $notification['key'],
                            'sent_for_date' => $today,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        $sentCount++;
                    }
                }
            });

        $this->info($sentCount . ' notification email(s) sent.');

        return self::SUCCESS;
    }
}