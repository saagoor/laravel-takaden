<?php

namespace App\Takaden;

use App\Takaden\Models\Payment;
use App\Takaden\Models\Purchase;
use App\Models\Package;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

trait Billable
{

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function packages(): BelongsToMany
    {
        return $this
            ->belongsToMany(Package::class, 'purchases', 'customer_id', 'purchasable_id', 'id', 'id')
            ->withPivot(['purchasable_type', 'expires_at', 'is_active', 'is_trial', 'trial_ends_at'])
            ->wherePivot('purchasable_type', Package::class)
            ->where(function (Builder $query) {
                $query->where('expires_at', '>', now())
                    ->orWhere(function (Builder $query) {
                        $query->whereNull('expires_at')->where('purchasable_type', '!=', Package::class);
                    });
            })
            ->wherePivot('is_active', true)
            ->using(Purchase::class)
            ->as('purchase')
            ->withTimestamps()
            ->orderByPivot('updated_at', 'desc');
    }

    public function subscription(): Attribute
    {
        return Attribute::make(get: function () {
            $key = 'customers.' . $this->id . '.subscription';
            if (Cache::has($key)) {
                // return Cache::get($key);
            }
            $subscription = $this->packages()->first();
            Cache::put($key, $subscription ?? [], $subscription?->purchase?->expires_at ?? now()->addMinutes(60));
            return $subscription;
        });
    }

    public function hasLicenceFor(Model $purchasable): bool
    {
        return $this->purchases()
            ->where('is_active', true)
            ->where('purchasable_type', $purchasable::class)
            ->where('purchasable_id', $purchasable->id)
            ->where(
                fn($query) => $query
                    ->whereNull('expires_at')
                    ->orWhere('expires_at', '=', 0)
                    ->orWhere('expires_at', '>', now())
            )
            ->exists();
    }
}
