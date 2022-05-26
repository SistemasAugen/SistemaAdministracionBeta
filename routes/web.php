<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::group(['prefix' => 'v1'], function() {
    Route::get('recalculate', 'OrdersController@recalcutateOrdersSinceDate');
    Route::get('promotional_mail/{id?}', 'OrdersController@sendPromotionalEmail');
    Route::get('refresh_courtesy', 'OrdersController@refreshCourtesy');
    Route::get('orders/buen_fin', 'OrdersController@buenFin');

    Route::get('dt_test', 'OrdersController@sendPromotionalEmail');

    Route::get('excel_febe', 'HomeController@excelFebe');
    Route::get('status', 'HomeController@excelStatus');

    Route::get('clientes_test', 'ClientsController@test');

    Route::get('clients_top', function() {
        // $clients = [];
        // $clients2Search = [
        //     'JAIRO ENOC RODRIGUEZ LIZONDRO',
        //     'LIVERPOOL LA PAZ 308',
        //     'MARCELO CARLOS ABBUD',
        //     'ELIZONDO HERNANDEZ CONCEPCION',
        //     'JUAN MANUEL ESPINOZA MONTES',
        //     'GRUPO OPTICO CENTURY ENSENADA',
        //     'ISAAC NOLASCO MOLINA',
        //     'ENRIQUE JUAN CORRAL ESCAREÑO',
        //     'JULIO CESAR LARA AGUILAR',
        //     'LUIS CARLOS SAENZ MEZA',
        //     'GRAJEDA MARIN JOSUE ISHBAK',
        //     'ABRAHAM POLANCO',
        //     'ROBERTO DELGADILLO',
        //     'SAMUEL LEON BEAL',
        //     'ALFONSO HERNANDEZ',
        //     'CENTURY VISION',
        //     'ALVAREZ PARRA MARIA TERESA',
        //     'MANUEL PACHECO',
        //     'RUIZ Y LOPEZ ALEJANDRO',
        //     'MIQUEAS LOPEZ RODRIGUEZ',
        //     'AYALA SANCHEZ JAVIER ALEJANDRO',
        //     'HERNANDEZ CONTRERAS MARTHA LILIA',
        //     'CLAUDIA ARREGUI',
        //     'BELINDA ROMAÑA CARRERA',
        //     'JUAN JOSE MORA LOZANO',
        //     'CARMEN ALEJANDRA CORTES MACHADO',
        //     'AIDA CONSTANCIA CORTEZ FEDERICO',
        //     'CARLOS DE LA TORRE',
        //     'FROILAN MENDOZA ULLOA',
        //     'RAMIREZ BARRETO EDUARDO MARCO ANTONIO',
        //     'CALDERON ROMERO ANA ISABEL',
        //     'BARRERA VARGAS MARCO ANTONIO',
        //     'CORTEZ ARCE PABLO',
        //     'NAZARIO MONGE LOPEZ',
        //     'PATRICIA EUGENIA TORRES VELASCO',
        //     'CARLOS PEREZ',
        //     'FRIAS SALGADO VICTOR CHRISTIAN',
        //     'Alonso Hernandez Olachea',
        //     'CLIENTE MOSTRADOR',
        //     'MARTIN RAMIREZ GARCIA',
        //     'JESUS RODOLFO JIMENEZ CESEÑA',
        //     'MENDOZA UNZON CARLOS JUSTO',
        //     'IRMA SIMON FELIX',
        //     'Josue vergara cacelis',
        //     'OCHOA R. MARIO ALBERTO',
        //     'QUINTERO PEREZ ROGELIO',
        //     'CORTES RASGADO FLOR DE LUZ',
        //     'SILVA MONGE MANUEL',
        //     'JOSE LAGA',
        //     'CHAVEZ MONTAÑO RAFAEL ANTONIO',
        //     'Alma Delia Lemus Ruiz',
        //     'CORTESIA ENSENADA',
        //     'BERNABE LOPEZ GARCIA',
        //     'SOTO PALMA BLANCA ROSA',
        //     'JOSE DE JESUS MEDINA ORTIZ',
        //     'HILTON MEDICAL EYE CENTER, S.C.',
        //     'BRETTS PAUL CRAIN HELEN',
        //     'Maritza Isela Salazar Castañeda',
        //     'Pedro Munguia Hernandez',
        //     'PEDRO ROBLES VELASCO'
        // ];
        //
        //
        // foreach($clients2Search as $i => $s) {
        //     $tc = App\Client::with('branch')
        //                 ->where('name', 'LIKE', '%' . trim($s) . '%')->first();
        //     if(!is_null($tc)) {
        //         $tc->position = intval($i) + 1;
        //         $tc->save();
        //         $clients[] = $tc;
        //     } else {
        //         dd($s);
        //     }
        // }
        //
        // return response()->json(['status' => true, 'msg' => sprintf('Se han modificado %s clientes', count($clients))]);
    });

    Route::get('orders_test', function() {
        // $orders = App\Order::where('status', 'entregado')
        //                    ->whereNull('delivered_date')
        //                    ->get();
        // echo '<style>table, th, td { border: 1px solid black; }</style>';
        // echo '<table>';
        // echo '<tr>';
        //     echo '<td>Rx</td>';
        //     echo '<td>Cliente</td>';
        //     echo '<td>Estado</td>';
        //     echo '<td>Laboratorio</td>';
        //     echo '<td>PDV</td>';
        //     echo '<td>Fecha de captura</td>';
        // echo '</tr>';
        // foreach ($orders as $order) {
        //     echo '<tr>';
        //         echo '<td>' . $order->rx .'</td>';
        //         echo '<td>' . $order->client->comertial_name .'</td>';
        //         echo '<td>' . $order->status .'</td>';
        //         echo '<td>' . $order->laboratory->name .'</td>';
        //         echo '<td>' . $order->branch->name .'</td>';
        //         echo '<td>' . $order->created_at .'</td>';
        //     echo '</tr>';
        // }
        // echo '</table>';
    });

    Route::get('order_rx/{id}', function($id) {
        $order = App\Order::findOrFail($id);
        dd($order->passed);
    });
    // Route::get('orders_test', function() {
    //     $order = App\Order::where('delivered_date', 'LIKE', '2019-11-27%')->where('payed', 0)->where('status', 'entregado')->first();
    //
    //     $deadlineToPay = $order->client->payment_plan;
    //     if($deadlineToPay == 0) {
    //         // Condiciones comerciales -> CONTADO
    //         dd(date('W', strtotime($order->delivered_date)), date('W'));
    //     } else {
    //         $actualWeek         = intval(date('W'));
    //         $mustBePayed        = strtotime(sprintf('+%s week', $deadlineToPay), strtotime($order->delivered_date));
    //
    //         $weekMustBePayed    = date('W', $mustBePayed);
    //
    //         if(date('Y') < date('Y', $mustBePayed)) {
    //             $weeksInThisYear    = date('W', strtotime('28th December '. date('Y')));
    //             $weekMustBePayed    = intval($weeksInThisYear) + intval($weekMustBePayed);
    //
    //         } else if(date('Y') > date('Y', $mustBePayed)) {
    //             $weeksInThisYear    = date('W', strtotime('28th December '. date('Y')));
    //             $actualWeek         = intval($weeksInThisYear) + intval($actualWeek);
    //         }
    //         $passed = $weekMustBePayed - $actualWeek;
    //         // if()
    //         dd($passed);
    //         // dd($order->passed);
    //         // dd(, date('W'));
    //     }
    //     // dd($order->delivered_date, $order->client->payment_plan);
    //     // dd()
    //     // $passed = $order-
    //     // dd($passed);
    // });
});
// Route::get('/','HomeController@home');
Route::get('/generate/debtors/{id?}','HomeController@debtors');
Route::get('/generate/account/{id}','HomeController@account');
Route::get('/generate/account/{id}/client','HomeController@accountClient');
Route::get('/generate/income','HomeController@income');
Route::get('/generate/pdf','HomeController@pdf');
// Route::get('/generate/pdf','HomeController@pdf');
Route::get('/img/{key}','MediaController@getImage');
Route::get('/docs/{key}','MediaController@getDocument');

//  Let's encrypt patch
Route::get('.well-known/acme-challenge/{hash}', function ($hash) {
    return 'qApnPUDh5u9hd-Y9fDSnTV4FiirC_NQN8WTDMYkxkFs.tORqj4E9dRXhNeUKWKTI0BcVYOPtlegmRh_X9iwJ8SM';
});

Route::get('/{path?}/','HomeController@admin')->where('path', '.*');
