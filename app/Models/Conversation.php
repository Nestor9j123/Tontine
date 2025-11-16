<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'title',
        'type',
        'created_by',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($conversation) {
            if (empty($conversation->uuid)) {
                $conversation->uuid = Str::uuid();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    // Relations
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participants()
    {
        return $this->hasMany(ConversationParticipant::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'conversation_participants')
                    ->withPivot('joined_at', 'last_read_at')
                    ->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    // Méthodes utilitaires
    public function addParticipant(User $user)
    {
        return $this->participants()->firstOrCreate([
            'user_id' => $user->id,
        ], [
            'joined_at' => now(),
        ]);
    }

    public function removeParticipant(User $user)
    {
        return $this->participants()->where('user_id', $user->id)->delete();
    }

    public function isParticipant(User $user)
    {
        return $this->participants()->where('user_id', $user->id)->exists();
    }

    public function getUnreadCount(User $user)
    {
        $participant = $this->participants()->where('user_id', $user->id)->first();
        
        if (!$participant) {
            return 0;
        }

        return $this->messages()
                    ->where('user_id', '!=', $user->id)
                    ->where('created_at', '>', $participant->last_read_at ?? $participant->joined_at)
                    ->count();
    }

    public function markAsRead(User $user)
    {
        return $this->participants()
                    ->where('user_id', $user->id)
                    ->update(['last_read_at' => now()]);
    }
}
