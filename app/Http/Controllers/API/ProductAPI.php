<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Models\Product;
use App\Models\Category;

class ProductAPI extends Controller
{
    public function index(string $id = null){
        if($id != null){ // for product details
            $products = Product::with('addons.products')->with('complamentary.product')->where('is_visible',1)->where('id',$id)->first();
            $products->images = $products->getMedia('products-media');
        }else{ // for all products
            $products = Product::with('addons.products')->with('complamentary.product')->where('is_visible',1)->get();
        }

        $products->each(function($product) {
            $product->image_url = getProductMainImage($product->id);
        });


        return response()->json([
            'status' => 'true',
            'data' =>  $products,
        ]);
    }

    public function get_products_by_category(string $id){
        $category = Category::find($id);
        if($category){
            $products = $category->products()
                                ->with('addons.products', 'complamentary.product')
                                ->where('is_visible', 1)->get();
            
            $products->each(function($product) {
                $product->image_url = getProductMainImage($product->id);
            });
            return response()->json([
                'status' => 'true',
                'data' => $products,
            ]);
        }else{
            return response()->json([
                'status' => 'false',
                'massege' =>  'Category Not found',
            ]);
        }
    }
}