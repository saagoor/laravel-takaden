<?php

namespace App\Takaden;

interface Orderable
{
    public function handleSuccessPayment(Payable $payment);
    public function handleFailPayment(Payable $payment);
}
