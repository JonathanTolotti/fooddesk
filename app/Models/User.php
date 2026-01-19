<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'login',
        'email',
        'password',
        'role',
        'uuid',
        'status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Boot the model - gera UUID automaticamente ao criar
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (User $user): void {
            if (empty($user->uuid)) {
                $user->uuid = Str::uuid();
            }
        });
    }

    /**
     * Route model binding usa UUID ao invés de ID
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

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
            'role' => UserRole::class,
            'status' => 'boolean',
        ];
    }

    public function isManager():bool
    {
        return $this->role === UserRole::Manager;
    }

    public function isWaiter():bool
    {
        return $this->role === UserRole::Waiter;
    }

    public function isKitchen():bool
    {
        return $this->role === UserRole::Kitchen;
    }

    public function isCustomer():bool
    {
        return $this->role === UserRole::Customer;
    }

    public function hasRole(UserRole ...$roles):bool
    {
        return in_array($this->role, $roles);
    }
}
