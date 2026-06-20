<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['branch_id', 'role_id', 'name', 'email', 'phone', 'password', 'status', 'last_login_at'])]
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
            'last_login_at' => 'datetime',
        ];
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Check if the user has a specific role.
     */
    public function hasRole(string $roleSlug): bool
    {
        if (!$this->relationLoaded('role')) {
            $this->load('role');
        }
        return $this->role && $this->role->slug === $roleSlug;
    }

    /**
     * Check if the user has a specific permission.
     */
    public function hasPermission(string $permissionSlug): bool
    {
        if ($this->hasRole('super-admin')) {
            return true;
        }

        if (!$this->relationLoaded('role')) {
            $this->load('role.permissions');
        } else if (!$this->role->relationLoaded('permissions')) {
            $this->role->load('permissions');
        }

        return $this->role && $this->role->permissions->contains('slug', $permissionSlug);
    }
}
