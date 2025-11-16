<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'city',
        'photo',
        'is_active',
        'uuid',
        'google2fa_secret',
        'google2fa_enabled',
        'google2fa_enabled_at',
        'backup_codes',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'google2fa_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'google2fa_enabled' => 'boolean',
        'google2fa_enabled_at' => 'datetime',
        'backup_codes' => 'array',
    ];

    /**
     * Boot method to generate UUID automatically
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    /**
     * Find a user by UUID
     */
    public static function findByUuid($uuid)
    {
        return static::where('uuid', $uuid)->first();
    }

    // Relations
    public function clients()
    {
        return $this->hasMany(Client::class, 'agent_id');
    }

    public function tontines()
    {
        return $this->hasMany(Tontine::class, 'agent_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'collected_by');
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAgents($query)
    {
        return $query->role('agent');
    }

    public function scopeSecretaries($query)
    {
        return $query->role('secretary');
    }

    // Relations pour la messagerie
    public function conversations()
    {
        return $this->belongsToMany(Conversation::class, 'conversation_participants')
                    ->withPivot('joined_at', 'last_read_at')
                    ->withTimestamps()
                    ->orderByRaw('COALESCE(conversations.last_message_at, conversations.created_at) DESC');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function createdConversations()
    {
        return $this->hasMany(Conversation::class, 'created_by');
    }

    // Méthodes utilitaires pour la messagerie
    public function canMessageUser(User $user)
    {
        // Super admin peut parler à tout le monde
        if ($this->hasRole('super_admin')) {
            return true;
        }

        // Secrétaires peuvent parler aux agents et super admin
        if ($this->hasRole('secretary')) {
            return $user->hasRole(['agent', 'super_admin']);
        }

        // Agents peuvent parler aux secrétaires et super admin
        if ($this->hasRole('agent')) {
            return $user->hasRole(['secretary', 'super_admin']);
        }

        return false;
    }

    public function getConversationWith(User $user)
    {
        // Récupérer toutes les conversations privées de l'utilisateur actuel
        $conversations = $this->conversations()->where('type', 'private')->get();
        
        // Filtrer pour trouver celle qui contient exactement les deux utilisateurs
        foreach ($conversations as $conversation) {
            $participantIds = $conversation->users()->pluck('user_id')->toArray();
            if (count($participantIds) === 2 && in_array($user->id, $participantIds)) {
                return $conversation;
            }
        }
        
        return null;
    }
}
