<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Helpers\CP;
use App\Client;
use App\Order;
use App\OrderHasExtra;
use App\Purchase;
use App\OrdersMove;
use App\Branch;
use App\Laboratory;
use App\Promo;
use JWTAuth;
use Mail;

class OrdersController extends Controller
{
    public function store(Request $request)
    {
        $user=Auth::user();
        $user->branch;
        if(!isset($user->branch->laboratory)){
            return response()->json(['msg'=>'No puedes realizar pedidos, contacta al administrador.'],500);
        }
        // dd($user);
        $purchase=new Purchase(array(
            "user_id"=>$user->id,
            "client_id"=>$request->client_id,
            "total"=>$request->total,
            "description"=>$request->observation
        ));
        $purchase->save();

        foreach ($request->cart as $key => $value) {
            $order = new Order(array(
                "purchase_id"=>$purchase->id,
                "product_has_subcategory_id"=>$value['product_has_subcategory_id'],
                "price"=>$value['price'],
                "discount"=>$value['discount'],
                "discount_percent"=>$value['percent_discount'],
                "service"=>$value['service'],
                "total"=>$value['total'],
                "iva"=>$value['iva'],
                "rx"=>$value['rx'],
                "laboratory_id"=>$user->branch->laboratory->id,
                "branch_id"=>$user->branch->id,
                "client_id"=>$request->client_id,
            ));

            $actualTotal = floatval($value['total']) - floatval($value['discount']);

            $order->iva = $this->_recalculateTax($actualTotal, $order->laboratory_id, $order->iva);
            $order->save();

            if($value['extras']) {
                foreach ($value['extras'] as $k => $v) {
                    OrderHasExtra::create(array(
                        "order_id"=>$order->id,
                        "extra_id"=>$v['extra_id'],
                        "price"=>$v['price'],
                    ));
                }
            }

            // if Order object is an Augen Mask it must be on ENTREGADO inmediatly
            if (strpos($order->rx, 'AM-') !== false) {
                $order->finish_date = date('Y-m-d');
                $order->delivered_date = date('Y-m-d');
                $order->status = 'entregado';
                $order->save();
            }
        }

        return response()->json(['msg'=>'Pedido generado correctamente!!']);
    }

    private function _recalculateTax($total, $laboratory_id, $actualTax) {
        // $client = Client::findOrFail($client_id);
        // $zipCode = $client->facturacion->postal_code;

        if(!isset($laboratory_id) || is_null($laboratory_id))
            return $actualTax;
        return in_array($laboratory_id, [2, 10]) ? (floatval($total) * 0.08) : $actualTax;
    }

    public function search($rx = null) {
        $user=Auth::user();

        if(is_null($rx))
        $orders = Order::with('client')
                       ->where('branch_id', $user->branch_id)
                       ->take(20)
                       ->get();
        else
            $orders = Order::where("rx",$rx)
                           ->where('branch_id', $user->branch_id)
                           ->with('client')
                           ->get();
        foreach ($orders as $key => $value) {
            $value->productHas;
            $value->extras;

            if(isset($orders[$key]->promo_discount))
                $orders[$key]->total_real = $orders[$key]->promo_discount;//number_format(floatval($orders[$key]->total)  - floatval($orders[$key]->discount) - floatval($orders[$key]->discount_admin) + floatval($orders[$key]->iva), 2);
            else
                $orders[$key]->total_real = number_format(floatval($orders[$key]->total)  - floatval($orders[$key]->discount) - floatval($orders[$key]->discount_admin) + floatval($orders[$key]->iva), 2);
        }

        return response()->json($orders, 200, [], JSON_NUMERIC_CHECK);
    }

    public function client($id){

        $orders = Order::where("client_id",$id)->where(function($query){
            $query->where("status","en_proceso")
                ->orWhere("status","terminado")
                ->orWhere("status","entregado")
                ->orWhere("status","garantia");
        })->get();

        foreach ($orders as $key => $value) {
            $value->productHas;
            $value->extras;
        }

        return response()->json($orders);
    }

    public function laboratory()
    {
        $user=Auth::user();
        $user->laboratory;
        if(!$user->laboratory){
            return response()->json(['msg'=>'No estas registrado en ningun laboratorio.','data'=>$user->laboratory],404);
        }

        $orders= Order::where("laboratory_id",$user->laboratory->id)->where(function($query){
            $query->where("status","en_proceso")->orWhere("status","garantia");
        })->get();

        foreach ($orders as $key => $value) {
            $value->productHas;
            $value->extras;
            $value->client;
        }

        foreach ($orders as $key => $value) {
            $orders[$key] = $orders[$key]->toArray();
            if(is_null($orders[$key]['client'])){
                $orders[$key] = array_merge($orders[$key], ['client'=>['name'=> 'Cliente eliminado']]);
            }
        }

        // dd($orders[0]);
        return response()->json($orders);

    }

    public function pendings(Request $request) {
        $start = $request->start;
        $end = $request->end;
        $status = $request->status;
        $start = date("Y-m-d H:i:s",strtotime($start." 00:00:00"));
        $end = date("Y-m-d H:i:s",strtotime($end." 23:59:59"));

        if(!$status['en_proceso'] && !$status['terminado'] && !$status['entregado'] && !$status['pagado'] && !$status['garantia'])
            return [];

        $orders = Order::query();

        if($request->has('search') && $request->input('search') != '')
            // $orders = $orders->where('rx', 'LIKE', '%' . $request->input('search') . '%');
            $orders = $orders->where('rx', $request->input('search'));
        else
            $orders = $orders->where("created_at", ">=" ,$start)->where("created_at", "<=", $end)->where(
                function($query) use ($status){
                    if($status['en_proceso']==true) $query->orWhere("status","like","en_proceso");
                    if($status['terminado']==true) $query->orWhere("status","like","terminado");
                    if($status['entregado']==true) $query->orWhere("status","like","entregado");
                    if($status['pagado']==true) $query->orWhere("status","like","pagado");
                    if($status['garantia']==true) $query->orWhere("status","like","garantia");
                }
            );


        $orders = $orders->get();
        foreach ($orders as $key => $value) {
            $value->productHas;
            $value->extras;
            $value->client;
            $value->branch;
        }

        foreach ($orders as $key => $value) {
            $orders[$key] = $orders[$key]->toArray();
            if(is_null($orders[$key]['client'])){
                $orders[$key] = array_merge($orders[$key], ['client'=>['name'=> 'Cliente eliminado']]);
            }
        }
        return response()->json($orders, 200, [], JSON_NUMERIC_CHECK);
    }

    public function discount(Request $request,$id)
    {
        $order = Order::find($id);
        $order->discount_admin=$request->discount;
        if($order->total <= ($order->discount+$order->discount_admin)){
            $order->payed=true;
            if($order->status == "entregado") {
                // $order->delivered_date = date('Y-m-d');
                // $order->payment_date = date('Y-m-d');
                // $order->status = "pagado";
            }
        }
        $order->save();

        return response()->json(['msg'=>"Descuento hecho por: $".$request->discount]);
    }

    public function today(Request $request, $branch_id = null) {
        $startDate  = $request->query('from') . ' 00:00:00';
        $endDate    = $request->query('to') . ' 23:59:59';

        $user = Auth::user();
        $branch_id =  $user->branch ? $user->branch->id : $branch_id;

        $orders= Order::whereBetween('created_at', [ $startDate, $endDate ])
                        ->where("branch_id", $branch_id)
                        ->get();

        foreach ($orders as $key => $value) {
            $value->productHas;
            $value->extras;
            $value->purchase;
        }
        return response()->json($orders);
    }

    public function changeStatus(Request $request,$id) {
        $order  = Order::find($id);
        $status = $request->input('status');
        $clientName = $order->client->name;

        define('EN_PROCESO', 'en_proceso');
        define('TERMINADO', 'terminado');
        define('ENTREGADO', 'entregado');
        define('PAGADO', 'pagado');
        define('GARANTIA', 'garantia');
        define('CANCELADO', 'cancelado');

        /*
         *  Order $order lifecycle
         *  EN PROCESO
         *  TERMINADO
         *  ENTREGADO
         *  PAGADO
         *  En cualquiero momento la orden puede pasar a
         *  CANCELADO
         *  Solo si la orden no está en EN PROCESO ó CANCELADO puede pasar a
         *  GARANTÍA
         */

         try {
             $prevStatus = $order->status;
             $order->status = $status;
             // dd($prevStatus, $status);
             switch ($prevStatus) {
                 case CANCELADO:
                    if($order->cancelled == 1) throw new \Exception("Error: Rx cancelada", 1);
                    break;
                 case EN_PROCESO:
                     if($status == TERMINADO)
                         $order->finish_date = date('Y-m-d');
                     else throw new \Exception("Error: flujo incorrecto", 1);
                     break;
                 case TERMINADO:
                    if($status == ENTREGADO) {
                        $order->delivered_date = date('Y-m-d');
                        // Si la Rx fue pagada con anerioridad pasa a pagado y cierre el flujo
                        if($order->payed == 1) {
                            $order->payment_date = date('Y-m-d');
                            $order->status = PAGADO;
                        }
                        // Si el cliente es de CORTESÍA la Rx pasa a pagado y cierra el flujo.
                        if (strpos($clientName, 'CORTESIA') !== false) {
                            $order->payment_date = date('Y-m-d');
                            $order->status = PAGADO;
                            $order->payed = 1;
                        }
                    } else if($status == GARANTIA)
                        $order->warranty_date = date('Y-m-d');
                    else throw new \Exception("Error: flujo incorrecto", 1);
                    break;
                 case ENTREGADO:
                    if($status == PAGADO) {
                        $order->payment_date = date('Y-m-d');
                        $order->payed = 1;
                    }
                    else if($status == GARANTIA)
                        $order->warranty_date = date('Y-m-d');
                    else throw new \Exception("Error: flujo incorrecto", 1);
                    break;
                 case PAGADO:
                    if($status == GARANTIA)
                        $order->warranty_date = date('Y-m-d');
                    else throw new \Exception("Error: flujo incorrecto", 1);
                    break;
                 case GARANTIA:
                    if($status == TERMINADO)
                        $status = ENTREGADO;
                    if($status == ENTREGADO) {
                         $order->delivered_date2 = date('Y-m-d');
                         // Si la Rx fue pagada con anerioridad pasa a pagado y cierre el flujo
                         if($order->payed == 1) {
                             $order->payment_date = date('Y-m-d');
                             $order->status = PAGADO;
                         }
                         // Si el cliente es de CORTESÍA la Rx pasa a pagado y cierra el flujo.
                         if (strpos($clientName, 'CORTESIA') !== false) {
                             $order->payment_date = date('Y-m-d');
                             $order->status = PAGADO;
                             $order->payed = 1;
                         }
                    } else throw new \Exception("Error: flujo incorrecto", 1);
                    break;
                 default:
                     throw new \Exception("Error: flujo incorrecto", 1);
                     break;
             }

             $order->save();

             return response()->json(['msg'=>"Estatus de la orden se cambio a " . $order->status ]);
         } catch(\Exception $e) {
             return response()->json(['msg'=> $e->getMessage()]);
         }
    }

    public function changeLaboratory(Request $request,$id)
    {
        $order=Order::find($id);
        OrdersMove::create(array(
            "from"=> $order->laboratory_id,
            "to"=> $request->laboratory_id
        ));

        $order->laboratory_id=$request->laboratory_id;
        $order->save();
        return response()->json(['msg'=>"RX fue movido a otro laboratorio con exito."]);
    }

    public function paymentsPDV(Request $request, $branch_id) {
        $orders = Order::where("branch_id", $branch_id)
                       // ->where("created_at",">=",$start)
                       // ->where("created_at","<=",$end)
                       ->where("status","like","entregado")
                       ->where("payed",false)
                       ->orderByRaw('cast(rx as unsigned)')
                       ->get();



        foreach ($orders as $key => $value) {
            $value->productHas;
            $value->extras;
            $value->client;
            $value->branch;
            if(isset($value->client))
                $value->client->facturacion;
        }



        foreach ($orders as $key => $value) {
            $orders[$key] = $orders[$key]->toArray();
            if(is_null($orders[$key]['client'])){
                $orders[$key] = array_merge($orders[$key], ['client'=>['name'=> 'Cliente eliminado']]);
            }
        }

        return response()->json($orders, 200, [], JSON_NUMERIC_CHECK);

    }

    public function payments(Request $request, $branch_id = null)
    {
        $user=Auth::user();
        // $start=$request->start;
        // $end=$request->end;
        // $start=date("Y-m-d H:i:s",strtotime($start." 00:00:00"));
        // $end=date("Y-m-d H:i:s",strtotime($end." 23:59:59"));

        $branch_id =  $user->branch ? $user->branch->id : $branch_id;

        if($user->hasPermissionTo('cobranza_admin') && $branch_id == null) {
            $branchs = Branch::all();
            foreach ($branchs as $key => $value) {
                // $value->clients;
                $orders = Order::where("branch_id",$value->id)
                               // ->where("created_at",">=",$start)
                               // ->where("created_at","<=",$end)
                               ->where("status","like","entregado")
                               ->where("payed",false);

                if($request->has('client')) {
                    $orders = $orders->whereHas("client", function($query) use ($request) {
                        $query->where("name","like","%".$request->client."%")->orWhere("comertial_name","like","%".$request->client."%");
                    });
                }

                if(!empty($request->rx)){
                    $orders = $orders->where("rx",$request->rx);
                }


                // $orders = $orders->groupBy("orders.client_id");

                $orders = $orders->get();

                // foreach ($orders as $order) {
                //     $order->productHas;
                //     $order->extras;
                //     $order->client;
                //     $order->branch;
                //     $order->client->facturacion;
                // }

                $value->orders=$orders;
            }

            return response()->json($branchs);
        } else {

            $consult = Order::selectRaw("orders.client_id")
                            ->where("status","entregado")
                            // ->where("created_at",">=",$start)
                            // ->where("created_at","<=",$end)
                            ->where("payed",false)
                            ->where("branch_id", $branch_id);

            if(!empty($request->rx)){
                $consult=$consult->where("rx",$request->rx);
            }

            $consult=$consult->groupBy("orders.client_id");
            // $consult = $consult->orderByRaw('cast(rx as unsigned)');
            $payments=$consult->get();
            // dd($payments);
            $clients=[];
            foreach ($payments as $key => $value) {
                $client=Client::where("id",$value->client_id);
                if(!empty($request->client)){
                    $client = $client->where(function($query) use ($request){
                        $query->where("name","like","%".$request->client."%")->orWhere("comertial_name","like","%".$request->client."%");
                    });
                }

                $client=$client->first();
                if(is_null($client))
                    continue;

                $client->total = 0.0;
                $client->discounts = 0.0;
                $client->discounts_admin = 0.0;
                $client->ivas = 0.0;

                $filteredOrders = $client->orders()
                                         ->with('extras', 'client')
                                         ->where("status","entregado")
                                         // ->where("created_at",">=",$start)
                                         // ->where("created_at","<=",$end)
                                         ->where("payed",false)
                                         ->where("branch_id", $branch_id)
                                         ->orderByRaw('cast(rx as unsigned)')
                                         ->get();

                foreach ($filteredOrders as &$order) {
                    $order->productHas;
                    if($order->passed <= 0) {
                        $client->total += $order->total;
                        $client->discounts += $order->discount;
                        $client->discounts_admin += $order->discount_admin;
                        $client->ivas += $order->iva;
                    }
                }

                $client->orders = $filteredOrders;
                array_push($clients,$client);
            }
            return response()->json($clients, 200, [], JSON_NUMERIC_CHECK);
        }

    }

    public function ofClient(Request $request, $client_id, $branch_id = null) {
        // $startDate = $request->query('from');
        // $endDate = $request->query('to');

        $user=Auth::user();
        // $user->branch;
        $branch_id =  $user->branch ? $user->branch->id : $branch_id;

        $orders=Order::where("branch_id", $branch_id)
                     // ->whereBetween('created_at', [ $startDate, $endDate ])
                     ->where("status","entregado")
                     ->where("payed",false)
                     ->where("client_id",$client_id)
                     ->get();

        foreach ($orders as $key => $value) {
            $value->productHas;
            $value->extras;
            $value->client;
            $value->branch;
            $value->client->facturacion;
        }

        foreach ($orders as $key => $value) {
            $orders[$key] = $orders[$key]->toArray();
            if(is_null($orders[$key]['client'])){
                $orders[$key] = array_merge($orders[$key], ['client'=>['name'=> 'Cliente eliminado']]);
            }
        }

        return response()->json($orders);
    }

    public function ofBranch(Request $request, $branch_id) {
        $startDate = $request->query('from');
        $endDate = $request->query('to');

        $orders=Order::where("branch_id", $branch_id)
                     ->whereBetween('created_at', [ $startDate, $endDate ])
                     ->where("status","entregado")
                     ->where("payed",false)
                     ->get();

        foreach ($orders as $key => $value) {
            $value->productHas;
            $value->extras;
            $value->client;
            $value->branch;
            $value->client->facturacion;
        }

        foreach ($orders as $key => $value) {
            $orders[$key] = $orders[$key]->toArray();
            if(is_null($orders[$key]['client'])){
                $orders[$key] = array_merge($orders[$key], ['client'=>['name'=> 'Cliente eliminado']]);
            }
        }

        return response()->json($orders);
    }

    private function _payOrder($id){
        $order=Order::find($id);
        $order->status="pagado";
        $order->payed=true;
        $order->payment_date = date('Y-m-d');
        $order->save();
    }

    public function pay(Request $request,$id)
    {
        $this->_payOrder($id);

        return response()->json(['msg'=>'La RX fue cobrada.']);
    }

    public function payMany(Request $request){
        $ids=$request->ids;
        // dd($ids);
        foreach ($ids as $key => $id) {
            $this->_payOrder($id);
        }

        return response()->json(['msg'=>'Todos los RX fueron cobrados.']);
    }

    public function statusTable(Request $request) {
        // $branch_id  = $request->input('lab');
        $start      = $request->input('start') . ' 00:00:00';
        $end        = $request->input('end') . ' 23:59:59';
        // $status     = $request->input('status');
        // $date = $request->input('date');
        $rx = $request->input('rx');

        $user=Auth::user();

        if(isset($rx) && $rx != '') {
            if($user->hasRole('punto de ventas') || $user->hasRole('Ejecutivo')) {
                $orders = Order::where('branch_id', $user->branch_id)->where('rx', $rx)->get();
            } else {
                $orders = Order::where('rx', $rx)->get();
            }

            foreach ($orders as $key => $value) {
                $value->productHas;
                $value->extras;
                $value->client;
                $value->branch;
                $value->laboratory;
                $value->branch;
                $value->client->facturacion;
            }

            foreach ($orders as $key => $value) {
                $orders[$key] = $orders[$key]->toArray();
                if(is_null($orders[$key]['client'])){
                    $orders[$key] = array_merge($orders[$key], ['client'=>['name'=> 'Cliente eliminado']]);
                }
            }

            return response()->json($orders);
        } else {
            if($request->has('q')) {
                $field  = $request->query('q');

                if($user->hasRole('punto de ventas') || $user->hasRole('Ejecutivo'))
                    $orders =  Order::where('branch_id', $user->branch_id)->whereBetween($field, [$start, $end])->orderBy($field, 'ASC')->orderByRaw('cast(rx as unsigned)')->get();
                else
                    $orders =  Order::whereBetween($field, [$start, $end])->orderBy($field, 'ASC')->orderByRaw('cast(rx as unsigned)')->get();

                foreach ($orders as $key => $value) {
                    $value->productHas;
                    $value->extras;
                    $value->client;
                    $value->branch;
                    $value->laboratory;
                    $value->branch;
                    // $value->client->facturacion;
                }

                foreach ($orders as $key => $value) {
                    $orders[$key] = $orders[$key]->toArray();
                    if(is_null($orders[$key]['client'])){
                        $orders[$key] = array_merge($orders[$key], ['client'=>['name'=> 'Cliente eliminado']]);
                    }
                }

                return response()->json($orders);
            } else {
                if($user->hasRole('punto de ventas') || $user->hasRole('Ejecutivo')) {
                    $data = [
                        'terminado' => Order::where('branch_id', $user->branch_id)->whereBetween('finish_date', [$start, $end])->count(),
                        'entregado' => Order::where('branch_id', $user->branch_id)->whereBetween('delivered_date', [$start, $end])->count(),
                        'pagado'    => Order::where('branch_id', $user->branch_id)->whereBetween('payment_date', [$start, $end])->count(),
                        'garantia'    => Order::where('branch_id', $user->branch_id)->whereBetween('delivered_date2', [$start, $end])->count()//->where('warranty_date', '<=', $date)->count()
                    ];
                } else {
                    $data = [
                        'terminado' => Order::whereBetween('finish_date', [$start, $end])->count(),
                        'entregado' => Order::whereBetween('delivered_date', [$start, $end])->whereNull('warranty_date')->count(),
                        'pagado'    => Order::whereBetween('payment_date', [$start, $end])->count(),
                        'garantia'    => Order::whereBetween('delivered_date2', [$start, $end])->count()//->where('warranty_date', '<=', $date)->count()
                    ];
                }

                // $orders = Order::with('laboratory', 'client')
                //                 ->where('branch_id', $branch_id)
                //                 ->whereBetween('created_at', [$start, $end])
                //                 ->where(
                //                     function($query) use ($status){
                //                         if($status['en_proceso'] == true) $query->orWhere("status","like","en_proceso");
                //                         if($status['terminado'] == true) $query->orWhere("status","like","terminado");
                //                         if($status['entregado'] == true) $query->orWhere("status","like","entregado");
                //                         if($status['pagado'] == true) $query->orWhere("status","like","pagado");
                //                     }
                //                 )->get();

                // foreach ($orders as $order) {
                //     $order->productHas;
                // }

                return response()->json($data);
            }
        }
    }

    public function recalcutateOrdersSinceDate() {
        set_time_limit(0);
        ini_set("memory_limit", "-1");
        ini_set('max_execution_time', 0);


        $datetime = '2019-02-01 00:00:00';
        $ordersModified = 0;
        $orders = Order::where('created_at', '>=', $datetime)->get();
        // $ids = [62645, 62646, 62647, 62648];
        // $orders = Order::whereIn('id', $ids)->get();

        try {
            foreach($orders as $order) {
                $zipCode = $order->client->facturacion->postal_code;

                if(!isset($zipCode) || is_null($zipCode))
                    continue;

                if(in_array($order->laboratory_id, [2, 10])) {
                // if(CP::searchZipCode($zipCode)) {
                    $actualTotal = floatval($order->total) - floatval($order->discount);
                    $order->iva = $actualTotal * 0.08;
                    $order->save();
                    $ordersModified++;
                } else {
                    $actualTotal = floatval($order->total) - floatval($order->discount);
                    $order->iva = $actualTotal * 0.16;
                    $order->save();
                    $ordersModified++;
                }
            }
        } catch(\Exception $e) {
            dd($e->getMessage());
        }

        return response()->json([
            'status'    => true,
            'message'   => sprintf('(%s) ordenes modificadas', $ordersModified),
            'data'      => null
        ]);
    }


    public function screen(Request $request) {
        date_default_timezone_set('America/Mexico_City');
        $lab = $request->query('lab');
        $laboratories = Laboratory::all();
        $id = null;
        foreach ($laboratories as $laboratory) {
            if(substr(md5($laboratory->id), 0, 5) == $lab) {
                $id = $laboratory->id;
                break;
            }
        }

        if(is_null($id)) return response()->json([]);

        $pages = [];
        $pageSize = $id == 3 ? 25 : 20;
        for($i = 0; $i < 4; $i++) {
            $orders = Order::with('branch')
                            ->where('laboratory_id', $id)
                            ->where(function($query) {
                                $query->where('status', 'en_proceso')
                                      ->orWhere(function($q){
                                         $q->where('status', 'garantia')
                                           ->whereNotNull('delivered_date');
                                      });
                            })
                            // ->whereIn('status', ['en_proceso', 'garantia'])
                            ->skip($i * $pageSize)
                            ->take($pageSize)
                            ->orderBy('created_at', 'ASC')
                            ->get();


            foreach($orders as &$order) {
                $start = new \DateTime($order->created_at);
                $end = new \DateTime(date('Y-m-d H:i:s'));
                $days = $start->diff($end, true)->days;
                $sundays = intval($days / 7) + ($start->format('N') + $days % 7 >= 7);
                $order->days_diff = $days - $sundays;

                $order->formatted_date = date('d-M', strtotime($order->created_at));


            }

            $pages[] = $orders;
        }

        return response()->json(compact('pages'));
    }

    public function sendPromotionalEmail($id = null) {
        if(is_null($id)) {
            // TEST MAIL
            // $order = Order::inRandomOrder()->first();
            $order = Order::findOrFail(41083);

            $order->client;
            $order->productHas;
            $order->extras;

            if(!is_null($order->client)) {
                // Prepare test view
                if(false) {
                    Mail::send('emails.promotional', compact('order'), function ($m) {
                        $m->from('no-reply@augensistema.com', 'AUGEN Newsletter');

                        $m->to('mkt@dlo.com.mx', 'Yorch')->subject('Augen TEST');
                        $m->bcc('pgarcia@dlo.com.mx')->subject('Augen TEST');
                        $m->to('sistemas@dlo.com.mx')->subject('Augen TEST');
                    });
                }

                return view('emails.promotional', compact('order'));

                return response()->json(['msg' => 'Test mail sent']);
            }
        } else {
            $order = Order::findOrFail($id);
            return true;
        }
    }

    public function refreshCourtesy() {
        $orders = Order::whereHas('client', function($query) {
            $query->where('name', 'LIKE', '%CORTESIA%');
        })->where('status', 'entregado')
        ->with('client')
        ->update([
            'status' => 'pagado',
            'payment_date' => date('Y-m-d'),
            'payed' =>  1,
        ]);

        return response()->json(['msg' => 'Orders refreshed']);
    }

    public function buenFin() {
        $readingHeaders = true;
        // echo '<table>';
        if (($manager = fopen(base_path('import.csv'), "r")) !== FALSE) {
            while (($row = fgetcsv($manager, 1000, ",")) !== FALSE) {
                if($readingHeaders) {
                    $readingHeaders = false;
                    continue;
                }

                $promo = Promo::findOrFail(1);

                // echo '<tr>';

                if($row[1] == 'SLP')
                    $branch = Branch::findOrFail(10);
                else if($row[1] == 'MTY')
                    $branch = Branch::findOrFail(8);
                else
                    $branch = Branch::where('name', 'LIKE', '%' .  $row[1] . '%')->first();

                // if(is_null($branch))
                //     dd($row);
                // dd($row);
                DB::enableQueryLog();
                $order = Order::whereIn('rx', [ str_replace("O", "0", $row[2]), '0' . $row[2], str_replace("*", "", $row[2]) ])
                              ->where('branch_id', $branch->id)
                              ->whereBetween('created_at', [ $promo->starts_at, $promo->ends_at])
                              ->first();

              if(is_null($order)) {

                  echo $row[2] . '<br/>';
                  continue;
                  // dd(DB::getQueryLog());
              }
              continue;


                if(!is_null($order)) {
                    // $order->discount_admin = 0.0;
                    // $orde
                    if($order->discount_admin == 0.0) {
                        // $partialAmount = round(floatval($order->total) - floatval($order->discount) - floatval($order->discount_admin) + floatval($order->iva), 2);
                        $finalPrice = floatval($order->total) / 2;

                        $order->iva = $this->_recalculateTax($finalPrice, $order->laboratory_id, $finalPrice * 0.16);
                        $order->promo_discount = $finalPrice + $order->iva;

                        $order->promos()->sync([1]);
                        $order->save();
                        // dd($order->iva, $finalPrice);

                        // dd($partialAmount, $finalPrice, $order->iva);
                    }
                }

                // if(is_null($order)) {
                //
                //     echo $row[2] . '<br/>';
                //     continue;
                //     // dd(DB::getQueryLog());
                // }
                // else
                //     continue;
                    // DB::disableQueryLog();
                              // ->whereRaw('DATE(created_at) >= "' . vsprintf('%3$s-%2$s-%1$s', explode('/', $row[0])) . '"')



                // if(count($order) > 1 || count($order) == 0) {
                //     // dd($row);$order = Order::where('rx', str_replace("O", "0", $row[2]))
                //     $order = Order::where('rx', '0' . $row[2])
                //                   ->where('branch_id', $branch->id)
                //
                //                   // ->whereRaw('DATE(created_at) >= "' . vsprintf('%3$s-%2$s-%1$s', explode('/', $row[0])) . '"')
                // $order->promos()->sync([1]);
                //                   ->get();
                // }
                // if(count($order) > 1 || count($order) == 0) {
                //     echo $row[2] . ' - ' . $row[0] . '<br/>';
                // }
                // else {
                //     DB::disableQueryLog();
                //     continue;
                //     $order = $order[0];
                //     $promoId = 1;
                //
                //     // dd($order);
                //
                //     echo '
                //         <td> ' . $order->subtotal .'</td>
                //         <td> ' . floatval($order->subtotal / 2)  .'</td>
                //     ';
                // }

                // echo '</tr>';
            }
            fclose($manager);
            return response()->json(['msg' => 'Orders with promo :)']);
        } else {
            return response()->json(['msg' => 'No se encontró el archivo']);
        }
        // echo '</table>';

    }

    public function cancelOrder($id) {
        $order = Order::findOrFail($id);
        if($order->payed == 1)
            return response()->json(['msg' => 'RX no se puede cancelar, ya se encuentra pagada']);

        $order->cancelled = 1;
        $order->status = 'cancelado';
        $order->user_id = Auth::user()->id;

        $order->save();

        return response()->json(['msg' => 'RX cancelada correctamente']);
    }

    public function partalPayOrder($id) {
        $order = Order::findOrFail($id);
        $order->payed = 1;
        $order->user_id = Auth::user()->id;

        if($order->status == 'entregado') {
            $order->payment_date = date('Y-m-d');
            $order->status = 'pagado';
        }

        $order->save();

        return response()->json(['msg' => 'Rx reportada como pagada correctamente']);
    }

    public function applyCourtesy($id) {
        $order = Order::findOrFail($id);
        $order->courtesy = 1;
        $order->status = 'pagado';
        $order->user_id = Auth::user()->id;

        $order->save();

        return response()->json(['msg' => 'Cortesía aplicada correctamente']);
    }

    public function generateRx(Request $request) {
        if($request->has('paqueteria')) {
            $rx = 'PAQ-';
            $actualSold = Order::where('rx', 'LIKE', '%PAQ-%')->count();
            $actualSold++;

            $rx .= str_pad($actualSold, 5, '0', STR_PAD_LEFT);

            return $rx;
        } else {
            $rx = 'AM-';
            $actualSold = Order::where('rx', 'LIKE', '%AM-%')->count();
            $actualSold++;

            $rx .= str_pad($actualSold, 5, '0', STR_PAD_LEFT);

            return $rx;
        }
    }

    public function checkDuplicate($rx) {
        $today = date('Y-m-d');
        $order = Order::where('rx', $rx)
                      ->whereRaw("DATE(created_at) = '$today'")
                      ->first();

        if(!is_null($order))
            return 'true';
        return 'false';
    }
}
