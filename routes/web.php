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

Route::get('/concerts/{id}', [App\Http\Controllers\ConcertController::class, 'show'])
    ->name('concerts.show');

Route::post('/concerts/{id}/orders', [App\Http\Controllers\ConcertOrderController::class, 'store']);
Route::get('/orders/{confirmationNumber}', [App\Http\Controllers\OrderController::class, 'show']);

Route::middleware('auth')
    ->prefix('backstage')
    ->group(function() {
        Route::get('/concerts', [App\Http\Controllers\Backstage\ConcertController::class, 'index'])
            ->name('backstage.concerts.index');
        Route::get('/concerts/new', [App\Http\Controllers\Backstage\ConcertController::class, 'create'])
            ->name('backstage.concerts.new');
        Route::post('/concerts', [App\Http\Controllers\Backstage\ConcertController::class, 'store']);
        Route::get('/concerts/{id}/edit', [App\Http\Controllers\Backstage\ConcertController::class, 'edit'])
            ->name('backstage.concerts.edit');
        Route::patch('/concerts/{id}', [App\Http\Controllers\Backstage\ConcertController::class, 'update'])
            ->name('backstage.concerts.update');

        Route::post(
            '/published-concerts', 
            [App\Http\Controllers\Backstage\PublishedConcertController::class, 'store']
        )->name('backstage.published-concerts.store');

        Route::get(
            '/published-concerts/{id}/orders', 
            [App\Http\Controllers\Backstage\PublishedConcertOrdersController::class, 'index']
        )->name('backstage.published-concert-orders.index');
    });

