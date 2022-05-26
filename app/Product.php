<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded=[];

    public function type()
    {
        return $this->hasOne('App\Type','id','type_id');
    }

    public function subcategories()
    {
        return $this->belongsToMany('App\Subcategory','product_has_subcategories');
    }

    public function extras()
    {
        return $this->belongsToMany('App\Extra','extra_has_products');
    }

    public function prices(){
        return $this->hasMany('App\ProductHasSubcategory','product_id');
    }
}
