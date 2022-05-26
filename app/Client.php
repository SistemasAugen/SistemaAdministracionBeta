<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
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

    public function discounts()
    {
        return $this->hasMany('App\Discount','client_id');
    }
    public function facturacion()
    {
        return $this->hasOne('App\Facturacion');
    }

    public function branch()
    {
        return $this->hasOne('App\Branch','id','branch_id');
    }
    
    public function orders() {
        return $this->hasMany('App\Order');
    }
}
