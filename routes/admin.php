<?php

use Illuminate\Support\Facades\Route;
use Azuriom\Plugin\Tebex\Controllers\Admin\SettingController;

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

Route::middleware('can:tebex.admin')->group(function () {
    Route::get('/', [SettingController::class, 'show'])->name('index');
    Route::post('/', [SettingController::class, 'save'])->name('settings.save');
});
