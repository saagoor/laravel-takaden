<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Takaden\Enums\PaymentProviders;
use Takaden\Payment\PaymentHandler;

class CheckoutController extends Controller
{
    public function initiatePayment(Request $request, string $paymentProvider)
    {
        $request->validate(['order_id' => 'required|exists:orders,id']);
        $order = Order::find($request->order_id);
        if ($paymentProvider == PaymentProviders::CASH->value) {
            return $this->handleCashPayment($order);
        }
        return PaymentHandler::create($paymentProvider)->initiatePayment($order);
    }

    public function executePayment(Request $request, string $paymentProvider)
    {
        return PaymentHandler::create($paymentProvider)->executePayment($request->payment_id);
    }

    public function validatePayment(Request $request, string $paymentProvider)
    {
        $isSuccessfull = PaymentHandler::create($paymentProvider)->validateSuccessfulPayment($request);
        if ($isSuccessfull) {
            return to_route('checkout.success');
        }
        return to_route('checkout.failure');
    }

    protected function handleCashPayment(Order $order)
    {
        $order->payment_method = PaymentProviders::CASH;
        $order->save();
        return response()->json($order);
    }
}
