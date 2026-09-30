<?php

namespace App\Models;

use App\Enums\RequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;

    protected $fillable = [
        'requester_id',
        'recipient_id',
        'listing_id',
        'project_id',
        'name',
        'phone',
        'proposed_at',
        'time_start',
        'time_end',
        'occupants_count',
        'message',
        'status',
        'idempotency_key',
    ];

    protected $casts = [
        'status' => RequestStatus::class,
        'proposed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function events()
    {
        return $this->hasMany(RequestEvent::class);
    }
}
