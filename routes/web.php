<?php

use App\Http\Controllers\CheckoutController;
use App\Models\Order;
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
    return view('welcome', ['order' => Order::factory()->create()]);
})->name('welcome');

Route::get('checkout', fn () => view('checkout.index', ['order' => Order::find(request('order_id'))]))->name('checkout.index');
Route::get('checkout/success', fn () => view('checkout.success', ['order' => Order::find(request('order_id'))]))->name('checkout.success');
Route::get('checkout/failure', fn () => view('checkout.failure', ['order' => Order::find(request('order_id'))]))->name('checkout.failure');
Route::get('checkout/complete', fn () => view('checkout.complete', ['order' => Order::find(request('order_id'))]))->name('checkout.complete');

Route::post('checkout/initiate', [CheckoutController::class, 'initiatePayment'])->name('checkout.initiate');
Route::post('checkout/execute', [CheckoutController::class, 'executePayment'])->name('checkout.execute');
Route::get('checkout/validate', [CheckoutController::class, 'validatePayment'])->name('checkout.validate');
