<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ProductsRequest;
use App\Product;
use App\ProductHasSubcategory;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products=Product::all();
        foreach ($products as $key => $value) {
            $value->type;
            $value->extras;
        }

        return response()->json($products);
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
    public function store(ProductsRequest $request)
    {
        $product=new Product(array(
            'name'=>$request->name,
            'code'=>$request->code,
            'type_id'=>$request->type_id,
            'material'=>$request->material,
            'description'=>$request->description,
            'delivery'=>$request->delivery
        ));

        $product->save();

        if($request->prices){
            foreach ($request->prices as $key => $value) {
                ProductHasSubcategory::create(array(
                    'product_id'=>$product->id,
                    'subcategory_id'=>$value['subcategory_id'],
                    'category_id'=>$value['category_id'],
                    'type_id'=>$value['type_id'],
                    'price'=>$value['price']
                ));
            }
        }

        return response()->json($product);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product=Product::find($id);
        $product->type;
        $product->subcategories;
        $product->prices = $product->prices()->orderBy('id', 'DESC')->get();
        return response()->json($product);
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
    public function update(ProductsRequest $request, $id)
    {
        $product=Product::find($id);
        $product->name=$request->name;
        $product->code=$request->code;
        $product->type_id=$request->type_id;
        $product->material=$request->material;
        $product->description=$request->description;
        $product->delivery=$request->delivery;

        $product->save();

        if($request->prices){
            // ProductHasSubcategory::where('product_id',$product->id)->delete();
            foreach ($request->prices as $key => $value) {
                if(isset($value['id'])) {
                    $actualPrice = ProductHasSubcategory::findOrFail($value['id'])->price;
                }
                if(!isset($value['id']) || $value['price'] !== $actualPrice) {
                    ProductHasSubcategory::create(array(
                        'product_id'=>$id,
                        'subcategory_id'=>$value['subcategory_id'],
                        'category_id'=>$value['category_id'],
                        'type_id'=>$value['type_id'],
                        'price'=>$value['price']
                    ));
                }
            }
        }

        return response()->json($product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product=Product::find($id);
        $product->delete();

        return response()->json(['msg'=>"Producto eliminado."]);
    }

    public function type($type_id)
    {
        $products = Product::where('type_id', $type_id)->get();

        foreach ($products as $key => $value) {
            $value->subcategories;
            $value->prices = DB::table('product_has_subcategories')->whereIn('id', function($query) use ($value) {
                $query->selectRaw('MAX(id)')
                      ->from('product_has_subcategories')
                      ->where('product_id', $value->id)
                      ->groupBy('subcategory_id', 'type_id', 'category_id');
            })->get();
            // $value->prices;
            $value->extras;
        }
        return response()->json($products);
    }
}
