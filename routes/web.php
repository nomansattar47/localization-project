<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;

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

Route::get('/{locale}', function ($locale) {
    if (! in_array($locale, ['en', 'ur'])) {
        abort(400);
    }
    App::setLocale($locale);
    return view('welcome');
});

Route::get('/{locale}/dashboard', function ($locale) {
    if (! in_array($locale, ['en', 'ur'])) {
        abort(400);
    }
    App::setLocale($locale);
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';
