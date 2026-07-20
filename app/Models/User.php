<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\AgentLevel;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'role',
        'password',
        'password_changed_at',
        'is_active',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password_changed_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'agent_level' => AgentLevel::class,
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isAgent(): bool
    {
        return $this->role === 'agent';
    }

    public function isLivreur(): bool
    {
        return $this->role === 'livreur';
    }

    public function isCuisinier(): bool
    {
        return $this->role === 'cuisinier';
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    public function mustChangePassword(): bool
    {
        return $this->password_changed_at === null;
    }

    public function ordersAsAgent(): HasMany
    {
        return $this->hasMany(Order::class, 'agent_id');
    }

    public function ordersAsClient(): HasMany
    {
        return $this->hasMany(Order::class, 'client_id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'client_id');
    }

    public function agentSubscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'agent_id');
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class, 'livreur_id');
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class, 'agent_id');
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class, 'agent_id');
    }

    public function fcmTokens(): HasMany
    {
        return $this->hasMany(FcmToken::class);
    }

    public function agentPoints(): HasMany
    {
        return $this->hasMany(AgentPoint::class, 'agent_id');
    }

    public function agentRewards(): HasMany
    {
        return $this->hasMany(AgentReward::class, 'agent_id');
    }

    public function badges(): HasMany
    {
        return $this->hasMany(Badge::class, 'agent_id');
    }

    public function leaderboardEntries(): HasMany
    {
        return $this->hasMany(LeaderboardEntry::class, 'agent_id');
    }

    public function appNotifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function unreadAppNotifications(): HasMany
    {
        return $this->hasMany(Notification::class)->where('is_read', false);
    }
}
