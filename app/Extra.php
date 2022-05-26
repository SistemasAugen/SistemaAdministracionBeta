<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Extra extends Model
{
    protected $guarded=[];
    public function products()
    {
        return $this->belongsToMany('App\Product','extra_has_products');
    }
}
