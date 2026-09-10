<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'password',
        'role',
        'status',
        'registered_via'
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
        ];
    }

    public function admin(): HasOne
    {
        return $this->hasOne(Admin::class);
    }

    public function resident(): HasOne
    {
        return $this->hasOne(Resident::class);
    }

    /**
     * Get the specific analytics role for the user based on their general role and admin position.
     */
    public function getAnalyticsRoleAttribute(): string
    {
        if ($this->role === 'admin') {
            return 'captain'; // Full access
        }
        if ($this->role === 'staff' && $this->admin) {
            switch ($this->admin->position) {
                case 'Barangay Secretary':
                    return 'secretary'; // Full access
                case 'Barangay Clerk':
                    return 'clerk'; // Limited access
                case 'Barangay Treasurer':
                    return 'treasurer'; // Payment access
                case 'Lupon Member':
                    return 'lupon'; // Blotter/Case access
                default:
                    return 'staff'; // Default staff, minimal access
            }
        }
        return 'guest'; // No access
    }
}
