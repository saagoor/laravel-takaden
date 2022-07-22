<?php

namespace App\Rules;

use App\Models\Coupon as ModelsCoupon;
use App\Takaden\Helpers\Currency;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class Coupon implements Rule, DataAwareRule
{
    /**
     * All of the data under validation.
     *
     * @var array
     */
    protected $data = [];

    protected string $message;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Set the data under validation.
     *
     * @param  array  $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $errorMessage = null;
        $purchasableId = $this->data['purchasable_id'];
        $purchasableType = $this->data['purchasable_type'];
        $customerId = $this->data['customer_id'] ?? auth('api')->id() ?? auth()->id();

        $coupon = ModelsCoupon::query()
            ->active()
            ->where('code', $value)
            ->withCount('applied')
            ->first();

        if (!$coupon) {
            $errorMessage = 'The entered :attribute doen\'t exists.';
        } else if ($coupon->applicable_type && $coupon->applicable_type->value != $purchasableType) {
            $errorMessage = 'This :attribute is not applicable for ' . Str::plural(class_basename($purchasableType)) . '.';
        } else if ($coupon->applicable_id && $coupon->applicable_id != $purchasableId) {
            $errorMessage = 'This :attribute is not applicable for this ' . class_basename($purchasableType) . '.';
        } else if ($coupon->starts_at->isAfter(now())) {
            // Coupon hasn't started yet.
            $errorMessage = 'This :attribute is for future :\').';
        } else if ($coupon->expires_at->isBefore(now())) {
            // Coupon has reached it's expire date
            $errorMessage = 'This :attribute has expired.';
        } else if (!($coupon->max_uses < 0) && $coupon->applied_count >= $coupon->max_uses) {
            // Not unlimited & applied maximum times
            $errorMessage = 'This :attribute has reached it\'s quota.';
        } else if ($coupon->max_uses_per_customer === 0) {
            // Customers cannot apply this :attribute (0 value)
            $errorMessage = 'You cannot apply this :attribute.';
        } else if (
            !($coupon->max_uses_per_customer < 0) &&
            ($count = $coupon->applied()->wherePivot('customer_id', $customerId)->count()) >= $coupon->max_uses_per_customer
        ) {
            // Not unlimited per customer & and applied the times of its limit.
            $errorMessage = 'You already applied this :attribute' . ($count <= 1 ? '.' : ' ' . $count . ' times.');
        } else if ($coupon->currency && $coupon->currency != Currency::current()) {
            $errorMessage = 'This :attribute is not applicable in your region.';
        }

        if ($errorMessage) {
            $this->message = $errorMessage;
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}