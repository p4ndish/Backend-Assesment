<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'applicant_id',
        'job_id',
        'resume_link',
        'cover_letter',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'string',
        'applied_at' => 'datetime',
    ];

    /**
     * Get the applicant who submitted this application
     */
    public function applicant()
    {
        return $this->belongsTo(User::class, 'applicant_id');
    }

    /**
     * Get the job this application is for
     */
    public function job()
    {
        return $this->belongsTo(JobListing::class, 'job_id');
    }

    /**
     * Scope to get applications by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get applied applications
     */
    public function scopeApplied($query)
    {
        return $query->where('status', 'applied');
    }

    /**
     * Scope to get reviewed applications
     */
    public function scopeReviewed($query)
    {
        return $query->where('status', 'reviewed');
    }

    /**
     * Scope to get interview applications
     */
    public function scopeInterview($query)
    {
        return $query->where('status', 'interview');
    }

    /**
     * Scope to get rejected applications
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope to get hired applications
     */
    public function scopeHired($query)
    {
        return $query->where('status', 'hired');
    }
}
