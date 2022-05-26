<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Branch;
use App\Order;
// use App\Town;
// use App\State;
// use App\Laboratory;
use App\Http\Requests\BranchRequest;
use Illuminate\Support\Facades\DB;

class BranchesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $branches=Branch::all();

        foreach ($branches as $key => &$value) {
            $value->town;
            $value->state;
            $value->laboratory;
        }

        return response()->json($branches);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BranchRequest $request)
    {
        $branch=new Branch(array(
            'name'=>$request->name,
            'state_id'=>$request->state_id,
            'town_id'=>$request->town_id,
            'laboratory_id'=>$request->laboratory_id,
            'address'=>$request->address
        ));

        $branch->save();

        return response()->json($branch);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $branch=Branch::find($id);
        $branch->state;
        $branch->town;
        $branch->laboratory;

        return response()->json($branch);
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
        $branch=Branch::find($id);

        $branch->name=$request->name;
        $branch->state_id=$request->state_id;
        $branch->town_id=$request->town_id;
        $branch->laboratory_id=$request->laboratory_id;
        $branch->address=$request->address;

        $branch->save();

        return response()->json($branch);
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
            return response()->json(['msg'=>'Sucursal con ID '.$id.' eliminado.']);
        }
        else{
            return response()->json(['msg'=>'Ocurrio un error al eliminar.'],500);
        }
    }

    private function _destroy($id)
    {
        $branch=Branch::find($id);
        if($branch->delete()){
            return true;
        }
        else{
            return false;
        }

    }

    public function today(Request $request) {
        $startDate  = $request->query('from') . ' 00:00:00';
        $endDate    = $request->query('to') . ' 23:59:59';
        // $branches   = [];
        $subtotal   = 0.0;
        $discounts = 0.0;
        $total      = 0.0;
        $ordersCount = 0;

        $branches = Branch::all();
        foreach ($branches as &$branch) {
            $data = DB::table(with(new Order)->getTable())
                      ->selectRaw('SUM(total) AS q1, SUM(discount) as q2, SUM(discount_admin) AS q3,  SUM(iva) AS q4, count( DISTINCT(client_id)) AS q5')
                      ->whereBetween('created_at', [ $startDate, $endDate ])
                      ->where('branch_id', $branch->id)
                      // ->groupBy('client_id')
                      ->get();

            $data = $data[0];

            $branch->subtotal = floatval($data->q1) + floatval($data->q4);
            $branch->descuentos = floatval($data->q2);
            $branch->descuentos_admin = floatval($data->q3);
            $branch->total = $branch->subtotal - $branch->descuentos - $branch->descuentos_admin;

            $subtotal   += $branch->subtotal;
            $discounts += $branch->descuentos + $branch->descuentos_admin;

            $branch->clients_orders = $data->q5;
            $branch->orders = Order::whereBetween('created_at', [ $startDate, $endDate ])
                            ->where("branch_id", $branch->id)
                            ->count();

            $ordersCount += $branch->orders;
        }

        $total = $subtotal - $discounts;

        $avg = $total / $ordersCount;

        return response()->json(compact('branches', 'subtotal', 'discounts', 'total', 'ordersCount', 'avg'));
    }
}
