<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Subcategory;
use App\SubcategoriesHasTypes;
use App\CategoriesHasSubcategories;
use App\Http\Requests\SubcategoriesRequest;

class SubcategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $subcategories=Subcategory::all();
        foreach ($subcategories as $key => $value) {
            $value->categories;
            $value->types;
        }

        return response()->json($subcategories);
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
    public function store(SubcategoriesRequest $request)
    {
        $subcategory=new Subcategory(array(
            'name'=>$request->name,
            'description'=>$request->description,
            'antireflective'=>$request->antireflective,
            'no_antireflective'=>$request->no_antireflective
        ));
        $subcategory->save();

        if($request->categories_id){
            foreach ($request->categories_id as $key => $value) {
                CategoriesHasSubcategories::create(array(
                    'category_id'=>$value,
                    'subcategory_id'=>$subcategory->id
                ));
            }
        }
        if($request->types_id){
            foreach ($request->types_id as $key => $value) {
                SubcategoriesHasTypes::create(array(
                    'type_id'=>$value,
                    'subcategory_id'=>$subcategory->id
                ));
            }
        }

        return response()->json($subcategory);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $subcategory=Subcategory::find($id);
        $subcategory->categories;
        $subcategory->types;

        return response()->json($subcategory);
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
    public function update(SubcategoriesRequest $request, $id)
    {
        $subcategory=Subcategory::find($id);
        $subcategory->name=$request->name;
        $subcategory->description=$request->description;
        $subcategory->antireflective=$request->antireflective;
        $subcategory->no_antireflective=$request->no_antireflective;

        $subcategory->save();

        if($request->categories_id){
            CategoriesHasSubcategories::where('subcategory_id',$subcategory->id)->delete();
            foreach ($request->categories_id as $key => $value) {

                CategoriesHasSubcategories::create(array(
                    'category_id'=>$value,
                    'subcategory_id'=>$subcategory->id
                ));
            }
        }

        if($request->types_id){
            SubcategoriesHasTypes::where('subcategory_id',$subcategory->id)->delete();
            foreach ($request->types_id as $key => $value) {
                SubcategoriesHasTypes::create(array(
                    'type_id'=>$value,
                    'subcategory_id'=>$subcategory->id
                ));
            }
        }

        return response()->json($subcategory);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if($this->_destroy($id)){
            return response()->json(['msg'=>"Subcategoria con el ID ".$id." eliminado correctamente."]);
        }

        return response()->json(['msg'=>"Ocurrio un error al eliminar"],500);
    }

    public function destroyMultiple($id)
    {
        if ($request->ids) {
            foreach ($request->ids as $key => $value) {
                $status=$this->_destroy($value);
                if(!$status)
                    break;
            }

            if ($status) {
                return response()->json(['msg'=>'Subcategorias eliminadas.']);
            }
        }

        return response()->json(['msg'=>'Ocurrio un error al eliminar.'],500);

    }

    private function _destroy($id)
    {
        $subcategory=Subcategory::find($id);

        if($subcategory->delete()){
            CategoriesHasSubcategories::where('subcategory_id',$id)->delete();
            return true;
        }

        return false;
    }
}
