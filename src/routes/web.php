<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PortfolioController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/submit', [HomeController::class, 'submit'])->name('submit');
Route::get('/portfolio', [PortfolioController::class, 'index'])->name('portfolio');
