<?php

namespace Azuriom\Plugin\Tebex\Controllers\Admin;

use Azuriom\Models\Setting;
use Illuminate\Support\Arr;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Http;
use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\Tebex\Resources\Currencies;

class SettingController extends Controller
{
    /**
     * Display the tebex settings.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        return view('tebex::admin.index', [
            'tebex_currencies' => Currencies::all(),
            'tebex_current_currency' => setting('tebex.currency', 'USD'),
            'tebex_key' => setting('tebex.key', ''),
            'tebex_shop_title' => setting('tebex.shop.title', ''),
            'tebex_shop_subtitle' => setting('tebex.shop.subtitle', ''),
        ]);
    }


    /**
     * Update the tebex settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function save(Request $request)
    {
        $this->validate($request, [
            'tebex_currency' => ['required', Rule::in(Currencies::codes())],
            'tebex_key' => ['nullable', 'max:40', 'min:40'],
        ]);

        $response = Http::withHeaders(['X-Tebex-Secret' => $request->input('tebex_key')])->get('https://plugin.tebex.io/information');
         

        if ($response->successful()) {
            Setting::updateSettings([
                'tebex.key' => $request->input('tebex_key'),
                'tebex.currency' => $request->input('tebex_currency'),
                'tebex.shop.title' => $request->input('tebex_title'),
                'tebex.shop.subtitle' => $request->input('tebex_subtitle'),
            ]);

            return redirect()->route('tebex.admin.index')
                ->with('success', trans('admin.settings.updated'));
        } else {
            return redirect()->route('tebex.admin.index')
                ->with('error', trans('tebex::admin.errors.badApiKey'));
        }
    }
}
