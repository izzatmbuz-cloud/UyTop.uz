<?php

namespace App\Models;

use App\Enums\ModerationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'manager_user_id',
        'district_id',
        'name',
        'developer_name',
        'description',
        'stage',
        'completion_text',
        'moderation_status',
        'is_demo',
    ];

    protected $casts = [
        'moderation_status' => ModerationStatus::class,
    ];

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_user_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function listings()
    {
        return $this->hasMany(Listing::class);
    }

    public function media()
    {
        return $this->hasMany(Media::class);
    }

    public function requests()
    {
        return $this->hasMany(Request::class);
    }
}
