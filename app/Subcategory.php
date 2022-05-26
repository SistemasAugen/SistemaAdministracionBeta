<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    protected $guarded=[];
    
    public function categories()
    {
        return $this->belongsToMany('App\Category','categories_has_subcategories');
    }
    public function types()
    {
        return $this->belongsToMany('App\Type','subcategories_has_types');
    }
}
