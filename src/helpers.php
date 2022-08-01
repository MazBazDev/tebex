<?php

use Azuriom\Plugin\Tebex\Resources\Currencies;

/*
|--------------------------------------------------------------------------
| Helper functions
|--------------------------------------------------------------------------
|
| Here is where you can register helpers for your plugin. These
| functions are loaded by Composer and are globally available on the app !
| Just make sure you verify that a function doesn't exist before registering it
| to prevent any side effect.
|
*/

if (! function_exists('tebexMode')) {
    function tebexMode()
    {
        return setting('tebex.active');
    }
}

if (! function_exists('tebex_currency')) {
    function tebex_currency()
    {
        return setting('tebex.currency', 'USD');
    }
}

if (! function_exists('currency_symbol')) {
    function tebex_currency_symbol(string $currency = null)
    {
        return Currencies::symbol($currency ?? tebex_currency());
    }
}

