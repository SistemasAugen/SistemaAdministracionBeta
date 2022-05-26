<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Extra;
use App\ExtraHasProduct;

class ExtrasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $extras=Extra::all();
        foreach ($extras as $key => $value) {
            $value->products;
        }

        return $extras;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $extra=new Extra(array(
            "name"=>$request->name,
            "description"=>$request->description,
            "price"=>$request->price,
            "free_form"=>$request->free_form
        ));
        $extra->save();

        if($request->products_id){
            foreach ($request->products_id as $key => $value) {
                ExtraHasProduct::create(array(
                    "extra_id"=>$extra->id,
                    "product_id"=>$value
                ));
            }
        }

        return response()->json($extra);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $extra=Extra::find($id);
        $extra->products;

        return response()->json($extra);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $extra=Extra::find($id);
        $extra->name=$request->name;
        $extra->description=$request->description;
        $extra->price=$request->price;
        $extra->free_form=$request->free_form;
        $extra->save();

        if($request->products_id){
            ExtraHasProduct::where("extra_id",$extra->id)->delete();
            foreach ($request->products_id as $key => $value) {
                ExtraHasProduct::create(array(
                    "extra_id"=>$extra->id,
                    "product_id"=>$value
                ));
            }
        }

        return response()->json($extra);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $extra=Extra::find($id);

        if($extra->delete()){
            return response()->json(['msg'=>"Eliminado correctamente."],200);
        }else{
            return response()->json(['msg'=>"Ocurrio un error al eliminar."],500);
        }
    }
}
