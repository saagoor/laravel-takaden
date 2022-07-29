<?php

use App\Models\Order;
use App\Takaden\Payment\Handlers\BkashPaymentHandler;
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
    return (new BkashPaymentHandler)->initiatePayment(new Order([
        'amount'    => $request->amount,
        'id'        => 1,
        'currency'  => 'BDT',
    ]));
});

Route::post('checkout/execute', function (Request $request) {
    return (new BkashPaymentHandler)->executePayment($request->payment_id);
});

Route::get('checkout/success', function (Request $request) {
    dd("Success", $request->all());
});
