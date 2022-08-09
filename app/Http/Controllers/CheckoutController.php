<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Takaden\Enums\PaymentProviders;
use Takaden\Payment\PaymentHandler;

class CheckoutController extends Controller
{
    public function initiatePayment(Request $request) {
        $request->validate([
            'payment_provider'  => ['required', Rule::in(PaymentProviders::values())],
            'order_id'          => 'required|exists:orders',
        ]);
        $handler = PaymentHandler::create('paypal');
        $order = Order::findOrFail($request->order_id);
        return $handler->initiatePayment($order);
    }

    public function executePayment(Request $request) {
        return PaymentHandler::create($request->payment_provider)->executePayment($request->payment_id);
    }

    public function validatePayment(Request $request) {
        dd("validate", $request->all());
    }
}
