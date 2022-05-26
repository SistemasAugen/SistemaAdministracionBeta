<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Promo extends Model {
    protected $fillable = [
        'name',
        'type',
        'amount',
        'starts_at',
        'ends_at',
    ];
}
