<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests\ClientsRequest;
use App\Client;
use App\Discount;
use App\Facturacion;
use App\Order;
use App\User;
use App\Branch;


class ClientsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $clients = Client::all();
        foreach ($clients as $key => $value) {
            $value->state;
            $value->town;
            $value->facturacion;
        }
        return response()->json($clients);
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
    public function store(ClientsRequest $request)
    {
        $client=new Client(array(
            'name'=>$request->name,
            'email'=>$request->email,
            'phone'=>$request->phone,
            'celphone'=>$request->celphone,
            'comertial_name'=>$request->comertial_name,
            'address'=>$request->address,
            'suburb'=>$request->suburb,
            'state_id'=>$request->state_id,
            'town_id'=>$request->town_id,
            'postal_code'=>$request->postal_code,
            'branch_id'=>$request->branch_id,
            'contact_name'=>$request->contact_name,
            'contact_phone'=>$request->contact_phone,
            'contact_celphone'=>$request->contact_celphone,
            'contact_email'=>$request->contact_email,
            'payment_plan'=>$request->payment_plan,
            'status'=>$request->status,
            'category'=>$request->category,
        ));

        $client->save();

        if($request->discounts){
            foreach ($request->discounts as $key => $value) {
                Discount::create(array(
                    'client_id'=>$client->id,
                    'discount'=>$value['discount'],
                    'type_id'=>$value['type_id']
                ));
            }
        }
        if ($request->facturacion) {
            $facturacion=$request->facturacion;
            Facturacion::create(array(
                'client_id'=>$client->id,
                'name'=>$facturacion['name'],
                'phone'=>$facturacion['phone'],
                'celphone'=>$facturacion['celphone'],
                'rfc'=>$facturacion['rfc'],
                'address'=>$facturacion['address'],
                'suburb'=>$facturacion['suburb'],
                'state_id'=>$facturacion['state_id'],
                'town_id'=>$facturacion['town_id'],
                'postal_code'=>$facturacion['postal_code']
            ));
        }

        return response()->json($client);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $client=Client::findOrFail($id);
        $client->town;
        $client->state;
        $client->discounts;
        $client->facturacion;
        $client->facturacion->state;
        $client->facturacion->town;
        $client->branch;
        return response()->json($client);
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
        $client=Client::find($id);
        $client->name=$request->name;
        $client->email=$request->email;
        $client->phone=$request->phone;
        $client->celphone=$request->celphone;
        $client->comertial_name=$request->comertial_name;
        //$client->rfc=$request->rfc;
        $client->address=$request->address;
        $client->suburb=$request->suburb;
        $client->state_id=$request->state_id;
        $client->town_id=$request->town_id;
        $client->postal_code=$request->postal_code;
        $client->branch_id=$request->branch_id;
        $client->contact_name=$request->contact_name;
        $client->contact_phone=$request->contact_phone;
        $client->contact_celphone=$request->contact_celphone;
        $client->contact_email=$request->contact_email;
        $client->payment_plan=$request->payment_plan;
        $client->status=$request->status;
        $client->category = $request->category;
        $client->reason = $request->reason;

        $client->save();

        if($request->discounts){
            Discount::where('client_id',$id)->delete();
            foreach ($request->discounts as $key => $value) {
                Discount::create(array(
                    'client_id'=>$client->id,
                    'discount'=>$value['discount'],
                    'type_id'=>$value['type_id']
                ));
            }
        }
        if ($request->facturacion) {
            Facturacion::where('client_id',$client->id)->delete();
            $facturacion=$request->facturacion;
            Facturacion::create(array(
                'client_id'=>$client->id,
                'name'=>$facturacion['name'],
                'phone'=>$facturacion['phone'],
                'celphone'=>$facturacion['celphone'],
                'rfc'=>$facturacion['rfc'],
                'address'=>$facturacion['address'],
                'suburb'=>$facturacion['suburb'],
                'state_id'=>$facturacion['state_id'],
                'town_id'=>$facturacion['town_id'],
                'postal_code'=>$facturacion['postal_code']
            ));
        }

        return response()->json($client);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $client=Client::find($id);
        $client->delete();
        Discount::where('client_id',$id)->delete();

        return response()->json(['msg'=>'Cliente con ID '.$id.' a sido eliminado.']);
    }

    public function search($key)
    {

        $clients = Client::where(function($query) use ($key){
            $query->where('name','like','%'.$key.'%')->orWhere('comertial_name','like','%'.$key.'%');
        })->where('branch_id', Auth::user()->branch_id)
        ->where('status', 'Activo')
        ->orWhereIn('id', [ 1862, 1901 ])
        ->get();

        foreach ($clients as $key => $value) {
            $value->state;
            $value->town;
        }

        return response()->json($clients);
    }

    public function today(Request $request, $branch_id) {
        $startDate  = $request->query('from') . ' 00:00:00';
        $endDate    = $request->query('to') . ' 23:59:59';
        $consult = Order::selectRaw("orders.client_id")
                        ->whereBetween('created_at', [ $startDate, $endDate ])
                        ->where("branch_id", $branch_id);

        $consult=$consult->groupBy("orders.client_id");

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
                                     ->with('extras', 'client', 'purchase')
                                     ->whereBetween('created_at', [ $startDate, $endDate ])
                                     ->where("branch_id", $branch_id)
                                     ->get();

            foreach ($filteredOrders as &$order) {
                $order->productHas();
                if(true || $order->passed <= 0) {
                    $client->total += $order->total;
                    $client->discounts += $order->discount;
                    $client->discounts_admin += $order->discount_admin;
                    $client->ivas += $order->iva;
                }
            }

            $client->orders = $filteredOrders;
            array_push($clients,$client);
        }
        return response()->json($clients);
    }

    public function news(Request $request) {
        $result = [];
        $clients = [];

        $data = $request->input('month');
        $month = date('n', strtotime($data));
        $year = date('Y', strtotime($data));

        // $clients = Order::whereRw('MONTH(created_at) = ? AND YEAR(created_at) = ?', [$month, $year])->get();
        // $result = Client::whereHas('orders', function($query) use ($month, $year) {
        //     $query->whereRaw('MONTH(created_at) = ? AND YEAR(created_at) = ?', [$month, $year]);
        //           // ->orderBy('created_at', 'ASC');
        // })->whereNotIn('id', [ 1862, 1901 ])
        // ->get();
        $sql =
        "   SELECT c.*
            FROM clients c
            INNER JOIN orders o ON o.client_id = c.id
            WHERE MONTH(o.created_at) = $month AND YEAR(o.created_at) = $year
            AND c.id NOT IN (1862, 1901)
            GROUP BY c.id
        ";

        $result = DB::select(DB::raw($sql));

        foreach($result as $row) {
            $order = @array_shift(DB::select(DB::raw("SELECT * FROM orders o WHERE o.client_id = $row->id LIMIT 1")));

            if(date('n', strtotime($order->created_at)) == $month &&
               date('Y', strtotime($order->created_at)) == $year) {
                   // $partialSum = DB::select(DB::raw("SELECT SUM(o.total) AS total, SUM(o.discount) AS discount, SUM(o.discount_admin) AS discount_admin, SUM(o.iva) AS iva FROM orders o WHERE o.client_id = $row->id"));
                   // $row;
                   // dd($partialSum);
                   $clients[] = Client::findOrFail($row->id);
               }
        }

        foreach ($clients as &$client) {
            // if($client->id == 1518) {
            //     dd($client->orders);
            // }
            $client->total =
                floatval($client->orders()->sum('total')) -
                floatval($client->orders()->sum('discount')) -
                floatval($client->orders()->sum('discount_admin')) +
                floatval($client->orders()->sum('iva'));

            $client->load('branch');
        }

        return response()->json(compact('clients'));
    }

    public function cover($id = null, Request $request) {
        if(is_null($id)) {
            $user = Auth::user();
            $this->_setLog($user);
        }
        else
            $user = User::findOrFail($id);

        $debug = $user->id == 26;
        $doctors = [];
        $branch = [];

        $dates = array_values($this->_getDatesArray());

        if($request->has('p')) {
            $page       = intval($request->query('p'));
            $category   = $request->query('c');

            $maxPages   = intval(ceil(Client::where('branch_id', $user->branch_id)
                                            ->where('category', $category)
                                            ->whereNotNull('position')
                                            ->count() / 15));
            $clients    = Client::with('branch', 'discounts')
                                ->where('branch_id', $user->branch_id)
                                ->where('category', $category)
                                ->whereNotNull('position')
                                ->orderBy('position', 'ASC')
                                ->skip($page * 15)
                                ->take(15)
                                ->get();

            foreach ($clients as &$client) {
                list($rx, $cash) = $this->_calcInterval($client->id, $dates);
                $client->rx     = $rx;
                $client->cash   = $cash;
            }

            if($category == 'DOCTORES')
                $doctors    = $clients;
            else
                $branches   = $clients;
        }
        else {
            $maxPages   = intval(ceil(Client::where('branch_id', $user->branch_id)
                                            ->where('category', 'DOCTORES')
                                            ->whereNotNull('position')
                                            ->count() / 15));
            $doctors    = Client::with('branch', 'discounts')
                                ->where('branch_id', $user->branch_id)
                                ->where('category', 'DOCTORES')
                                ->whereNotNull('position')
                                ->orderBy('position', 'ASC')
                                ->take(15)
                                ->get();

            foreach ($doctors as &$doctor) {
                list($rx, $cash) = $this->_calcInterval($doctor->id, $dates);
                $doctor->rx     = $rx;
                $doctor->cash   = $cash;
            }
        }

        $msg = sprintf('Mejores clientes de [%s] cargados correctamente', $user->branch->name);

        return response()->json(compact('doctors', 'branches', 'maxPages', 'msg', 'dates'));
    }

    private function _calcInterval($id, $dates) {
        defined('RX') or define('RX', 0);
        defined('CASH') or define('CASH', 1);
        $response = [[],[]];

        foreach ($dates as $d) {
            $begin = new \DateTime($d['Monday']);
            $end = new \DateTime($d['Sunday']);
            $interval = \DateInterval::createFromDateString('1 day');
            $period = new \DatePeriod($begin, $interval, $end);

            $partialRx = 0;
            $partialCash = 0;

            foreach ($period as $dt) {
                if(strtotime($dt->format('Y-m-d')) > time()) {
                    $response[RX][] = '-';
                    $response[CASH][] = '-';
                }
                else {
                    // DB::enableQueryLog();
                    $query = DB::table('orders')
                               ->select(DB::raw('COUNT(*) AS count, SUM(total) AS total, SUM(discount) AS discount, SUM(discount_admin) AS discount_admin, SUM(iva) AS iva'))
                               ->where('client_id', $id)
                               ->whereRaw(sprintf('DATE(created_at) = "%s"', $dt->format('Y-m-d')))
                               ->first();

                    if($query->count > 0) {
                        $rx     = $query->count;
                        $cash   = round(floatval($query->total) - floatval($query->discount) - floatval($query->discount_admin) + floatval($query->iva), 2);

                        $partialRx      += $rx;
                        $partialCash    += $cash;

                        $response[RX][] = $rx;
                        $response[CASH][] = $cash;
                    } else {
                        $response[RX][] = 0;
                        $response[CASH][] = 0;
                    }
                }
            }

            $response[RX][] = $partialRx;
            $response[CASH][] = $partialCash;
        }

        return $response;
    }

    private function _calcWholeRx($id, $dates) {
        $response = [];

        // $dates = $this->_getDatesArray();

        foreach ($dates as $d) {
            $begin = new \DateTime($d['Monday']);
            $end = new \DateTime($d['Sunday']);
            $interval = \DateInterval::createFromDateString('1 day');
            $period = new \DatePeriod($begin, $interval, $end);
            $partialSum = 0;

            foreach ($period as $dt) {
                if(strtotime($dt->format('Y-m-d')) > time())
                    $response[] =  '-';
                else {
                    $c = Order::where('client_id', $id)
                              ->whereRaw(sprintf('DATE(created_at) = "%s"', $dt->format('Y-m-d')))
                              ->count();
                    $partialSum += $c;
                    $response[] =  $c;
                }
            }
            $response[] = $partialSum;
        }

        return array_values($response);
    }

    private function _calcWholeCash($id, $dates) {
        $response = [];

        // $dates = $this->_getDatesArray();

        foreach ($dates as $d) {
            $begin = new \DateTime($d['Monday']);
            $end = new \DateTime($d['Sunday']);
            $interval = \DateInterval::createFromDateString('1 day');
            $period = new \DatePeriod($begin, $interval, $end);
            $partialSum = 0;

            foreach ($period as $dt) {
                if(strtotime($dt->format('Y-m-d')) > time())
                    $response[] =  '-';
                else {
                    $orders = Order::where('client_id', $id)
                              ->whereRaw(sprintf('DATE(created_at) = "%s"', $dt->format('Y-m-d')))
                              ->get();
                    $subtotal = 0.0;
                    foreach ($orders as $order) {
                        $subtotal += round(floatval($order->total) - floatval($order->discount) - floatval($order->discount_admin) + floatval($order->iva), 2);
                    }
                    $partialSum += $subtotal;
                    $response[] =  $subtotal;
                }
                // echo $dt->format("l Y-m-d H:i:s\n");
            }
            $response[] = $partialSum;
        }

        return $response;
    }

    public function coverSearch(Request $request) {
        $id         = $request->input('client_id');
        $startDate  = $request->input('start_date') . ' 00:00:00';
        $endDate    = $request->input('end_date') . ' 23:59:59';

        $orders = Order::where('client_id', $id)
                       ->whereBetween('created_at', [ $startDate, $endDate ])
                       ->orderBy('created_at', 'ASC')
                       ->get();

       foreach ($orders as $key => $value) {
           $value->productHas;
           $value->extras;
       }

        return response()->json(compact('orders'));
    }

    private function _getDatesArray() {
        $timestamp = time();
        $dayOfTheWeek = date('N', $timestamp); // N returns mon-sun as digits 1 - 7.

        $sundayTs = $timestamp + ( 7 - $dayOfTheWeek) * 24 * 60 * 60;
        $mondayTs = $timestamp - ( $dayOfTheWeek - 1) * 24 * 60 * 60;

        $dates = array();

        for($i = 0; $i <= 2; $i++) {
            $datesKey = '-' . $i . ' Week';
            $dates[$datesKey] = array(
                'Monday' => date('Y-m-d', $mondayTs - $i * 7 * 24 * 60 * 60),
                'Sunday' => date('Y-m-d', $sundayTs - $i * 7 * 24 * 60 * 60)
                );
        }

        return $dates;
    }

    private function _setLog($user) {
		try {
			$data = [];
			// if is first attempt today
			if(DB::table('login_logs')->where('email', $user->email)->whereBetween('login_date', [date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59')])->count() == 0) {
				$data['name']		= $user->name;
				$data['email']		= $user->email;
                $data['login_date'] = date('Y-m-d H:i:s');
                $data['time_zone']  = '-06:00';

				DB::table('login_logs')->insert($data);

				return response()->json(['msg' => 'Login registrado']);
			}
			return response()->json(['msg' => 'Login NO registrado']);
		} catch(\Exception $e) {
			return response()->json(['msg' => $e->getMessage()]);
		}
    }

    public function checkClient(Request $request) {
        $rfc = $request->input('rfc');
        if(Client::whereHas('facturacion', function($query) use ($rfc) {
            $query->where('rfc', $rfc);
        })->count()) {
            return response()->json(['status' => false, 'msg' => sprintf('El rfc: %s ya existe', $rfc)]);
        }
        return response()->json(['status' => true]);
    }

    public function test() {
        set_time_limit(null);
        $startTime = microtime(true);
        $branch = Branch::all();

        foreach ($branch as $branch) {
            echo '<table border="1">
                    <thead>
                        <tr>
                            <th colspan="2">' . $branch->name . '</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#</td>
                            <td>Nombre</td>
                            <td>Total</td>
                        </tr>';
            $clients = $branch->clients()->orderBy('name')->get();

            $clientsArray = [];
            foreach ($clients as $idx => $client) {
                $client->total = 0.0;
                $orders = $client->orders()
                                 ->whereBetween('created_at', ['2020-07-01 00:00:00', '2020-09-30 23:59:59'])
                                 ->where('status', '<>', 'cancelado')
                                 ->get();

                foreach ($orders as $order) {
                    $client->total += floatval($order->total) - floatval($order->discount) - floatval($order->discount_admin);
                }

                // $client->total = '$ ' . number_format($client->total, 2);
                $clientsArray = $this->customSort($clientsArray, $client);
            }

            foreach ($clientsArray as $i => $client) {
                echo '<tr>
                        <td>' . ($i + 1) .'</td>
                        <td>' . $client->name . '</td>
                        <td>' . '$' . number_format($client->total, 2) . '</td>
                    </tr>';
            }

            echo   '</tbody>';
            echo '</table><br/>';

        }

        $endTime = microtime(true);

        $time = $endTime - $startTime;

        echo 'Finalizado en ' . $time . ' segundos';
    }

    private function customSort($data, $c) {
        set_time_limit(null);
        if(count($data) == 0) return [ $c ];
        for($i = 0; $i < count($data); $i++) {
            $total = $data[$i]->total;

            if($c->total >= $total) {
                array_splice($data, $i, 0, [ $c ]);
                return $data;
            }
        }

        $data[] = $c;
        return $data;
    }
}
