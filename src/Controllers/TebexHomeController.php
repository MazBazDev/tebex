<?php

namespace Azuriom\Plugin\Tebex\Controllers;

use Illuminate\Support\Facades\Http;
use Azuriom\Http\Controllers\Controller;

class TebexHomeController extends Controller
{
    /**
     * Show the home plugin page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rProducts = json_decode(Http::withHeaders(['X-Tebex-Secret' => setting('tebex.key')])->get('https://plugin.tebex.io/packages'));
        $rCategories = json_decode(Http::withHeaders(['X-Tebex-Secret' => setting('tebex.key')])->get('https://plugin.tebex.io/listing'));
        $rSales = json_decode(Http::withHeaders(['X-Tebex-Secret' => setting('tebex.key')])->get('https://plugin.tebex.io/sales'));

        $categories = array();
        $sales = array();

        foreach ($rCategories as $cat) {
            foreach ($cat as $ca) {
                $data = (object) array(
                    "id" =>  $ca->id,
                    "name" => $ca->name,
                    "packages" => array(),
                );

                foreach ($rProducts as $pro) {
                    if ($pro->category->id == $ca->id && !$pro->disabled) {
                        $product = (object) array(
                            "id" => $pro->id,
                            "name" => $pro->name,
                            "image" => $pro->image,
                            "price" => (object) array(
                                "normal" => $pro->price,
                                "discounted" => null,
                                "expire" => null,
                            ),
                            "sales" => array()
                        );

                        foreach ($rSales->data as $sales) {
                            switch ($sales->effective->type) {
                                case 'package':
                                    foreach ($sales->effective->packages as $salePackage) {
                                        if ($salePackage == $pro->id) {
                                            $price = $sales->discount->type == "percentage" ? $pro->price * ((100 - $sales->discount->percentage) / 100) : $pro->price - $sales->discount->value;

                                            $product->price->discounted = round($price, 2);
                                            $product->price->expire = date('d/m/y H:i:s', $sales->expire);

                                            array_push($product->sales, $sales->discount); 
                                        }
                                    }
                                    break;
                                case 'category':
                                    foreach ($sales->effective->categories as $saleCate) {
                                        if ($saleCate == $pro->category->id) {
                                            $price = $sales->discount->type == "percentage" ? $pro->price * ((100 - $sales->discount->percentage) / 100) : $pro->price - $sales->discount->value;

                                            $product->price->discounted = round($price, 2);
                                            $product->price->expire = date('d/m/y H:i:s', $sales->expire);

                                            array_push($product->sales, $sales->discount); 
                                        }
                                    };

                                    break;
                                case 'all':
                                    $price = $sales->discount->type == "percentage" ? $pro->price * ((100 - $sales->discount->percentage) / 100) : $pro->price - $sales->discount->value;

                                    $product->price->discounted = round($price, 2);
                                    $product->price->expire = date('d/m/y H:i:s', $sales->expire);

                                    array_push($product->sales, $sales->discount); 

                                    break;
                            }
                        };
                        array_push($data->packages, $product);
                    }
                }
                array_push($categories, $data);
            }
        }

        // return $categories;

        return view('tebex::index', ["categories" => $categories]);
    }
}
