<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UsersSeeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$customer= new Role();
    	$customer->name = "customer";
    	$customer->save();
    	$employee= new Role();
    	$employee->name = "employee";
    	$employee->save();
    	$manager= new Role();
    	$manager->name = "manager";
    	$manager->save();
    	$admin_role= new Role();
    	$admin_role->name = "admin";
    	$admin_role->save();
       $admin= new User();
        $admin->first_name="Super";
        $admin->last_name="Admin";
        $admin->email="superadmin@softnterpris";
        $admin->password= Hash::make('admin@soft');
        $admin->user_type="1";
        $admin->save();
        $admin->assignRole($admin_role);
    }
}
