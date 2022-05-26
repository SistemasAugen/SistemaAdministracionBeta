<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Exports\BalanceExport;
use App\Exports\DebtorsExport;
use App\Exports\ClientsExport;
use App\Exports\StatusExport;
use App\Branch;
use App\Order;
use App\Client;
use App\User;
use Zipper;
use Excel;
use PDF;

/**
*
*/
class HomeController extends Controller
{
	public static $unwantedArray = array(    'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
                            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
                            'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
                            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
                            'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y',
                            '&#225;'=>'a', '&#233;'=>'e', '&#237;'=>'i', '&#243;'=>'o', '&#250;'=>'u',
                            '&#193;'=>'A', '&#201;'=>'E', '&#205;'=>'I', '&#211;'=>'O', '&#218;'=>'U',
                            '&#209;'=>'N', '&#241;'=>'n', '.' => '', ' ' => '_');
	public function home(){
		return view('page');
	}

	public function admin(){
		return view('admin');
	}

	public function account($id) {
		// Clear all export folder
		foreach (glob(public_path('/documents/*')) as $file) {
			if(is_file($file)){
			   //Use the unlink function to delete the file.
			   unlink($file);
   			}
		}
		$documentstoAdd = $this->_generateReports($id);

		$headers = ["Content-Type" => "application/zip"];
		$branch = Branch::findOrFail($id);
		$branchName = $branch->name;
		$branchName = str_replace('PDV ', '', $branchName);
		$fileName = 'EdoCuenta_' . $branchName . date('Y-m-d') . '.zip';
		Zipper::make(public_path('/documents/' . $fileName))
					->add($documentstoAdd)
					->close();

		return response()->download(public_path('/documents/'.$fileName),$fileName, $headers);
	}

	private function _generateReports($id) {
		$branch = Branch::findOrFail($id);

		// Remote old existing reports
		$folder = storage_path('app/documents/branch/' . $id);
		// First we check if the folder already exists
		if(!is_dir($folder)) {
			mkdir($folder, 0777);
		}

		foreach (glob($folder . '/*') as $file) {
			if(is_file($file)){
			   //Use the unlink function to delete the file.
			   unlink($file);
   			}
		}

		foreach ($branch->clients as $client) {
			$clientName = $client->name;
			$clientName = $this->clearString($clientName);

			$fileName = $client->id . '-' . $clientName . '.xlsx';


			// $handle = fopen($folder . '/' . $fileName, 'w') or die('Cannot open file:  '. $fileName); //implicitly creates file
			// $data = DB::table('orders');
			// dd($data);
			$data = [];
			if(intval($client->payment_plan) == 0) continue;
			$payments = Order::where("status","entregado")
                            ->where("payed", false)
							->where('client_id', $client->id)
							// ->whereRaw(sprintf('ROUND(DATEDIFF(DATE(NOW()), IFNULL(delivered_date, created_at))/7, 0) <= 0'))
							->get();


            // $consult = $consult->orderByRaw('cast(rx as unsigned)');
			if(count($payments) == 0) continue;

            foreach ($payments as $order) {
				$order->productHas;
				$orderExtras = [];
				foreach ($order->extras as $extra) {
					$orderExtras[] = $extra->name;
				}

				if(isset($order->promo_discount))
					$total = floatval($order->promo_discount);
				else
					$total = floatval($order->total) - floatval($order->discount) - floatval($order->discount_admin) + floatval($order->iva);

				$data[] = [
		            'rx' => $order->rx,
		        	'fecha' => $order->created_at,
		        	'semanas_restantes' => $order->passed,
		        	'diseno' => isset($order->product['name']) ? $order->product['name'] : 'NO DISPONIBLE',
					'material' => isset($order->product['subcategory_name']) ? $order->product['subcategory_name'] : 'NO DISPONIBLE',
					'caracteristica' => isset($order->product['type_name']) ? $order->product['type_name'] : 'NO DISPONIBLE',
		        	'antireflejante' => implode(', ', $orderExtras),
		        	'plazo' => @$order->payment_plan ?: '0',
		        	'subtotal' => $order->total,
		        	'descuentos_sistema' => $order->discount,
		        	'descuento' =>  floatval($order->discount_admin) / (in_array($order->laboratory_id, [2, 10]) ? 1.08 : 1.16),
		        	'total' => $total,
		        ];
            }

			Excel::store(new BalanceExport($data, $client), 'documents/branch/' . $id . '/' . $fileName);
			// return Excel::download(new BalanceExport($data, $client), 'test.xlsx');
		}
		return glob($folder . '/*');
	}

	private function clearString($str) {
		return strtr($str, self::$unwantedArray);
	}

	public function income(Request $request) {
		$typeOf 	= $request->query('type');
		$startDate 	= $request->query('start_date');
		$endDate 	= $request->query('end_date');

		$branches = Branch::all();

		foreach($branches as $branch) {
			// if($branch->id != 5) continue;
			$partialAmount = 0.0;

			$begin = new \DateTime($startDate);
			$end = new \DateTime($endDate);

			for($i = $begin; $i <= $end; $i->modify('+1 day')) {
				$date = $i->format("Y-m-d");

				$orders = $branch->orders()
								 ->where('status', 'pagado')
							     ->whereRaw("DATE(payment_date) = '$date'")
								 ->get();

				dd($orders);

				foreach ($orders as $order) {
					$client = $order->client;
					$weekPassed = $this->calcFrom($order, $date, $client->payment_plan);
					if($weekPassed == 0 && $client->payment_plan > 0) {
						$partialAmount += round(floatval($order->total) - floatval($order->discount) - floatval($order->discount_admin) + floatval($order->iva), 2);
						echo $client->name . ' ' . number_format($partialAmount, 2) . ' ' . $order->delivered_date  . ' ' . $client->payment_plan  .   PHP_EOL;
					}
				}
			}
		}
		die;

		if($typeOf == 'CONTADO')
			$clients = Client::where('payment_plan', 0)->orderBy('branch_id', 'ASC')->get();
		else
			$clients = Client::where('payment_plan', '>', 0)->orderBy('branch_id', 'ASC')->get();

		$data = [];
		$totalAmount = 0.0;

		foreach ($clients as $client) {
			$branchName = $client->branch->name;
			$branchName = str_replace('PDV ', '', $branchName);

			$clientId 	= $client->id;
			$clientName = $client->name;
			$partialAmount = 0.0;

			$begin = new \DateTime($startDate);
			$end = new \DateTime($endDate);

			for($i = $begin; $i <= $end; $i->modify('+1 day')) {
				$date = $i->format("Y-m-d");
				$orders = $client->orders()
								 ->where('payment_date', $date)
								 ->get();

				if(count($orders) == 0) continue;

				switch ($typeOf) {
					case 'EDO_CUENTA':
					case 'CONTADO':
						foreach ($orders as $order) {
							$weekPassed = $this->calcFrom($order, $date, $client->payment_plan);
							if($weekPassed == 0) {
								$partialAmount += round(floatval($order->total) - floatval($order->discount) - floatval($order->discount_admin) + floatval($order->iva), 2);
							}
						}
						break;
					case 'COBRANZA':
						// $query = DB::table('orders')
						// 			 ->select(DB::raw('SUM(total) AS total, SUM(discount) AS discount, SUM(discount_admin) AS discount_admin, SUM(iva) AS iva'))
						// 			 ->where('client_id', $clientId)
						// 			 ->whereRaw(sprintf('ROUND(DATEDIFF(DATE(NOW()), IFNULL(delivered_date, created_at))/7, 0) IN (-1, -2, -3)'))
						// 			 ->whereBetween('payment_date', [ $startDate, $endDate ])
						// 			 ->groupBy('client_id')
						// 			 ->first();
						if(count($orders) > 0) {
							foreach ($orders as $order) {
								if(in_array($order->passed, [-1, -2, -3])) {
									$partialAmount += round(floatval($order->total) - floatval($order->discount) - floatval($order->discount_admin) + floatval($order->iva), 2);
								}
							}
						}
						break;
					case 'VENCIDO':
						// $query = DB::table('orders')
						// 			 ->select(DB::raw('SUM(total) AS total, SUM(discount) AS discount, SUM(discount_admin) AS discount_admin, SUM(iva) AS iva'))
						// 			 ->where('client_id', $clientId)
						// 			 ->whereRaw(sprintf('ROUND(DATEDIFF(DATE(NOW()), IFNULL(delivered_date, created_at))/7, 0) <= -4'))
						// 			 ->whereBetween('payment_date', [ $startDate, $endDate ])
						// 			 ->groupBy('client_id')
						// 			 ->first();
						if(count($orders) > 0) {
							foreach ($orders as $order) {
								if($order->passed <= -4) {
									$partialAmount += round(floatval($order->total) - floatval($order->discount) - floatval($order->discount_admin) + floatval($order->iva), 2);
								}
							}
						}
						break;
					// case 'CONTADO':
					// 	// $query = DB::table('orders')
					// 	// 			 ->select(DB::raw('SUM(total) AS total, SUM(discount) AS discount, SUM(discount_admin) AS discount_admin, SUM(iva) AS iva'))
					// 	// 			 ->where('client_id', $clientId)
					// 	// 			 ->whereRaw(sprintf('ROUND(DATEDIFF(DATE(NOW()), IFNULL(delivered_date, created_at))/7, 0) = 0'))
					// 	// 			 ->whereBetween('payment_date', [ $startDate, $endDate ])
					// 	// 			 ->groupBy('client_id')
					// 	// 			 ->first();
					// 	if(count($orders) > 0) {
					// 		foreach ($orders as $order) {
					// 			if($order->passed == 0) {
					// 				$partialAmount += round(floatval($order->total) - floatval($order->discount) - floatval($order->discount_admin) + floatval($order->iva), 2);
					// 			}
					// 		}
					// 	}
					// 	break;
					case 'ANTICIPO':
						// $query = DB::table('orders')
						// 			 ->select(DB::raw('SUM(total) AS total, SUM(discount) AS discount, SUM(discount_admin) AS discount_admin, SUM(iva) AS iva'))
						// 			 ->where('client_id', $clientId)
						// 			 ->whereRaw(sprintf('ROUND(DATEDIFF(DATE(NOW()), IFNULL(delivered_date, created_at))/7, 0) > 0'))
						// 			 ->whereBetween('payment_date', [ $startDate, $endDate ])
						// 			 ->groupBy('client_id')
						// 			 ->first();
						if(count($orders) > 0) {
							foreach ($orders as $order) {
								if($order->passed > 0) {
									$partialAmount += round(floatval($order->total) - floatval($order->discount) - floatval($order->discount_admin) + floatval($order->iva), 2);
								}
							}
						}
						break;
				}

				if($partialAmount != 0.0) {
					$data[] = [
						'laboratory' 	=> $branchName,
						'client'		=> $clientName,
						'amount'		=> $partialAmount
					];

					$totalAmount += $partialAmount;
				}
				// if(!is_null($query)) {
				// 	$totalAmount = round(floatval($query->total) - floatval($query->discount) - floatval($query->discount_admin) + floatval($query->iva), 2);
				// 	if($totalAmount > 0) {
				// 		$data[] = [
				// 			'laboratory' 	=> $branchName,
				// 			'client'		=> $clientName,
				// 			'amount'		=> $totalAmount
				// 		];
				// 	}
				// }
			}
		}

		if(count($data) > 0) {
			echo '<table>';
				echo '
					<tr>
						<th>Laboratorio</th>
						<th>Clinte</th>
						<th>Cantidad</th>
					</tr>
				';
			foreach($data as $row) {
				echo '<tr>';
				echo '<td>' . $row['laboratory'] . '</td>';
				echo '<td>' . $row['client'] . '</td>';
				echo '<td>' . '$ ' . number_format($row['amount'], 2) . '</td>';
				echo '</tr>';
			}

			echo '<tfoot>
				<tr>
					<td colspan="2"> Total </td>
					<td> ' . number_format($totalAmount, 2)  . ' </td>
				</tr>
			</tfoot>';
			echo '</table>';
		}
	}

	public function debtors($branch_id = null) {
		$actualDate = date('Y-m-d');
		$numberOfWeek = date('N', strtotime($actualDate));
		$weekFirstDay = date('Y-m-d', strtotime($actualDate . " - " . ($numberOfWeek - 1) . " days"));
		$weekLastDay = date('Y-m-d', strtotime($actualDate . " + " . (7 - $numberOfWeek) . " days"));
		$isWholeClients = is_null($branch_id);

		$data = [];
		$data['EDO_CUENTA'] = [];
		$data['COBRANZA_1'] = [];
		$data['COBRANZA_2'] = [];
		$data['COBRANZA_3'] = [];
		$data['VENCIDO'] = [];
		$data['CONTADO'] = [];

		define('BASE_DATE', '2019-02-11');

		if(!$isWholeClients) {
			$branch = Branch::findOrFail($branch_id);
			$branchName = $branch->name;
			$branchName = str_replace('PDV ', '', $branchName);
			$branchs = [ $branch ];
		} else {
			$branchName = 'CONCENTRADO';
			$branchs = Branch::all();
		}

		// Now we'll use each POS BRANCH for iterate through ther customers
		foreach ($branchs as $branch) {
			// If we need headers
			if($isWholeClients) {
				$data['EDO_CUENTA'][] = str_replace('PDV ', '', $branch->name);
				$data['COBRANZA_1'][] = str_replace('PDV ', '', $branch->name);
				$data['COBRANZA_2'][] = str_replace('PDV ', '', $branch->name);
				$data['COBRANZA_3'][] = str_replace('PDV ', '', $branch->name);
				$data['VENCIDO'][] = str_replace('PDV ', '', $branch->name);
				$data['CONTADO'][] = str_replace('PDV ', '', $branch->name);
			}

			// We'll use this foreach cycle to iterate through all customers with more time
			// in ther payment_plan

			$clients = $branch->clients()
							  ->where('payment_plan', '>', 0)
						  	  ->where('category', 'DOCTORES')
						  	  ->orderBy('name', 'ASC')
							  ->get();

			foreach ($clients as $client) {
				$orders = $client->orders()
								->whereNull('payment_date')
								->whereNotNull('delivered_date')
								->where('payed', 0)
								->where('created_at', '>', BASE_DATE)
								->where('status', 'entregado')
								->get();

				if(count($orders) > 0) {
					$clientData = [];
					$clientData['EDO_CUENTA'] = 0.0;
					$clientData['COBRANZA_1'] = 0.0;
					$clientData['COBRANZA_2'] = 0.0;
					$clientData['COBRANZA_3'] = 0.0;
					$clientData['VENCIDO'] = 0.0;

					// if($client->id == 33) {
					// 	array_map(function($a) {
					// 		echo $a['passed'] . ' - ' . $a['id'] . PHP_EOL;
					// 	}, $orders->toArray());
					// 	die;
					// }

					foreach ($orders as $order) {
						$weekPassed = $order->passed;
						$partialAmount = round(floatval($order->total) - floatval($order->discount) - floatval($order->discount_admin) + floatval($order->iva), 2);

						if($weekPassed >= 1) continue;
						if($weekPassed == 0) {
							$clientData['EDO_CUENTA'] += $partialAmount;
						} else if($weekPassed == -1) {
							$clientData['COBRANZA_1'] += $partialAmount;
						} else if($weekPassed == -2) {
							$clientData['COBRANZA_2'] += $partialAmount;
						} else if($weekPassed == -3) {
							$clientData['COBRANZA_3'] += $partialAmount;
						} else {
							$clientData['VENCIDO'] += $partialAmount;
						}
					}

					if($clientData['EDO_CUENTA'] > 0) {
						$data['EDO_CUENTA'][] = [
							'client' 	=> $client->name,
							'amount'	=> $clientData['EDO_CUENTA'],
						];
					}

					if($clientData['COBRANZA_1'] > 0) {
						$data['COBRANZA_1'][] = [
							'client' 	=> $client->name,
							'amount'	=> $clientData['COBRANZA_1'],
						];
					}

					if($clientData['COBRANZA_2'] > 0) {
						$data['COBRANZA_2'][] = [
							'client' 	=> $client->name,
							'amount'	=> $clientData['COBRANZA_2'],
						];
					}

					if($clientData['COBRANZA_3'] > 0) {
						$data['COBRANZA_3'][] = [
							'client' 	=> $client->name,
							'amount'	=> $clientData['COBRANZA_3'],
						];
					}

					if($clientData['VENCIDO'] > 0) {
						$data['VENCIDO'][] = [
							'client' 	=> $client->name,
							'amount'	=> $clientData['VENCIDO'],
						];
					}
				}
			}

			$clients = $branch->clients()
							  ->where('payment_plan', 0)
							  ->where('category', 'DOCTORES')
							  ->orderBy('name', 'ASC')
							  ->get();
			// And now for all customer with no payment_plan
			foreach ($clients as $client) {
				$orders = $client->orders()
								->whereNull('payment_date')
								->whereNotNull('delivered_date')
								->where('created_at', '>', BASE_DATE)
								->where('status', 'entregado')
								->get();

				if(count($orders) > 0) {
					$totalAmount = 0.0;

					foreach ($orders as $order) {
						$partialAmount = round(floatval($order->total) - floatval($order->discount) - floatval($order->discount_admin) + floatval($order->iva), 2);
						$totalAmount += $partialAmount;
					}

					if($totalAmount > 0) {
						$data['CONTADO'][] = [
							'client' 	=> $client->name,
							'amount'	=> $totalAmount,
						];
					}
				}
			}
		}

		$data4Sheet = [
			'sheets' => [
				[ $data['EDO_CUENTA'] ],
				[ $data['COBRANZA_1'], $data['COBRANZA_2'], $data['COBRANZA_3'] ],
				[ $data['VENCIDO'] ],
				[ $data['CONTADO'] ]
			],
			'range' 	=> sprintf('AL %s', $weekFirstDay),
			'branch'	=> $branchName,
		];

		return (new DebtorsExport($data4Sheet))->download(date('Y-m-d') . $branchName . '_FORMATO_DE_INGRESOS.xlsx');
	}

	public function pdf(Request $request) {
		$id         = $request->input('client_id');
		$startDate  = $request->input('start_date') . ' 00:00:00';
		$endDate    = $request->input('end_date') . ' 23:59:59';

		$orders = Order::where('client_id', $id)
					   ->whereBetween('created_at', [ $startDate, $endDate ])
					   ->orderBy('created_at', 'ASC')
					   ->get();

	   foreach ($orders as $key => &$value) {
		   $value->productHas;
		   $value->extras;

		   $value->computedTotal = !is_null($value->promo) ? $value->promo : floatval($value->total) - floatval($value->discount) - floatval($value->discount_admin) + floatval($value->iva);
	   }

	   $selectedClient = Client::findOrFail($id);
	   $selectedClient->monofocalDiscount       = $selectedClient->discounts()->where('type_id', 1)->first()->discount;
	   $selectedClient->bifocalDiscount         = $selectedClient->discounts()->where('type_id', 2)->first()->discount;
	   $selectedClient->progresiveDiscount      = $selectedClient->discounts()->where('type_id', 3)->first()->discount;
	   $selectedClient->packagesDiscount        = $selectedClient->discounts()->where('type_id', 3)->first()->discount;
	   $selectedClient->finishedDiscount        = $selectedClient->discounts()->where('type_id', 3)->first()->discount;

	   $pdf = PDF::loadView('pdf.invoice', compact('orders', 'selectedClient'));
	   return $pdf->setPaper('a4', 'landscape')->stream();
	}

	public function calcFrom(Order $order, $date, $paymentPlan) {
		$startDate  = isset($order->delivered_date) ? date_create($order->delivered_date) : date_create($order->created_at);
        @$weeks  = $paymentPlan ?: 0;
        date_add($startDate, date_interval_create_from_date_string($weeks . ' weeks'));
        $endDate    = date('Y-m-d', strtotime($date));

        $startDate = date_format($startDate, 'Y-m-d');

        $first = \DateTime::createFromFormat('Y-m-d', $startDate);
        $second = \DateTime::createFromFormat('Y-m-d', $endDate);
        if($first > $second)
            return intval(ceil($first->diff($second)->days/7));
        else
            return -intval(ceil($first->diff($second)->days/7)) + 1;
	}

	public function accountClient($id) {
		$client = Client::findOrFail($id);
		$clientName = $client->name;
		$clientName = $this->clearString($clientName);

		$fileName = $client->id . '-' . $clientName . '.xlsx';
		$data = [];

		$payments = Order::where("status","entregado")
						->where("payed", false)
						->where('client_id', $client->id)
						->get();

		foreach ($payments as $order) {
			$order->productHas;
			$orderExtras = [];
			foreach ($order->extras as $extra) {
				$orderExtras[] = $extra->name;
			}

			if(isset($order->promo_discount))
				$total = floatval($order->promo_discount);
			else
				$total = floatval($order->total) - floatval($order->discount) - floatval($order->discount_admin) + floatval($order->iva);

			$data[] = [
				'rx' => $order->rx,
				'fecha' => $order->created_at,
				'semanas_restantes' => $order->passed,
				'diseno' => isset($order->product['name']) ? $order->product['name'] : 'NO DISPONIBLE',
				'material' => isset($order->product['subcategory_name']) ? $order->product['subcategory_name'] : 'NO DISPONIBLE',
				'caracteristica' => isset($order->product['type_name']) ? $order->product['type_name'] : 'NO DISPONIBLE',
				'antireflejante' => implode(', ', $orderExtras),
				'plazo' => @$order->payment_plan ?: '0',
				'subtotal' => $order->total,
				'descuentos_sistema' => $order->discount,
				'descuento' =>  floatval($order->discount_admin) / (in_array($order->laboratory_id, [2, 10]) ? 1.08 : 1.16),
				'total' => $total,
			];
		}

		// Excel::store(new BalanceExport($data, $client), 'documents/branch/' . $id . '/' . $fileName);
		return Excel::download(new BalanceExport($data, $client), $fileName);
	}

	public function setLog(Request $request) {
		try {
			$user = Auth::user();
			$data = $request->all();
			// if is first attempt today
			if(DB::table('login_logs')->where('email', $user->email)->whereBetween('login_date', [date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59')])->count() == 0) {
				$data['name']		= $user->name;
				$data['email']		= $user->email;

				DB::table('login_logs')->insert($data);

				return response()->json(['msg' => 'Login registrado']);
			}
			return response()->json(['msg' => 'Login NO registrado']);
		} catch(\Exception $e) {
			return response()->json(['msg' => $e->getMessage()]);
		}
	}

	public function getLogs(Request $request) {
		$logs = DB::table('login_logs')->whereRaw(sprintf('DATE(login_date) = "%s"', $request->input('date')))->get();

		foreach ($logs as &$log) {
			$log->login_time = date('h:i:s a', strtotime($log->login_date));
		}

		$maxAssistance 	= User::whereHas("roles", function($q){ $q->where("name", "Ejecutivo"); })->count();

		return response()->json(compact('logs', 'maxAssistance'));
	}

	public function excelFebe() {
		$clients = Client::with('branch', 'state', 'town')
						 ->orderBy('id', 'ASC')->get();

		return Excel::download(new ClientsExport($clients), date('Ymd') . '_CLIENTES.xlsx');
	}

	public function excelStatus(Request $request) {
		$start      = $request->input('start') . ' 00:00:00';
		$end        = $request->input('end') . ' 23:59:59';

		$user = User::findOrFail($request->input('user_id'));

		if($request->has('q')) {
			$field  = $request->query('q');

			$title = '';
			switch ($request->input('q')) {
				case 'finish_date': $title = 'PROCESO_TERMINADO'; break;
				case 'delivered_date': $title = 'TERMINADO_ENTREGADO'; break;
				case 'payment_date': $title = 'ENTREGADO_PAGADO'; break;
				case 'delivered_date2': $title = 'GARANTIA_ENTREGADO'; break;
			}

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

				$value->computedTotal = !is_null($value->promo) ? $value->promo : floatval($value->total) - floatval($value->discount) - floatval($value->discount_admin) + floatval($value->iva);
			}

			foreach ($orders as $key => $value) {
				$orders[$key] = $orders[$key]->toArray();
				if(is_null($orders[$key]['client'])){
					$orders[$key] = array_merge($orders[$key], ['client'=>['name'=> 'Cliente eliminado']]);
				}
			}
			libxml_use_internal_errors(true);
			return Excel::download(new StatusExport($orders), date('Ymd') . '_' . $title  . '_STATUS.xlsx');
		} else
			die('Request cannot be processed');
	}
}

 ?>
