<?php

namespace App\Takaden\Payment\Handlers;

use App\Takaden\Enums\PaymentProviders;
use App\Takaden\Models\Purchase;
use App\Takaden\Payment\PaymentHandler;
use DGvai\SSLCommerz\SSLCommerz;
use Exception;
use Illuminate\Http\Request;

class SSLCommerzPaymentHandler extends PaymentHandler
{
    public PaymentProviders $name = PaymentProviders::SSLCOMMERZ;

    public function initiatePayment(Purchase $purchase)
    {
        $customer = $purchase->customer;
        $email = $customer->email ?? config('mail.from.address', 'hello@example.com');
        $name = ($customer->name ?? config('app.name') . ' Customer');
        $phone = $customer->phone;

        $sslc = (new SSLCommerz)
            ->amount($purchase->payment->payable_total)
            ->setCurrency($purchase->payment->currency)
            ->trxid($purchase->id)
            ->product($purchase->getPaymentTitle())
            ->customer($name, $email, $phone)
            ->setExtras($purchase->payment->id); // `value_a` is Payment ID

        return $sslc->make_payment(true);
    }

    public function validateSuccessfulPayment(Request $request): bool
    {
        try {
            return SSLCommerz::validate_payment($request) ?? false;
        } catch (Exception $e) {
            report($e);
        }
        return false;
    }
}
