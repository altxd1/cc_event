<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
    /**
     * The attributes that are mass assignable.
     *
     * We explicitly list the columns present in our custom users table
     * so that Eloquent can correctly perform mass assignment on these
     * attributes. "username" and "full_name" are required at registration,
     * while "phone" and "user_type" may be optional. The password field
     * will be hashed automatically by Laravel when using the built-in
     * authentication scaffolding.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'full_name',
        'email',
        'phone',
        'password',
        'user_type',
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
            // Use Laravel's built-in hashed casting for the password
            'password' => 'hashed',
        ];
    }

    /**
     * The table associated with the model.
     *
     * Set explicitly to align with our migration where the primary key is
     * "user_id" rather than the default "id". We also specify the
     * primary key and incrementing details to ensure Eloquent uses the
     * correct column when performing queries.
     */
    protected $table = 'users';

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'user_id';

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = true;

    /**
     * The "type" of the auto-incrementing ID.
     */
    protected $keyType = 'int';

    /**
     * Constants defining available user roles. Storing these values here
     * avoids magic strings throughout the application and makes it
     * straightforward to add new roles in the future.
     */
    public const ROLE_CLIENT        = 'client';
    public const ROLE_ADMIN         = 'admin';
    public const ROLE_EVENT_MANAGER = 'event_manager';

    /**
     * Check if the current user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->user_type === self::ROLE_ADMIN;
    }

    /**
     * Check if the user is an event manager. Event managers have
     * administrative privileges for events but may not have full system
     * access like a super admin.
     */
    public function isEventManager(): bool
    {
        return $this->user_type === self::ROLE_EVENT_MANAGER;
    }

    /**
     * Check if the user is a client.
     */
    public function isClient(): bool
    {
        return $this->user_type === self::ROLE_CLIENT;
    }
}
