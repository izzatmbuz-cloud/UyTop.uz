<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_uz',
        'name_ru',
        'active',
    ];

    public function listings()
    {
        return $this->hasMany(Listing::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
