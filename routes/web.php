<?php

use Illuminate\Support\Facades\Route;
use Azuriom\Plugin\Tebex\Controllers\TebexHomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your plugin. These
| routes are loaded by the RouteServiceProvider of your plugin within
| a group which contains the "web" middleware group and your plugin name
| as prefix. Now create something great!
|
*/

Route::get('/', [TebexHomeController::class, 'index'])->name('index');
