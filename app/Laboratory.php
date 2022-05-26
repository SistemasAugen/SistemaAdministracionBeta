<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Laboratory extends Model
{
    protected $guarded=[];

    public function state()
    {
        return $this->hasOne('App\State','id','state_id');
    }

    public function town()
    {
        return $this->hasOne('App\Town','id','town_id');
    }
}
