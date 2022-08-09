<?php

use App\Http\Controllers\CheckoutController;
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

Route::view('/', 'welcome');

Route::view('checkout', 'checkout.index');
Route::view('checkout/success')->name('checkout.success');
Route::view('checkout/failure')->name('checkout.failure');

Route::post('checkout/initiate', [CheckoutController::class, 'initiatePayment'])->name('checkout.initiate');
Route::post('checkout/execute', [CheckoutController::class, 'executePayment'])->name('checkout.execute');
Route::get('checkout/validate', [CheckoutController::class, 'validatePayment'])->name('checkout.validate');
