<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\User;

class Notify
{
    public static function send(int $userId, string $title, ?string $body = null, ?string $link = null, string $icon = 'bell'): void
    {
        AppNotification::create([
            'user_id' => $userId,
            'title' => $title,
            'body' => $body,
            'link' => $link,
            'icon' => $icon,
        ]);
    }

    public static function admins(string $title, ?string $body = null, ?string $link = null, string $icon = 'bell'): void
    {
        User::role('admin')->pluck('id')->each(fn($id) => self::send($id, $title, $body, $link, $icon));
    }
}
