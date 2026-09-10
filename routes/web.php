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
Route::get('/dashboard', function () {
    App::setLocale('en');
    return view('dashboard');
})->middleware('auth')->name('dashboard');

Route::get('/birds', function () {
    App::setLocale('en');
    return view('birds');
})->middleware('auth')->name('birds');

Route::get('/cages', function () {
    App::setLocale('en');
    return view('workspace', ['workspaceType' => 'cages']);
})->middleware('auth')->name('cages');

Route::get('/breeding', function () {
    App::setLocale('en');
    return view('workspace', ['workspaceType' => 'breeding']);
})->middleware('auth')->name('breeding');

Route::get('/records', function () {
    App::setLocale('en');
    return view('workspace', ['workspaceType' => 'records']);
})->middleware('auth')->name('records');

Route::get('/program', function () {
    App::setLocale('en');
    return view('program');
})->middleware('auth')->name('program');

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
    Route::get('/dashboard', function ($locale) {
        App::setLocale($locale);
        return view('dashboard');
    })->middleware('auth')->name('localized.dashboard');
    Route::get('/birds', function ($locale) {
        App::setLocale($locale);
        return view('birds');
    })->middleware('auth')->name('localized.birds');
    Route::get('/cages', function ($locale) {
        App::setLocale($locale);
        return view('workspace', ['workspaceType' => 'cages']);
    })->middleware('auth')->name('localized.cages');
    Route::get('/breeding', function ($locale) {
        App::setLocale($locale);
        return view('workspace', ['workspaceType' => 'breeding']);
    })->middleware('auth')->name('localized.breeding');
    Route::get('/records', function ($locale) {
        App::setLocale($locale);
        return view('workspace', ['workspaceType' => 'records']);
    })->middleware('auth')->name('localized.records');
    Route::get('/program', function ($locale) {
        App::setLocale($locale);
        return view('program');
    })->middleware('auth')->name('localized.program');
});
