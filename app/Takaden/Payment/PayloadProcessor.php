<?php

namespace App\Takaden\Payment;

use App\Takaden\Enums\PaymentProviders;
use Carbon\Carbon;

class PayloadProcessor
{
    public static function process($payload, $provider)
    {
        return match ($provider) {
            PaymentProviders::SSLCOMMERZ    => static::sslCommerz($payload),
            PaymentProviders::PADDLE        => static::paddle($payload),
            default                         => $payload,
        };
    }

    public static function paddle($payload)
    {
        $billable = json_decode($payload['passthrough'], true);
        return [
            'payment_id'                => $billable['billable_id'], // Payment ID
            'method'                    => $payload['payment_method'] ?? 'PADDLE',
            'amount'                    => $payload['sale_gross'],
            'provider'                  => PaymentProviders::PADDLE,
            'paid_at'                   => (isset($payload['event_time']) && $payload['event_time']) ? Carbon::parse($payload['event_time']) : now(),
            'providers_transaction_id'  => $payload['order_id'],
            'providers_payload'         => json_encode($payload),
        ];
    }

    public static function sslCommerz($payload)
    {
        return [
            'payment_id'                => ($payload['value_a'] ?? null), // Purchase ID
            'method'                    => $payload['card_issuer'] ?? 'SSL',
            'amount'                    => $payload['currency_amount'] ?? 0,
            'provider'                  => PaymentProviders::SSLCOMMERZ,
            'paid_at'                   => (isset($payload['tran_date']) && $payload['tran_date']) ? Carbon::parse($payload['tran_date']) : now(),
            'providers_payment_id'      => $payload['tran_id'] ?? '',
            'providers_transaction_id'  => $payload['bank_tran_id'] ?? '',
            'providers_payload'         => json_encode($payload),
        ];
    }
}
