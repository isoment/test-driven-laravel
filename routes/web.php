<?php

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

Route::get('/', function() {
    return "TicketApp";
});

Route::get('/concerts/{id}', [App\Http\Controllers\ConcertController::class, 'show']);
Route::post('/concerts/{id}/orders', [App\Http\Controllers\ConcertOrderController::class, 'store']);
Route::get('/orders/{confirmationNumber}', [App\Http\Controllers\OrderController::class, 'show']);


Route::middleware('auth')->group(function() {
    Route::get('/backstage/concerts/new', [App\Http\Controllers\Backstage\ConcertController::class, 'create']);
});