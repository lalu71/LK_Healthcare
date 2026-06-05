<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppNotification extends Model
{
    protected $table = 'app_notifications';

    protected $fillable = ['user_id', 'title', 'body', 'icon', 'link', 'read_at'];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread($q)
    {
        return $q->whereNull('read_at');
    }

    public static function pushFor(int $userId, string $title, ?string $body = null, ?string $link = null, string $icon = 'bell'): self
    {
        return self::create([
            'user_id' => $userId, 'title' => $title, 'body' => $body, 'link' => $link, 'icon' => $icon,
        ]);
    }
}
