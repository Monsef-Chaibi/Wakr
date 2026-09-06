<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;

// Default route (English)
Route::get('/', function () {
    App::setLocale('en');
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Language-prefixed routes
Route::group(['prefix' => '{locale}', 'where' => ['locale' => 'en|ar']], function () {
    Route::get('/', function ($locale) {
        App::setLocale($locale);
        return view('welcome');
    });
    Route::get('/login', function ($locale) {
        App::setLocale($locale);
        return app(AuthController::class)->showLogin();
    })->name('localized.login');
    Route::post('/login', function ($locale, Request $request) {
        App::setLocale($locale);
        return app(AuthController::class)->login($request);
    })->middleware('throttle:6,1');
    Route::get('/register', function ($locale) {
        App::setLocale($locale);
        return app(AuthController::class)->showRegister();
    })->name('localized.register');
    Route::post('/register', function ($locale, Request $request) {
        App::setLocale($locale);
        return app(AuthController::class)->register($request);
    });
});
