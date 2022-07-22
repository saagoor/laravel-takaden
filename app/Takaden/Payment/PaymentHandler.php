<?php

namespace App\Takaden\Payment;

use App\Takaden\Enums\PaymentProviders;
use App\Takaden\Enums\PaymentStatus;
use App\Takaden\Models\Payment;
use App\Takaden\Models\Purchase;
use App\Takaden\Notifications\PaymentNotification;
use Illuminate\Http\Request;

abstract class PaymentHandler
{
    public PaymentProviders $name;

    abstract public function initiatePayment(Purchase $purchase);

    abstract public function validateSuccessfulPayment(Request $request): bool;

    /**
     * Before creating/initiating payemnt
     */
    public function beforePaymentCreate(Request $request): void
    {
    }

    /**
     * After payment successful action
     * 1. Update payment status to 'success'.
     * 2. Mark the purchase as active.
     * 3. Clear cache of customer's subscription, payment & purchase history.
     */
    public function afterPaymentSuccessful(Request $request): Payment
    {
        $payment = $this->updateStatusAndGetPayment($request, PaymentStatus::SUCCESS);
        if ($payment->purchase && !$payment->purchase->is_active) {
            $payment->purchase->is_active = true;
            $payment->purchase->save();
        }
        return $payment;
    }

    /**
     * After payment failed action
     * 1. Update the payment status to 'failed'.
     * 2. Mark the purchase as inactive.
     * 3. Clear cache of customer's subscription, payment & purchase history.
     */
    public function afterPaymentFailed(Request $request): Payment
    {
        $payment = $this->updateStatusAndGetPayment($request, PaymentStatus::FAILED);
        $payment->purchase->is_active = false;
        $payment->purchase->save();
        return $payment;
    }

    /**
     * After payment cancelled action
     * 1. Update the payment status to 'cancelled'.
     */
    public function afterPaymentCancelled(Request $request): Payment
    {
        return $this->updateStatusAndGetPayment($request, PaymentStatus::CANCELLED);
    }

    /**
     * Process the payload came from payment gateway,
     *  and create or update the payment record according to the payment status
     */
    protected function updateStatusAndGetPayment(Request $request, PaymentStatus $status): Payment
    {
        $paymentPayload = PayloadProcessor::process($request->all(), $this->name);
        $paymentPayload['status'] = $status;
        $payment = Payment::findOrNew($paymentPayload['payment_id']);
        $payment->update($paymentPayload);
        // Notify the customer
        $payment->customer->notify(new PaymentNotification($payment, $request->all()));
        return $payment;
    }
}
