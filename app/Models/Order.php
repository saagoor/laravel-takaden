<?php

namespace App\Models;

use Takaden\Orderable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Takaden\Enums\PaymentProviders;

class Order extends Model implements Orderable
{
    use HasFactory;

    protected $guarded = [];

    public function handleSuccessPayment(array $payload)
    {
    }
    public function handleFailPayment(array $payload)
    {
    }
    public function handleCancelPayment(array $payload)
    {
    }
    public function getTakadenAmount(): float
    {
        return $this->amount;
    }
    public function getTakadenCurrency(): string
    {
        return $this->currency;
    }
    public function getTakadenRedirectUrl(PaymentProviders $paymentProvider): string
    {
        return route('checkout.validate', $paymentProvider);
    }
    public function getTakadenClassName(): string
    {
        return $this::class;
    }
    public function getTakadenPaymentTitle(): string
    {
        return "Order #123";
    }

    public function getTakadenCustomer(): Model
    {
        return new User([
            'name'  => 'MH Sagor',
            'email' => 'mhsagor91@gmail.com',
            'phone' => '01775755272',
        ]);
    }

    public function getTakadenNotifiables(): Collection|array
    {
        return [
            $this->getTakadenCustomer(),
        ];
    }
}
