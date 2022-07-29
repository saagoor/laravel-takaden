<?php

namespace App\Models;

use App\Takaden\Orderable;
use App\Takaden\Payable;
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
    public function getTakadenAmount(): int
    {
        return $this->amount;
    }
    public function getTakadenCurrency(): string
    {
        return $this->currency;
    }
    public function getTakadenUniqueId(): string
    {
        return $this->id;
    }
    public function getTakadenClassName(): string
    {
        return $this::class;
    }
}
