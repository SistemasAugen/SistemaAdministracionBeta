<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderHasExtra extends Model
{
    protected $guarded=[];
    public function extra()
    {
        return $this->hasOne('App\Product','id','extra_id');
    }
}
