<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Laboratory;
use App\Http\Requests\LaboratoryRequest;

class LaboratoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $laboratories=Laboratory::all();
        foreach ($laboratories as $key => $value) {
            $value->state;
            $value->town;
        }

        return response()->json($laboratories);
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
    public function store(LaboratoryRequest $request)
    {
        $laboratory=new Laboratory(array(
            'name'=>$request->name,
            'state_id'=>$request->state_id,
            'town_id'=>$request->town_id,
            'address'=>$request->address
        ));

        $laboratory->save();

        return response()->json($laboratory);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $laboratory=Laboratory::find($id);
        $laboratory->state;
        $laboratory->town;

        return response()->json($laboratory);
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
        $laboratory=Laboratory::find($id);
        
        $laboratory->name=$request->name;
        $laboratory->state_id=$request->state_id;
        $laboratory->town_id=$request->town_id;
        $laboratory->address=$request->address;
        
        $laboratory->save();

        return response()->json($laboratory);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $laboratory=Laboratory::find($id);

        if($laboratory->delete()){
            return response()->json(['msg'=>'Laboratorio con el ID '.$id.' eliminado correctamente.']);
        }else{
            return response()->json(['msg'=>'Ocurrio un error al eliminar.'],500);
        }

        return false;
    }
}
