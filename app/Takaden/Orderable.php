<?php

namespace App\Takaden;

interface Orderable
{
    public function handleSuccessPayment(Payable $payment);
    public function handleFailPayment(Payable $payment);
    public function getTakadenAmount(): int;
    public function getTakadenCurrency(): string;
    public function getTakadenUniqueId(): string;
}
