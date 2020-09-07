<?php

namespace App;
use App\Category;
use Illuminate\Database\Eloquent\Model;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Product extends Model
{

    use Translatable;

    protected $appends = ['image_path'];

    protected $guarded = [];

    public $translatedAttributes = ['name', 'description'];

    public function category()
    {
      return $this->belongsTo(Category::class);
    }


    public function getImagePathAttribute()
        {
            return asset('uploads/products/' . $this->image);
        }


}
