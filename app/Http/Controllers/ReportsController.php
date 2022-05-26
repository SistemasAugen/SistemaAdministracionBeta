<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Branch;
use App\Order;
use App\ProductHasSubcategory;
use App\Client;
use App\User;

class ReportsController extends Controller
{

    public function get(Request $request)
    {
        $start=$request->start;
        $end=$request->end;
        $status=$request->status;
        // dd($status);
        $start=date("Y-m-d H:i:s",strtotime($start." 00:00:00"));
        $end=date("Y-m-d H:i:s",strtotime($end." 23:59:59"));
        $branchs = Branch::all();


        foreach ($branchs as $key => $value) {
            $value->clients;
            $orders = Order::where("branch_id",$value->id)->where("created_at",">=",$start)->where("created_at","<=",$end)->where(
                function($query) use ($status){
                    if($status['en_proceso']==true) $query->orWhere("status","like","en_proceso");
                    if($status['terminado']==true) $query->orWhere("status","like","terminado");
                    if($status['entregado']==true) $query->orWhere("status","like","entregado");
                    if($status['pagado']==true) $query->orWhere("status","like","pagado");
                }
            )->get();
            foreach ($orders as $k => $v) {
                $v->productHas;
                $v->extras;
                $v->client;
            }

            $value->orders=$orders;

        }

        return response()->json($branchs);
    }

    public function byBranch($branch_id)
    {

    }

    public function clientsReport(Request $request) {
        $ranges = [];
        $result = [];

        $mode       = $request->input('filters')['mode'];
        $options    = $request->input('options');
        $user_id     = $request->input('user');

        $category_id = $request->input('filters')['category'];
        $type_id     = $request->input('filters')['type'];

        $user = User::findOrFail($user_id);

        if($mode == 'daily') {
            // DAILY
            foreach ($options as $option) {
                $date = str_replace('/', '-', $option);
                $ranges[] = date('Y-m-d', strtotime($date));
            }
        } else {
            if($mode == 'weekly') {
                // WEEKLY
                foreach ($options as $option) {
                    $partialOption = explode('-', substr($option, 7));
                    $week = trim($partialOption[0]);
                    $year = trim($partialOption[1]);

                    $ranges[] = $this->_getStartAndEndDate($week, $year);
                }
            } else {
                // MONTHLY
                foreach ($options as $option) {
                    $date = date('Y-m-15', strtotime($option));
                    $ranges[] = $this->_getStartAndEndDateMonth($date);
                }
            }
        }

        $clients    = Client::where('branch_id', $user->branch_id)->get();

        foreach ($clients as $client) {
            $clientName = mb_strtoupper($client->name);
            if($mode == 'daily') {
                foreach ($ranges as $date) {
                    if($this->_isWeekend($date)) continue;
                    // First we search if we already calculated
                    $dp = DB::table('reports_log')
                            ->where('category_id', $category_id)
                            ->where('type_id', $type_id)
                            ->where('date', $date)
                            ->where('client_id', $client->id)
                            ->first();

                    if(is_null($dp)) {
                        // We did not do this before

                        $ordersId = DB::table('orders')
                                      ->where('client_id', $client->id)
                                      ->whereRaw(sprintf('DATE(created_at) = "%s"', $date))
                                      ->select('product_has_subcategory_id')
                                      ->get();
                        $producthasIds = [];
                        $orders = 0;

                        foreach ($ordersId as $item) {
                            $producthasIds[] = $item->product_has_subcategory_id;
                        }

                        if(count($producthasIds) > 0) {
                            $orders = ProductHasSubcategory::where('category_id', $category_id)
                                                           ->where('type_id', $type_id)
                                                           ->whereIn('id', $producthasIds)
                                                           ->count();

                            // Once we have the results, we store'em on logs
                            // If this date is before today
                            if (strtotime($date) < strtotime(date('Y-m-d 00:00:00'))) {
                                DB::table('reports_log')
                                  ->insert([
                                      'client_id'   => $client->id,
                                      'category_id' => $category_id,
                                      'type_id'     => $type_id,
                                      'orders'      => $orders,
                                      'date'        => $date
                                  ]);
                            }

                            if($orders > 0) {
                                if(!isset($result[$clientName]))
                                    $result[$clientName] = [];

                                $result[$clientName][date('d/m/Y', strtotime($date))] = $orders;
                            }
                        } else {
                            if (strtotime($date) < strtotime(date('Y-m-d 00:00:00'))) {
                                DB::table('reports_log')
                                  ->insert([
                                      'client_id'   => $client->id,
                                      'category_id' => $category_id,
                                      'type_id'     => $type_id,
                                      'orders'      => 0,
                                      'date'        => $date
                                  ]);
                              }
                        }
                    } else {
                        // We found a record

                        if($dp->orders > 0) {
                            if(!isset($result[$client->name]))
                                $result[$client->name] = [];

                            $result[$client->name][date('d/m/Y', strtotime($date))] = $dp->orders;
                        }
                    }
                }
            } else {
                foreach ($options as $idx => $option) {
                    $range = $ranges[$idx];
                    $begin = new \DateTime($range['start_date']);
                    $end = new \DateTime($range['end_date']);
                    $interval = \DateInterval::createFromDateString('1 day');
                    $period = new \DatePeriod($begin, $interval, $end);

                    foreach ($period as $dt) {
                        $date = $dt->format('Y-m-d');
                        if($this->_isWeekend($date)) continue;

                        $dp = DB::table('reports_log')
                                ->where('category_id', $category_id)
                                ->where('type_id', $type_id)
                                ->where('date', $date)
                                ->where('client_id', $client->id)
                                ->first();

                        if(is_null($dp)) {
                            // We did not do this before

                            $ordersId = DB::table('orders')
                                          ->where('client_id', $client->id)
                                          ->whereRaw(sprintf('DATE(created_at) = "%s"', $date))
                                          ->select('product_has_subcategory_id')
                                          ->get();
                            $producthasIds = [];
                            $orders = 0;

                            foreach ($ordersId as $item) {
                                $producthasIds[] = $item->product_has_subcategory_id;
                            }

                            if(count($producthasIds) > 0) {
                                $orders = ProductHasSubcategory::where('category_id', $category_id)
                                                               ->where('type_id', $type_id)
                                                               ->whereIn('id', $producthasIds)
                                                               ->count();

                                // Once we have the results, we store'em on logs
                                // If this date is before today
                                if (strtotime($date) < strtotime(date('Y-m-d 00:00:00'))) {
                                    DB::table('reports_log')
                                      ->insert([
                                          'client_id'   => $client->id,
                                          'category_id' => $category_id,
                                          'type_id'     => $type_id,
                                          'orders'      => $orders,
                                          'date'        => $date
                                      ]);
                                }

                                if($orders > 0) {
                                    if(!isset($result[$clientName]))
                                        $result[$clientName] = [];

                                    if(isset($result[$client->name][$option]))
                                        $result[$client->name][$option] += $orders;
                                    else
                                        $result[$client->name][$option] = $orders;
                                }
                            } else {
                                if (strtotime($date) < strtotime(date('Y-m-d 00:00:00'))) {
                                    DB::table('reports_log')
                                      ->insert([
                                          'client_id'   => $client->id,
                                          'category_id' => $category_id,
                                          'type_id'     => $type_id,
                                          'orders'      => 0,
                                          'date'        => $date
                                      ]);
                                  }
                            }
                        } else {
                            // We found a record

                            if($dp->orders > 0) {
                                if(!isset($result[$client->name]))
                                    $result[$client->name] = [];

                                if(isset($result[$client->name][$option]))
                                    $result[$client->name][$option] += $dp->orders;
                                else
                                    $result[$client->name][$option] = $dp->orders;
                            }
                        }
                    }
                }
            }
        }

        return response()->json(compact('result'));
    }

    private function _getStartAndEndDate($week, $year) {
        $result = [];
        $dateTime = new \DateTime();
        $dateTime->setISODate($year, $week);
        $result['start_date'] = $dateTime->format('Y-m-d');
        $dateTime->modify('+6 days');
        $result['end_date'] = $dateTime->format('Y-m-d');
        return $result;
    }

    private function _getStartAndEndDateMonth($date) {
        $result = [];
        $firstDate = strtotime(date("Y-m-d", strtotime($date)) . ", first day of this month");
        $result['start_date'] = date("Y-m-d", $firstDate);

        $lastDate = strtotime(date("Y-m-d", strtotime($date)) . ", last day of this month");
        $result['end_date'] = date("Y-m-d",$lastDate);
        return $result;
    }

    private function _isWeekend($date) {
        $weekDay = date('w', strtotime($date));
        // return ($weekDay == 0 || $weekDay == 6);
        return $weekDay == 0;
    }
}
