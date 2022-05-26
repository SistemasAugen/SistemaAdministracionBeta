<?php

use Illuminate\Http\Request;

require('basics.php');

Route::group(['prefix' => 'v1'], function() {
	Route::get('orders', 'OrdersController@screen');
});

//Grupo de rutas que requieren autentificacion
Route::middleware(["jwt.auth"])->group(function() {
	Route::get('/users/excecutive', 'UsersController@excecutiveUsers');

	Route::post('/login_log', 'HomeController@setLog');
	Route::post('/logs', 'HomeController@getLogs');
	//Laboratorios
	Route::get('/laboratories',"LaboratoriesController@index")->middleware('auth.perm:laboratorios');
	Route::get('/laboratory/{id}',"LaboratoriesController@show")->middleware('auth.perm:laboratorios');
	Route::post('/laboratory',"LaboratoriesController@store")->middleware('auth.perm:editar_laboratorios');
	Route::post('/laboratory/{id}',"LaboratoriesController@update")->middleware('auth.perm:editar_laboratorios');
	Route::delete('/laboratory/{id}',"LaboratoriesController@destroy")->middleware('auth.perm:editar_laboratorios');
	//Sucursales
	Route::get('/branches',"BranchesController@index")->middleware('auth.perm:sucursales');
	Route::get('/branches/today',"BranchesController@today")->middleware('auth.perm:cobranza_admin');
	Route::get('/branch/{id}',"BranchesController@show")->middleware('auth.perm:sucursales');
	Route::post('/branch',"BranchesController@store")->middleware('auth.perm:editar_sucursales');
	Route::post('/branch/{id}',"BranchesController@update")->middleware('auth.perm:editar_sucursales');
	Route::delete('/branch/{id}',"BranchesController@destroy")->middleware('auth.perm:editar_sucursales');
	//Tipos
	Route::get('/types',"TypesController@index")->middleware('auth.perm:ver_catalogo');
	Route::get('/type/{id}',"TypesController@show")->middleware('auth.perm:ver_catalogo');
	Route::post('/type',"TypesController@store")->middleware('auth.perm:ver_catalogo');
	Route::post('/type/{id}',"TypesController@update")->middleware('auth.perm:ver_catalogo');
	Route::delete('/type/{id}',"TypesController@destroy")->middleware('auth.perm:ver_catalogo');
	//Categorias
	Route::get('/categories',"CategoriesController@index")->middleware('auth.perm:ver_catalogo');
	Route::get('/category/{id}',"CategoriesController@show")->middleware('auth.perm:ver_catalogo');
	Route::post('/category',"CategoriesController@store")->middleware('auth.perm:editar_catalogo');
	Route::post('/category/{id}',"CategoriesController@update")->middleware('auth.perm:editar_catalogo');
	Route::delete('/category/{id}',"CategoriesController@destroy")->middleware('auth.perm:editar_catalogo');
	//Subcategorias
	Route::get('/subcategories',"SubcategoriesController@index")->middleware('auth.perm:ver_catalogo');
	Route::get('/subcategory/{id}',"SubcategoriesController@show")->middleware('auth.perm:ver_catalogo');
	Route::post('/subcategory',"SubcategoriesController@store")->middleware('auth.perm:editar_catalogo');
	Route::post('/subcategory/{id}',"SubcategoriesController@update")->middleware('auth.perm:editar_catalogo');
	Route::delete('/subcategory/{id}',"SubcategoriesController@destroy")->middleware('auth.perm:editar_catalogo');

	//Productos
	Route::get('/products',"ProductsController@index")->middleware('auth.perm:ver_catalogo');
	Route::get('/product/{id}',"ProductsController@show")->middleware('auth.perm:ver_catalogo');
	Route::post('/product',"ProductsController@store")->middleware('auth.perm:editar_catalogo');
	Route::post('/product/{id}',"ProductsController@update")->middleware('auth.perm:editar_catalogo');
	Route::delete('/product/{id}',"ProductsController@destroy")->middleware('auth.perm:editar_catalogo');

	//Extras
	Route::get('/extras',"ExtrasController@index")->middleware('auth.perm:ver_catalogo');
	Route::get('/extra/{id}',"ExtrasController@show")->middleware('auth.perm:ver_catalogo');
	Route::post('/extra',"ExtrasController@store")->middleware('auth.perm:editar_catalogo');
	Route::post('/extra/{id}',"ExtrasController@update")->middleware('auth.perm:editar_catalogo');
	Route::delete('/extra/{id}',"ExtrasController@destroy")->middleware('auth.perm:editar_catalogo');

	//Clientes
	Route::get('/clients/cover', 'ClientsController@cover');
	Route::get('/clients/cover/{id}', 'ClientsController@cover');
	Route::post('/clients/cover', 'ClientsController@coverSearch');

	Route::post('/clients/news', 'ClientsController@news')->middleware('auth.perm:buscar_clientes');
	Route::post('/clients/client_check', 'ClientsController@checkClient')->middleware('auth.perm:buscar_clientes');
	Route::get('/clients',"ClientsController@index")->middleware('auth.perm:clientes');
	Route::get('/clients/today/{id}',"ClientsController@today");//->middleware(['auth.perm:cobranza_admin', 'auth.perm:buscar_pedidos']);
	Route::get('/client/{id}',"ClientsController@show")->middleware('auth.perm:clientes');
	Route::post('/client',"ClientsController@store")->middleware('auth.perm:clientes');
	Route::post('/client/{id}',"ClientsController@update")->middleware('auth.perm:clientes');
	Route::delete('/client/{id}',"ClientsController@destroy")->middleware('auth.perm:clientes');
	Route::get('/client/search/{key}',"ClientsController@search")->middleware('auth.perm:buscar_clientes');

	Route::get('/subcategories/type/{type_id}',"SubcategoriesHasTypesController@byType")->middleware('auth.perm:ver_catalogo');
	Route::get('/product/type/{type_id}',"ProductsController@type")->middleware('auth.perm:ver_catalogo');

	Route::post('/order',"OrdersController@store")->middleware('auth.perm:editar_pedido');
	Route::get('/order/search/{rx?}',"OrdersController@search")->middleware('auth.perm:buscar_pedidos');
	Route::get('/order/client/{id}',"OrdersController@client")->middleware('auth.perm:buscar_pedidos');

	Route::get('/orders/check_duplicates/{rx}', 'OrdersController@checkDuplicate');
	Route::get('/orders/today/{branch_id?}',"OrdersController@today")->middleware('auth.perm:buscar_pedidos');
	Route::get('/orders/pending',"OrdersController@laboratory")->middleware('auth.perm:pedidos_laboratorio');
	Route::post('/orders/status/{id}',"OrdersController@changeStatus")->middleware('auth.perm:cambiar_status_rx');
	Route::post('/orders/laboratory/{id}',"OrdersController@changeLaboratory")->middleware('auth.perm:pedidos_laboratorio');
	Route::post('/orders',"OrdersController@pendings")->middleware('auth.perm:ver_pedidos');
	Route::post('/orders/discount/{id}',"OrdersController@discount")->middleware('auth.perm:ver_pedidos');
	Route::post('/orders/payments/{branch_id?}',"OrdersController@payments")->middleware('auth.perm:cobranza');
	Route::get('/orders/payments_pdv/{branch_id}',"OrdersController@paymentsPDV")->middleware('auth.perm:cobranza');
	Route::get('/orders/client/{client_id}/{branch_id?}',"OrdersController@ofClient")->middleware('auth.perm:cobranza');
	Route::get('/orders/branch/{branch_id}',"OrdersController@ofBranch")->middleware('auth.perm:cobranza');
	Route::post('/orders/pay/many',"OrdersController@payMany")->middleware('auth.perm:cobranza');
	Route::post('/orders/pay/{id}',"OrdersController@pay")->middleware('auth.perm:cobranza');
	Route::post('/orders/status',"OrdersController@statusTable")->middleware('auth.perm:ver_status');

	Route::post('/orders/{id}/cancel', 'OrdersController@cancelOrder');
	Route::post('/orders/{id}/payed', 'OrdersController@partalPayOrder');

	//Reportes
	Route::post('report_search', 'ReportsController@clientsReport');
	// Route::post('/reports',"ReportsController@get")->middleware('auth.perm:reportes');

	// Promos
	Route::resource('/promos', 'PromoController');

	// Augen Mask
	Route::get('generateRx', 'OrdersController@generateRx');
});
