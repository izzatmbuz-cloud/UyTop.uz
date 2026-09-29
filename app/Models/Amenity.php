<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Amenity extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'code',
        'name_uz',
    ];

    public function listings()
    {
        return $this->belongsToMany(Listing::class, 'listing_amenity');
    }
}
