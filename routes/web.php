<?php

use App\Models\Order;
use App\Takaden\Payment\Handlers\BkashPaymentHandler;
use App\Takaden\Payment\Handlers\SSLCommerzPaymentHandler;
use App\Takaden\Payment\Handlers\UpayPaymentHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return BkashPaymentHandler::test();
});

Route::view('checkout', 'checkout.index');

Route::post('checkout/initiate', function (Request $request) {
    $handler = match ($request->payment_method) {
        'bkash'         => new BkashPaymentHandler,
        'upay'          => new UpayPaymentHandler,
        'sslcommerz'    => new SSLCommerzPaymentHandler,
    };
    return $handler->initiatePayment(new Order([
        'id'        => 1,
        'amount'    => 20,
        'currency'  => 'BDT',
    ]));
})->name('checkout.initiate');

Route::post('checkout/execute', function (Request $request) {
    return (new BkashPaymentHandler)->executePayment($request->payment_id);
})->name('checkout.execute');

Route::get('checkout/validate', function (Request $request) {
    dd("validate", $request->all());
})->name('checkout.validate');

Route::get('checkout/success', function (Request $request) {
    dd("Success", $request->all());
})->name('checkout.success');
