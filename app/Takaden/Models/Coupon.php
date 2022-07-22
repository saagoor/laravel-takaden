<?php

namespace App\Models;

use App\Takaden\Enums\PaymentStatus;
use App\Takaden\Enums\Purchasable;
use App\Takaden\Models\Purchase;
use App\Traits\BlameableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Coupon extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'                => 'integer',
        'code'              => 'string',
        'amount'            => 'integer',
        'limit'             => 'integer',
        'max_uses'          => 'integer',
        'max_uses_per_customer' => 'integer',
        'created_by'        => 'integer',
        'updated_by'        => 'integer',
        'starts_at'         => 'datetime',
        'expires_at'        => 'datetime',
        'is_active'         => 'boolean',
        'applicable_id'     => 'integer',
        'applicable_type'   => Purchasable::class,
    ];

    public function applicable()
    {
        return $this->morphTo('applicable');
    }

    public function applied()
    {
        return $this
            ->belongsToMany(Purchase::class, 'applied_coupons', 'coupon_id', 'purchase_id')
            ->withTimestamps()
            ->whereRelation('payment', 'status', '!=', PaymentStatus::INITIATED);
    }

    public function appliedCustomers()
    {
        return $this
            ->belongsToMany(Customer::class, 'applied_coupons', 'coupon_id', 'customer_id')
            ->withTimestamps();
    }

    public function scopeLocalSearch($query, $term)
    {
        return $query->where(
            fn ($query) => $query
                ->where('code', 'like', '%' . $term . '%')
                ->orWhere('amount', 'like', '%' . $term . '%')
                ->orWhere('amount_type', 'like', '%' . $term . '%')
                ->orWhere('applicable_type', 'like', '%' . $term . '%')
        );
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function calculateDiscountAmount($price)
    {
        $discount = 0;
        if ($this->amount_type == 'percentage') {
            $amount = ($this->amount / 100) * $price;
            $discount = $this->limit && $this->limit < $amount ? $this->limit : $amount;
        } else {
            $discount = $this->amount < $price ? $this->amount : $price;
        }
        return round($discount);
    }
}