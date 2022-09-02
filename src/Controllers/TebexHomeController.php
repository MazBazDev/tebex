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
        if(!setting('tebex.key')) {
            return redirect()->route('home')->with('error', trans('tebex::admin.errors.noApiKey'));
        }

        $rProducts = json_decode(Http::withHeaders(['X-Tebex-Secret' => setting('tebex.key')])->get('https://plugin.tebex.io/packages?verbose=true'));
        $rCategories = json_decode(Http::withHeaders(['X-Tebex-Secret' => setting('tebex.key')])->get('https://plugin.tebex.io/listing'));
        $rSales = json_decode(Http::withHeaders(['X-Tebex-Secret' => setting('tebex.key')])->get('https://plugin.tebex.io/sales'));
        $categories = array();
        $sales = array();  
 
        foreach($rCategories->categories as $cate) {
            $packages = [];
            $subCategories = [];

            foreach($cate->packages as $catPackages) { 
                foreach($rProducts as $Product) {
                    if($Product->id == $catPackages->id && !$Product->disabled) {
                        
                        $product = (object) [
                            'id' => $Product->id,
                            'name' => str_replace("'", "\'", $Product->name),
                            'image' => $Product->image,
                            'description' => str_replace("'", "\'", $Product->description),
                            "price" => (object) array(
                                "normal" => $Product->price,
                                "discounted" => null,
                                "expire" => null,
                            ),
                            "sales" => array()
                        ];

                        foreach ($rSales->data as $sales) {
                            switch ($sales->effective->type) {
                                case 'package': 
                                    foreach ($sales->effective->packages as $salePackage) {
                                        if ($salePackage == $Product->id) { 
                                            $price = $sales->discount->type == "percentage" ? $Product->price * ((100 - $sales->discount->percentage) / 100) : $Product->price - $sales->discount->value;

                                            $product->price->discounted = round($price, 2);
                                            $product->price->expire = date('d/m/y H:i:s', $sales->expire);

                                            array_push($product->sales, $sales->discount); 
                                        }
                                    }
                                    break;
                                case 'category':
                                    foreach ($sales->effective->categories as $saleCate) {
                                        if ($saleCate == $Product->category->id) {
                                            $price = $sales->discount->type == "percentage" ? $Product->price * ((100 - $sales->discount->percentage) / 100) : $Product->price - $sales->discount->value;

                                            $product->price->discounted = round($price, 2);
                                            $product->price->expire = date('d/m/y H:i:s', $sales->expire);

                                            array_push($product->sales, $sales->discount); 
                                        }
                                    };
                                    break;
                                case 'all':
                                    $price = $sales->discount->type == "percentage" ? $Product->price * ((100 - $sales->discount->percentage) / 100) : $Product->price - $sales->discount->value;

                                    $product->price->discounted = round($price, 2);
                                    $product->price->expire = date('d/m/y H:i:s', $sales->expire);

                                    array_push($product->sales, $sales->discount); 
                                    break;
                            }
                        };

                        array_push($packages, $product);
                    }
                }
            }

            foreach($cate->subcategories as $catSubCate) {
                $Subpackages = [];
                foreach($catSubCate->packages as $subpackages) {
                    foreach($rProducts as $Product) {
                        if($Product->id == $subpackages->id && !$Product->disabled) {
                            $product = (object) [
                                'id' => $Product->id,
                                'name' => str_replace("'", "\'", $Product->name),
                                'image' => $Product->image,
                                'description' => str_replace("'", "\'", $Product->description),
                                "price" => (object) array(
                                    "normal" => $Product->price,
                                    "discounted" => null,
                                    "expire" => null,
                                ),
                                "sales" => array()
                            ];
    
                            foreach ($rSales->data as $sales) {
                                switch ($sales->effective->type) {
                                    case 'package': 
                                        foreach ($sales->effective->packages as $salePackage) {
                                            if ($salePackage == $Product->id) { 
                                                $price = $sales->discount->type == "percentage" ? $Product->price * ((100 - $sales->discount->percentage) / 100) : $Product->price - $sales->discount->value;
    
                                                $product->price->discounted = round($price, 2);
                                                $product->price->expire = date('d/m/y H:i:s', $sales->expire);
    
                                                array_push($product->sales, $sales->discount); 
                                            }
                                        }
                                        break;
                                    case 'category':
                                        foreach ($sales->effective->categories as $saleCate) {
                                            if ($saleCate == $Product->category->id) {
                                                $price = $sales->discount->type == "percentage" ? $Product->price * ((100 - $sales->discount->percentage) / 100) : $Product->price - $sales->discount->value;
    
                                                $product->price->discounted = round($price, 2);
                                                $product->price->expire = date('d/m/y H:i:s', $sales->expire);
    
                                                array_push($product->sales, $sales->discount); 
                                            }
                                        };
                                        break;
                                    case 'all':
                                        $price = $sales->discount->type == "percentage" ? $Product->price * ((100 - $sales->discount->percentage) / 100) : $Product->price - $sales->discount->value;
    
                                        $product->price->discounted = round($price, 2);
                                        $product->price->expire = date('d/m/y H:i:s', $sales->expire);
    
                                        array_push($product->sales, $sales->discount); 
                                        break;
                                }
                            };
                            array_push($Subpackages, $product); 
                        }
                    }
                }

                array_push($subCategories, (object) [
                    'id' => $catSubCate->id,
                    'name' => $catSubCate->name,
                    'packages' => $Subpackages,
                ]);
            }

            array_push($categories, (object) [
                'id' => $cate->id,
                'name' => $cate->name,
                'packages' => $packages,
                'subcategories' => $subCategories
            ]);

        } 
        return view('tebex::index', ["categories" => $categories]);
    }
}
