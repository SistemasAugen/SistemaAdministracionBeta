<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $guarded=[];

    public function state()
    {
        return $this->hasOne('App\State',"id","state_id");
    }

    public function town()
    {
        return $this->hasOne('App\Town','id','town_id');
    }

    public function laboratory()
    {
        return $this->hasOne('App\Laboratory','id','laboratory_id');
    }

    public function clients()
    {
        return $this->hasMany('App\Client','branch_id');
    }

    public function orders()
    {
        return $this->hasMany('App\Order','branch_id');
    }
}
