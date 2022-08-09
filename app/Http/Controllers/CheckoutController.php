<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use Takaden\Enums\PaymentProviders;
use Takaden\Payment\PaymentHandler;

class CheckoutController extends Controller
{
    public function initiatePayment(Request $request) {
        $request->validate([
            'payment_provider'  => ['required', Rule::in(PaymentProviders::values())],
            'order_id'          => 'required|exists:orders,id',
        ]);
        $order = Order::findOrFail($request->order_id);
        if ($request->payment_provider == PaymentProviders::CASH->value) {
            return $this->handleCashPayment($order);
        }
        $handler = PaymentHandler::create($request->payment_provider);
        return $handler->initiatePayment($order);
    }

    public function executePayment(Request $request) {
        return PaymentHandler::create($request->payment_provider)->executePayment($request->payment_id);
    }

    public function validatePayment(Request $request) {
        $isSuccessfull = PaymentHandler::create($request->payment_provider)->validateSuccessfulPayment($request);
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
