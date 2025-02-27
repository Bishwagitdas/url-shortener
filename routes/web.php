<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UrlShortenerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// URL Shortener Routes
Route::get('/', [UrlShortenerController::class, 'index'])->name('url.index');

Route::prefix('url')->group(function () {
    Route::post('/shorten', [UrlShortenerController::class, 'store'])->name('url.shorten');
    Route::get('/analytics/{code}', [UrlShortenerController::class, 'analytics'])->name('url.analytics');
    Route::delete('/{id}', [UrlShortenerController::class, 'destroy'])->name('url.delete');
});

// This needs to be separate to allow short URL redirection without prefix
Route::get('/{code}', [UrlShortenerController::class, 'redirect'])->name('url.redirect');
