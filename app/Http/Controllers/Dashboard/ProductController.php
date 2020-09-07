<?php

namespace App\Http\Controllers\Dashboard;

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
        $products = Product::paginate(2);
        return view('Dashboard.products.index', compact('products'));
    }


    public function create()
    {
        $categories = Category::all();
        return view('dashboard.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
      $rules = [
          'category_id' => 'required|exists:categories,id',
          'purchase_price' => 'required|min:1',
          'sale_price' => 'required|min:1',
          'stock' => 'required|min:1',
      ];

        foreach (config('translatable.locales') as $locale) {

            $rules += [
                $locale . '.name' => 'required|unique:product_translations,name',
                $locale . '.description' => 'required',
            ];

        }//end of for each

        $request->validate($rules);

        $request_data = $request->except(['image']);

        if ($request->image) {

          Image::make($request->image)->resize(300, null, function($constraint){

            $constraint->aspectRatio();

          })
          ->save(public_path('uploads/products/' . $request->image->hashName()));

          $request_data['image'] = $request->image->hashName();

        } //end of iamge

        Product::create($request_data);
        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.products.index');

    }


    public function show(Product $product)
    {
        //
    }


    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('dashboard.products.edit', compact('categories', 'product'));
    }

    public function update(Request $request, Product $product)
    {
        $rules = [
            'category_id' => 'required|exists:categories,id',
            'purchase_price' => 'required|min:1',
            'sale_price' => 'required|min:1',
        ];

        foreach (config('translatable.locales') as $locale) {

            $rules += [
                $locale . '.name' => ['required', Rule::unique('product_translations', 'name')->ignore($product->id, 'product_id')],
                $locale . '.description' => 'required',
            ];

        }//end of for each

        $request->validate($rules);

        $request_data = $request->except(['image']);

        if ($request->image) {

            if ($product->image != 'default.jpg') {

              Storage::disk('public_uploads')->delete('/products/' . $product->image);

            }

            Image::make($request->image)->resize(300, null, function($constraint){

              $constraint->aspectRatio();

            })
            ->save(public_path('uploads/products/' . $request->image->hashName()));

            $request_data['image'] = $request->image->hashName();


        } //end of iamge

        $product->update($request_data);
        session()->flash('success', __('site.updated_successfully'));
        return redirect(route('dashboard.products.index'));
    }


    public function destroy(Product $product)
    {

        if ($product->image != 'default.jpg') {

          Storage::disk('public_uploads')->delete('/products/' . $product->image);
        }

        $product->delete();

        session()->flash('success', ('site.deleted_successfully'));

        return redirect(route('dashboard.products.index'));
    }
}
