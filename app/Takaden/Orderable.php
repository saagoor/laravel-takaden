<?php

namespace App\Takaden;

use Illuminate\Database\Eloquent\Model;

interface Orderable
{
    public function handleSuccessPayment(Payable $payment);
    public function handleFailPayment(Payable $payment);
    public function getTakadenAmount(): int;
    public function getTakadenCurrency(): string;
    public function getTakadenUniqueId(): string;
    public function getTakadenRedirectUrl(): string;
    public function getTakadenPaymentTitle(): string;
    public function getTakadenCustomer(): Model;
}
