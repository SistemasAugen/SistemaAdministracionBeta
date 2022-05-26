<?php

use Illuminate\Database\Seeder;
use App\Menu;

class AdminMenusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        
        DB::table('menus')->insert([
            "name"=>"Sucursales",
            "description"=>"Sucursales Augen",
            "route"=>"/branches",
            "icon"=>"fas fa-warehouse",
            "permissions_name"=>"sucursales",
            "position"=>2
        ]);
        DB::table('menus')->insert([
            "name"=>"Laboratorios",
            "description"=>"Laboratorios",
            "route"=>"/laboratories",
            "icon"=>"fas fa-flask",
            "permissions_name"=>"laboratorios",
            "position"=>3
        ]);

        DB::table('menus')->insert([
            "name"=>"Catalogo",
            "description"=>"Catalogo de productos",
            "icon"=>"fas fa-clipboard",
            "permissions_name"=>"ver_catalogo",
            "position"=>4
        ]);
        $conf=Menu::where('name','Catalogo')->first();
        DB::table('menus')->insert([
            "name"=>"Productos",
            "description"=>"Productos Augen",
            "route"=>"/products",
            "parent"=>$conf->id,
            "permissions_name"=>"ver_catalogo",
            "position"=>1
        ]);
        DB::table('menus')->insert([
            "name"=>"Tipos",
            "description"=>"Tipos de lentes",
            "route"=>"/types",
            "parent"=>$conf->id,
            "permissions_name"=>"ver_catalogo",
            "position"=>2
        ]);
        DB::table('menus')->insert([
            "name"=>"Categorias",
            "description"=>"Categorias de lentes",
            "route"=>"/categories",
            "parent"=>$conf->id,
            "permissions_name"=>"ver_catalogo",
            "position"=>3
        ]);
        DB::table('menus')->insert([
            "name"=>"Subcategorias",
            "description"=>"Subcategorias de lentes",
            "route"=>"/subcategories",
            "parent"=>$conf->id,
            "permissions_name"=>"ver_catalogo",
            "position"=>4
        ]);

        DB::table('menus')->insert([
            "name"=>"Reporte",
            "description"=>"Reporte de Augen",
            "route"=>"/report",
            "icon"=>"fas fa-chart-area",
            "permissions_name"=>"reportes",
            "position"=>5
        ]);

        DB::table('menus')->insert([
            "name"=>"Clientes",
            "description"=>"Clientes de Augen",
            "route"=>"/clients",
            "icon"=>"fab fa-odnoklassniki",
            "permissions_name"=>"clientes",
            "position"=>6
        ]);

        
    }
}