<?php

namespace App\Models;

use App\Enums\Gender;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'address',
        'contact_no',
        'gender',
        'status',
        'date_of_birth',
        'age',
        'parent_name',
        'email',
        'password',
        'role',
        'must_change_password',
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'gender' => Gender::class,
            'role' => UserRole::class,
            'status' => UserStatus::class,
            'must_change_password' => 'bool',
            'date_of_birth' => 'date',
            'age' => 'int',
        ];
    }

    public function getNameAttribute(): string
    {
        return $this->full_name;
    }

    public function getFullNameAttribute(): string
    {
        return trim(implode(' ', array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ])));
    }

    public function clientSessions(): HasMany
    {
        return $this->hasMany(Session::class, 'client_id');
    }

    public function therapistSessions(): HasMany
    {
        return $this->hasMany(Session::class, 'therapist_id');
    }

    public function assistantSessions(): HasMany
    {
        return $this->hasMany(Session::class, 'assistant_id');
    }

    public function isRole(UserRole|string $role): bool
    {
        $value = $role instanceof UserRole ? $role->value : $role;
        $currentRole = $this->role instanceof UserRole ? $this->role->value : (string) $this->role;

        return $currentRole === $value;
    }
}
