<?php

namespace App\Http\Controllers\Dashboard;

use App\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{

  public function __construct()
  {

      $this->middleware(['permission:categories_read'])->only('index');
      $this->middleware(['permission:categories_create'])->only('create');
      $this->middleware(['permission:categories_update'])->only('edit');
      $this->middleware(['permission:categories_delete'])->only('destroy');

  }


  public function index(Request $request)
  {
      $categories = Category::when($request->search, function ($q) use ($request) {
          return $q->whereTranslationLike('name', '%' . $request->search . '%');
      })->latest()->paginate(10);

      return view('dashboard.categories.index', compact('categories'));

  }//end index


    public function create()
    {
        return view('dashboard.categories.create');

    } // end of create


    public function store(Request $request)
    {
        $rules = [];

        foreach (config('translatable.locales') as $locale) {

          $rules += [$locale . '.name' => ['required', Rule::unique('category_translations', 'name')]];

        }

        $request->validate($rules);

        Category::create($request->all());

        session()->flash('success', __('site.add_successfully'));

        return redirect(route('dashboard.categories.index'));

    } //end of store


    public function show(Category $category)
    {
        //
    }

    public function edit(Category $category)
    {
        return view('dashboard.categories.edit', compact('category'));
    }


    public function update(Request $request, Category $category)
    {

      $rules = [];

      foreach (config('translatable.locales') as $locale) {

        $rules += [$locale . '.name' => ['required', Rule::unique('category_translations', 'name')->ignore($category,'category_id')]];

      }

      $request->validate($rules);

      $category->update($request->all());

      session()->flash('success', __('site.updated_successfully'));

      return redirect(route('dashboard.categories.index'));
    }


    public function destroy(Category $category)
    {
        $category->delete();

        session()->flash('success', __('site.deleted_successfully'));

        return redirect(route('dashboard.categories.index'));
    }
}
