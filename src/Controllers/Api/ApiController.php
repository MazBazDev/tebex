<?php

namespace Azuriom\Plugin\Tebex\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Azuriom\Http\Controllers\Controller;

class ApiController extends Controller
{
    /**
     * Show the plugin API default page.
     *
     * @return \Illuminate\Http\Response
     */
    public function buy(Request $request)
    {
        if (!preg_match("/^[a-zA-Z0-9 \s_.]+$/", $request->username) || strlen($request->username) < 3 || !is_numeric($request->package_id)) {
            return response()->json(['message' => 'Wrong settings!'], 404);
        }

        $response = Http::withHeaders(['X-Tebex-Secret' => setting('tebex.key')])->post('https://plugin.tebex.io/checkout', [
            'package_id' => $request->package_id,
            'username' => $request->username
        ]);

        $data = json_decode($response);

        if ($response->successful()) {
            return response()->json(['checkout_url' => $data->url], 200);
        } else {
            return response()->json(['message' => 'Internal Server Error !'], 500);
        };
    }
}
