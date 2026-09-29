<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'actor_id',
        'from_status',
        'to_status',
        'proposed_at',
        'comment',
    ];

    protected $casts = [
        'proposed_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function request()
    {
        return $this->belongsTo(Request::class);
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
