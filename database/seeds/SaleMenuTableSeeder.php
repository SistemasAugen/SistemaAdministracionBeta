<?php

use Illuminate\Database\Seeder;

class SaleMenuTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('menus')->insert([
            "name"=>"Buscar",
            "description"=>"Buscar pedidos",
            "route"=>"/search",
            "icon"=>"fas fa-search",
            "permissions_name"=>"buscar_pedidos",
            "position"=>1
        ]);
        DB::table('menus')->insert([
            "name"=>"Nuevo Pedido",
            "description"=>"Crear un nuevo pedido",
            "route"=>"/new_order",
            "icon"=>"far fa-plus-square",
            "permissions_name"=>"editar_pedido",
            "position"=>2
        ]);
        DB::table('menus')->insert([
            "name"=>"Ver catalogo",
            "description"=>"Ver todos los productos",
            "route"=>"/catalog",
            "icon"=>"fas fa-clipboard-list",
            "permissions_name"=>"ver_catalogo",
            "position"=>3
        ]);
        DB::table('menus')->insert([
            "name"=>"Ventas",
            "description"=>"Ventas del dia",
            "route"=>"/sales",
            "icon"=>"fas fa-hand-holding-usd",
            "permissions_name"=>"ver_cobros",
            "position"=>4
        ]);
    }
}
