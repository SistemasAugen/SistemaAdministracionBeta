<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Type;
use app\Http\Requests\TypesRequest;

class TypesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $types=Type::all();
        foreach ($types as $key => $value) {
            $value->categories;
        }

        return response()->json($types);
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
        $type=new Type(array(
            'name'=>$request->name,
            'description'=>$request->description
        ));

        $type->save();

        return response()->json($type);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $type=Type::find($id);

        return response()->json($type);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $type=Type::find($id);
        $type->categories();

        return response()->json($type);
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
        $type=Type::find($id);
        
        $type->name=$request->name;
        $type->description=$request->description;
        
        $type->save();

        return response()->json($type);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $type=Type::find($id);

        if ($type->delete()) {
            return response()->json(['msg'=>'Tipo con el ID '.$id.' eliminado correctamente.']);
        }else{
            return response()->json(['msg'=>'Ocurrio un error al eliminar.'],500);
        }
        
        return false;
    }
}
