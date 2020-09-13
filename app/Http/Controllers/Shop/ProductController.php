<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Product;
use App\Category;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    public function index(Request $request)
    {

      $products = Product::paginate(6);
      return view('shop.index', compact('products'));

    }


    public function show(Product $product)
    {
        return view('shop.product_details', compact('product'));
    } //end of show
}
