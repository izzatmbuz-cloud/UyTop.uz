<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    protected $fillable = ['request_id', 'listing_id', 'payer_user_id', 'deal_type', 'rate_percent', 'deal_amount', 'commission_amount', 'currency', 'status', 'paid_at', 'admin_note'];

    protected $casts = ['rate_percent' => 'decimal:2', 'deal_amount' => 'decimal:2', 'commission_amount' => 'decimal:2', 'paid_at' => 'datetime'];

    public function request()
    {
        return $this->belongsTo(Request::class);
    }

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function payer()
    {
        return $this->belongsTo(User::class, 'payer_user_id');
    }
}
