<?php

namespace App\Models;

use App\Enums\AvailabilityStatus;
use App\Enums\DealType;
use App\Enums\ModerationStatus;
use App\Enums\PropertyType;
use App\Enums\RentalUnit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_user_id',
        'project_id',
        'district_id',
        'deal_type',
        'rental_unit',
        'property_type',
        'dormitory_type',
        'students_allowed',
        'title',
        'description',
        'currency',
        'price',
        'price_basis',
        'utilities_mode',
        'utilities_amount',
        'utilities_payment_timing',
        'deposit_mode',
        'deposit_amount',
        'commission_mode',
        'commission_amount',
        'capacity',
        'free_places',
        'available_from',
        'min_months',
        'area_m2',
        'rooms',
        'floor',
        'location_text',
        'lat',
        'lng',
        'author_type',
        'source_type',
        'source_url',
        'moderation_status',
        'availability_status',
        'confirmed_at',
        'published_at',
        'archived_at',
        'is_demo',
    ];

    protected $casts = [
        'deal_type' => DealType::class,
        'property_type' => PropertyType::class,
        'rental_unit' => RentalUnit::class,
        'moderation_status' => ModerationStatus::class,
        'availability_status' => AvailabilityStatus::class,
        'price' => 'decimal:2',
        'utilities_amount' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'available_from' => 'date',
        'is_demo' => 'boolean',
        'confirmed_at' => 'datetime',
        'published_at' => 'datetime',
        'archived_at' => 'datetime',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function media()
    {
        return $this->hasMany(Media::class);
    }

    public function amenities()
    {
        return $this->belongsToMany(Amenity::class, 'listing_amenity');
    }

    public function requests()
    {
        return $this->hasMany(Request::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function isPubliclyVisible(): bool
    {
        return $this->moderation_status === ModerationStatus::APPROVED
            && $this->availability_status === AvailabilityStatus::AVAILABLE
            && $this->archived_at === null
            && ($this->confirmed_at === null || $this->confirmed_at->gte(now()->subDays(14)));
    }
}
