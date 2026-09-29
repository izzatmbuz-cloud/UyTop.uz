<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_id',
        'project_id',
        'storage_path',
        'mime',
        'sort_order',
    ];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
