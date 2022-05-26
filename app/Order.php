<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Product;
use App\ProductHasSubcategory;
use App\Extras;
use App\OrderHasExtra;
use App\Subcategory;
use App\Type;
use App\Category;

class Order extends Model
{
    protected $guarded=[];
    protected $appends = ['passed', 'cont_dias'];
    public function productHas()
    {
        $pivot = ProductHasSubcategory::find($this->product_has_subcategory_id);
        // dd($pivot);
        if(is_null($pivot)){
            $this->product=["name"=>"No disponible"];
        }
        else{
            $this->product = Product::find($pivot->product_id);

            if(is_null($this->product)){
                $this->product = ["name"=> "No disponible"];
            }else{
                $this->product = $this->product->toArray();
            }
            // $this->product = array_merge($this->product, ["hola"=>"aas"]);
            // dd($this->product);

            $category = Category::find($pivot->category_id);
            if(!is_null($category))
                $this->product = array_merge($this->product, ["subcategory_name"=> $category->name]);
            else
                $this->product = array_merge($this->product, ["subcategory_name"=>"No disponible"]);

            $subcategory = Subcategory::find($pivot->subcategory_id);
            if(!is_null($subcategory))
                $this->product =  array_merge($this->product, ["type_name"=> $subcategory->name]);
            else
                $this->product = array_merge($this->product, ["type_name"=>"No disponible"]);
        }
        return $this->hasOne('App\ProductHasSubcategory','id','purchase_id');
    }

    public function laboratory() {
        return $this->belongsTo('App\Laboratory');
    }

    public function extrasHas()
    {

        return $this->hasMany('App\OrderHasExtra','order_id');
    }

    public function extras()
    {
        return $this->belongsToMany("App\Extra","order_has_extras");
    }

    public function client()
    {
        return $this->hasOne('App\Client','id','client_id');
    }

    public function branch(){
        return $this->hasOne('App\Branch','id','branch_id');
    }

    public function getPassedAttribute() {
        $deadlineToPay      = @$this->client->payment_plan ?: 0;
        $weeksInThisYear    = date('W', strtotime('28th December '. date('Y')));
        $actualWeek         = intval(date('W'));
        if(isset($this->delivered_date))
            $mustBePayed = strtotime(sprintf('+%s week', $deadlineToPay), strtotime($this->delivered_date));
        else
            $mustBePayed = strtotime(sprintf('+%s week', $deadlineToPay), strtotime($this->created_at));

        $string_date = date('Y-m-d', $mustBePayed);
        $day_of_week = date('N', strtotime($string_date));
        $week_last_day = date('Y-m-d', strtotime($string_date . " + " . (7 - $day_of_week) . " days"));

        $mustBePayed = strtotime($week_last_day);
        $weekMustBePayed    = date('W', $mustBePayed);

        if(date('Y') < date('Y', $mustBePayed)) {
            if(intval($weekMustBePayed) < $actualWeek)
                $weekMustBePayed    = intval($weeksInThisYear) + intval($weekMustBePayed);
        }
        else if(date('Y') > date('Y', $mustBePayed))
            $actualWeek         = intval($weeksInThisYear) + intval($actualWeek);

        return $weekMustBePayed - $actualWeek;
    }

    public function getContDiasAttribute() {
        if(is_null($this->delivered_date))
            return 0;
        $startDate  = date_create($this->created_at);
        $endDate    = date_create($this->delivered_date);

        $startDate  = date_format($startDate, 'Y-m-d');
        $endDate    = date_format($endDate, 'Y-m-d');

        $first  = \DateTime::createFromFormat('Y-m-d', $startDate);
        $second = \DateTime::createFromFormat('Y-m-d', $endDate);

        return intval(floor($first->diff($second)->days));
    }

    public function purchase() {
        return $this->belongsTo('App\Purchase');
    }

    public function promos() {
        return $this->belongsToMany('App\Promo', 'orders_has_promos');
    }
}
