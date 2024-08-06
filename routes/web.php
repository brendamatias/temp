<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '^(?!api).*$');

Route::get('/login', function () {
    return view('welcome');
})->name('login');

Route::get('/register', function () {
    return view('welcome');
})->name('register');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Components/Dashboard');
    })->name('dashboard');

    Route::get('/invoices', function () {
        return view('welcome');
    })->name('invoices.index');

    Route::get('/invoices/create', function () {
        return view('welcome');
    })->name('invoices.create');

    Route::get('/invoices/{id}/edit', function () {
        return view('welcome');
    })->name('invoices.edit');

    Route::get('/partner-companies', function () {
        return view('welcome');
    })->name('partner-companies.index');

    Route::get('/partner-companies/create', function () {
        return view('welcome');
    })->name('partner-companies.create');

    Route::get('/partner-companies/{id}/edit', function () {
        return view('welcome');
    })->name('partner-companies.edit');
});
