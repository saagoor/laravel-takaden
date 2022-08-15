<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Takaden\Enums\PaymentProviders;
use Takaden\Controllers\CheckoutController as TakadenCheckoutController;

class CheckoutController extends TakadenCheckoutController
{
    public function initiate(Request $request, string $paymentProvider)
    {
        if ($paymentProvider == PaymentProviders::CASH->value) {
            return $this->handleCashPayment($request->orderable_type::findOrFail($request->orderable_id));
        }
        return parent::initiate($request, $paymentProvider);
    }
    protected function handleCashPayment(Order $order)
    {
        $order->payment_method = PaymentProviders::CASH;
        $order->save();
        return response()->json($order);
    }
}
