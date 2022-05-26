<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;
use App\TypesHasCategories;
use App\Http\Requests\CategoriesRequest;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories=Category::all();
        foreach ($categories as $key => $value) {
            $value->subcategories;
            $value->types;
        }

        return response()->json($categories);
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
        $category=new Category(array(
            'name'=>$request->name,
            'color'=>$request->color
        ));

        $category->save();

        if(isset($request->types_id)){
            foreach ($request->types_id as $key => $value) {
                TypesHasCategories::create(array(
                    "type_id"=>$value,
                    "category_id"=>$category->id,
                ));
            }
        }

        return response()->json($category);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $category=Category::find($id);
        $category->subcategories;
        $category->types;

        return response()->json($category);
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
        $category=Category::find($id);
        
        $category->name=$request->name;
        $category->color=$request->color;
                
        $category->save();

        if(isset($request->types_id)){
            TypesHasCategories::where('category_id',$category->id)->delete();
            foreach ($request->types_id as $key => $value) {
                TypesHasCategories::create(array(
                    "type_id"=>$value,
                    "category_id"=>$category->id,
                ));
            }
        }

        return response()->json($category);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category=Category::find($id);

        if($category->delete()){
            return response()->json(['msg'=>'Categoria con el ID '.$id.' eliminada correctamente.']);
        }else{
            return response()->json(['msg'=>'Ocurrio un error al eliminar.'],500);
        }
        
        return false;
    }
}
