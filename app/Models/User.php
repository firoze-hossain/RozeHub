<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'is_admin', 'is_developer', 'account_type'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_developer' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    public function isDeveloper(): bool
    {
        return (bool) $this->is_developer || (bool) $this->is_admin;
    }

    public function isNormalUser(): bool
    {
        return !$this->is_admin && !$this->is_developer;
    }

    public function getRoleBadge(): string
    {
        if ($this->isAdmin()) return 'Administrator';
        if ($this->isDeveloper()) return 'Developer / Publisher';
        return 'Standard User';
    }

    public function releaseUpdateNotifications(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\ReleaseUpdateNotification::class);
    }

    public function publisherProfile() { return $this->hasOne(\App\Models\PublisherProfile::class); }
    public function marketplaceReviews() { return $this->hasMany(\App\Models\MarketplaceReview::class); }
}
