<?php

namespace App;
use App\Product;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
class Category extends Model
{

  use Translatable;

  protected $guarded = [];

  public $translatedAttributes = ['name'];

  public function products()
  {
    return $this->hasMany(Product::class);
  }

}
