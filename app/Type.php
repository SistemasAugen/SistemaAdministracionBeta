<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    protected $guarded=[];
    
    public function categories()
    {
        return $this->belongsToMany('App\Category','types_has_categories');
    }
}
