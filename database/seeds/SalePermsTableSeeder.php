<?php

use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SalePermsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()['cache']->forget('spatie.permission.cache');
        Permission::create(['name'=>'editar_pedido']);
        Permission::create(['name'=>'ver_pedido']);
        Permission::create(['name'=>'buscar_clientes']);
        Permission::create(['name'=>'buscar_pedidos']);
        Permission::create(['name'=>'ver_cobros']);

        
        $admins = Role::where('name','vendedor')->first();
        $admins->givePermissionTo('sucursales');
        $admins->givePermissionTo('laboratorios');
        $admins->givePermissionTo('ver_catalogo');
        $admins->givePermissionTo('clientes');
        $admins->givePermissionTo('ver_pedido');
        $admins->givePermissionTo('editar_pedido');
        $admins->givePermissionTo('buscar_clientes');
        $admins->givePermissionTo('buscar_pedidos');
        $admins->givePermissionTo('ver_cobros');
    }
}
