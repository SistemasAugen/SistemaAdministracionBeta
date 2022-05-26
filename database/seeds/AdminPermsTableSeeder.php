<?php

use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminPermsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()['cache']->forget('spatie.permission.cache');
        Permission::create(['name' => 'sucursales']);
        Permission::create(['name' => 'laboratorios']);
        Permission::create(['name' => 'ver_catalogo']);
        Permission::create(['name' => 'editar_catalogo']);
        Permission::create(['name' => 'modificar_catalogo']);
        Permission::create(['name' => 'reportes']);
        Permission::create(['name' => 'clientes']);

        
        $admins = Role::where('name','administrador')->first();
        $admins->givePermissionTo('sucursales');
        $admins->givePermissionTo('laboratorios');
        $admins->givePermissionTo('ver_catalogo');
        $admins->givePermissionTo('editar_catalogo');
        $admins->givePermissionTo('modificar_catalogo');
        $admins->givePermissionTo('reportes');
        $admins->givePermissionTo('clientes');
        
    }
}
