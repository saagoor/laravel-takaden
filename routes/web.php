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

// Overwrite Takaden Routes
Route::post('takaden/checkout/initiate/{provider?}', [CheckoutController::class, 'initiate'])->name('takaden.checkout.initiate');

Route::get('checkout', fn () => view('checkout.index', ['order' => Order::findOrFail(request('order_id'))]))->name('checkout.index');
Route::get('checkout/complete', fn () => view('checkout.complete', ['order' => Order::find(request('orderable_id'))]))->name('checkout.complete');
Route::get('checkout/failure', fn () => view('checkout.failure', ['order' => Order::find(request('orderable_id'))]))->name('checkout.failure');
Route::get('checkout/cancelled', fn () => view('checkout.cancelled', ['order' => Order::find(request('orderable_id'))]))->name('checkout.cancelled');
