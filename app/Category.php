<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model {
    // protected $table = 'categories';
    protected $guarded=[];

    public function subcategories()
    {
        return $this->belongsToMany('App\Subcategory','categories_has_subcategories');
    }

    public function types()
    {
        return $this->belongsToMany('App\Type','types_has_categories');
    }
}
