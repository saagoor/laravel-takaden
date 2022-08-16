<?php

namespace App\Models;

use Takaden\Orderable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Takaden\Enums\PaymentStatus;
use Takaden\Models\Checkout;

class Order extends Model implements Orderable
{
    use HasFactory;

    protected $fillable = ['user_id', 'payment_method', 'currency', 'amount'];

    public function handleSuccessPayment(array $payload)
    {
        $this->update([
            'payment_status'    => PaymentStatus::SUCCESS,
            'payment_method'    => $payload['payment_method'],
        ]);
    }
    public function handleFailPayment(array $payload)
    {
        $this->update([
            'payment_status'    => PaymentStatus::FAILED,
            'payment_method'    => $payload['payment_method'],
        ]);
    }
    public function handleCancelPayment(array $payload)
    {
        $this->update([
            'payment_status'    => PaymentStatus::CANCELLED,
            'payment_method'    => $payload['payment_method'],
        ]);
    }
    public function getTakadenAmount(): float
    {
        return $this->amount;
    }
    public function getTakadenCurrency(): string
    {
        return $this->currency;
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
        return User::firstOrNew([
            'id'    => 1,
            'name'  => 'MH Sagor',
            'email' => 'mhsagor91@gmail.com',
        ]);
    }
    public function getTakadenNotifiables(): Collection|array
    {
        return [
            $this->getTakadenCustomer(),
        ];
    }
    public function checkout()
    {
        return $this->morphOne(Checkout::class, 'orderable')->ofMany('updated_at');
    }
}
