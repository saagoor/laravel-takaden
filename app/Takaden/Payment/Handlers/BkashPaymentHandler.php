<?php

namespace App\Takaden\Payment\Handlers;

use App\Models\Order;
use App\Takaden\Orderable;
use App\Takaden\Payable;
use App\Takaden\Payment\PaymentHandler;
use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class BkashPaymentHandler extends PaymentHandler
{
    protected array $config;

    public function __construct()
    {
        $this->config = [
            'app_key'       => config('takaden.bkash.app_key'),
            'app_secret'    => config('takaden.bkash.app_secret'),
            'username'      => config('takaden.bkash.username'),
            'password'      => config('takaden.bkash.password'),
            'base_url'      => config('takaden.bkash.base_url'),
            'script_url'    => config('takaden.bkash.script_url'),
            'intent'        => config('takaden.bkash.intent'),
        ];

        if (!$this->config['app_key'] || !$this->config['app_secret']) {
            throw new Exception('Bkash credentials not found, make sure to add bkash app key & app secret on the .env file.');
        }
    }

    public function initiatePayment(Orderable $order)
    {
        $payload = [
            'app_key'       => $this->config['app_key'],
            'app_secret'    => $this->config['app_secret'],
            'intent'        => 'authorization',
            'amount'        => $order->getTakadenAmount(),
            'currency'      => $order->getTakadenCurrency(),
            'merchantInvoiceNumber' => $order->getTakadenUniqueId(),
        ];

        $response = $this->httpClient()
            ->withHeaders(['x-app-key' => $this->config['app_key']])
            ->withToken($this->getToken())
            ->post('/checkout/payment/create', $payload);
        logger($response->json());
        return $response->json();
    }

    public function executePayment($bkashPaymentId)
    {
        $response = $this->httpClient()
            ->withHeaders(['x-app-key' => $this->config['app_key']])
            ->withToken($this->getToken())
            ->post('/checkout/payment/execute/' . $bkashPaymentId);
        logger($response->json());
        return $response->json();
    }

    public function validateSuccessfulPayment(Request $request): bool
    {
        return false;
    }

    protected function getToken(): string
    {
        $token = Cache::get('takaden.bkash.token');
        if ($token && !$this->isTokenExpiringSoon($token)) {
            return $token['id_token'];
        }

        $payload = [
            'app_key'       => $this->config['app_key'],
            'app_secret'    => $this->config['app_secret'],
        ];
        $endpoint = '/checkout/token/grant';

        // Refresh token if already has a token & it's expiring but not yet expired.
        if ($token && $this->isTokenExpiringSoon($token) && !$this->isTokenExpired($token)) {
            $payload['refresh_token'] = $token['refresh_token'];
            $endpoint = '/checkout/token/refresh';
        }

        $response = $this
            ->httpClient()
            ->withHeaders([
                'username'   => $this->config['username'],
                'password'   => $this->config['password'],
            ])
            ->post($endpoint, $payload);

        if ($response->failed() || $response->json('status') === 'fail') {
            throw new Exception($response->json('msg', 'Something went wrong') . ', could not get bkash access token.');
        }

        $token = $response->json();
        $token['created_at'] = time();
        Cache::put('takaden.bkash.token', $token, $token['expires_in']);
        return $token['id_token'];
    }

    protected function httpClient(): PendingRequest
    {
        return Http::baseUrl($this->config['base_url'])
            ->contentType('application/json')
            ->acceptJson();
    }

    protected function isTokenExpiringSoon(array $token): bool
    {
        return ((time() - $token['created_at']) < ($token['expires_in'] - 60 * 10)) ? false : true;
    }

    protected function isTokenExpired(array $token): bool
    {
        return ((time() - $token['created_at']) < $token['expires_in']) ? false : true;
    }

    public static function test()
    {
        return (new static)->initiatePayment(new Order([
            'amount'    => 445,
            'id'        => 1,
            'currency'  => 'BDT',
        ]));
    }
}
