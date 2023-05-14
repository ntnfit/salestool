<?php
  
namespace Database\Seeders;
  
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
  
class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
           'role-list',
           'role-create',
           'role-edit',
           'role-delete',
          'sales-module',
          'sales-manager',
          'purchase-module',
          'purchase-manager',
          'inventory-module',
          'inventory-manager',
          'users-list',
          'users-create',
          'users-delete',
          'users-edit',
          'logistic-module',
          'logistic-manager',
          'marketing-module',
          'marketing-manager',
        ];
     
        foreach ($permissions as $permission) {
             Permission::create(['name' => $permission]);
        }
    }
}