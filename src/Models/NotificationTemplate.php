<?php

namespace AdvancedNotifications\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class NotificationTemplate extends Model
{
    use HasTranslations;

    protected $fillable = [
        'key',
        'name',
        'title',
        'body',
        'type',
        'icon',
        'action_url',
        'metadata',
        'is_active',
    ];

    public $translatable = ['title', 'body'];

    protected $casts = [
        'metadata' => 'array',
        'is_active' => 'boolean',
    ];
}
