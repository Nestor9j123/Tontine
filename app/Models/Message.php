<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'conversation_id',
        'user_id',
        'content',
        'type',
        'attachments',
        'read_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'read_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($message) {
            if (empty($message->uuid)) {
                $message->uuid = Str::uuid();
            }
        });

        static::created(function ($message) {
            // Mettre à jour le timestamp de la conversation
            $message->conversation->update([
                'last_message_at' => $message->created_at,
            ]);
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    // Relations
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accesseurs
    public function getIsReadAttribute()
    {
        return !is_null($this->read_at);
    }

    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}
