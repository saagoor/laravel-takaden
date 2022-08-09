<?php

namespace App\Models;

use Takaden\Orderable;
use Takaden\Payable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model implements Orderable
{
    use HasFactory;

    protected $guarded = [];

    public function handleSuccessPayment(Payable $payment)
    {
    }
    public function handleFailPayment(Payable $payment)
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
    public function getTakadenUniqueId(): string
    {
        return 'TRIPTOPIA' . md5(time());
    }
    public function getTakadenRedirectUrl(): string
    {
        return route('checkout.validate');
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
}
