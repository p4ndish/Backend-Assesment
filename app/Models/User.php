<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;


    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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
            'role' => 'string',
        ];
    }

    /**
     * Get the jobs created by this user (company role)
     */
    public function jobs()
    {
        return $this->hasMany(JobListing::class, 'created_by');
    }

    /**
     * Get the applications submitted by this user (applicant role)
     */
    public function applications()
    {
        return $this->hasMany(Application::class, 'applicant_id');
    }

    /**
     * Get the email verification records for this user
     */
    public function emailVerifications()
    {
        return $this->hasMany(EmailVerification::class);
    }

    /**
     * Check if user is an applicant
     */
    public function isApplicant(): bool
    {
        return $this->role === 'applicant';
    }

    /**
     * Check if user is a company
     */
    public function isCompany(): bool
    {
        return $this->role === 'company';
    }

    /**
     * Check if user's email is verified
     */
    public function isEmailVerified(): bool
    {
        return !is_null($this->email_verified_at);
    }
}
