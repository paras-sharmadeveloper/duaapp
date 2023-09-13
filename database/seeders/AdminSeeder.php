<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role; 
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->PermissionSeeder();
        $this->AdminSeeder(); 
 
    }


    public function AdminSeeder(){
       $user = User::create([
            'name' => 'Admin User', 
            'email' => 'admin@gmail.com',
            'password' => bcrypt('123456'), 
            'email_verified_at' => date('Y-m-d H:i:s'),
        ]);
        $role = Role::create(['name' => 'admin']);
     
        $permissions = Permission::pluck('id','id')->all();
   
        $role->syncPermissions($permissions);
     
        $user->assignRole([$role->id]);
        // $roles = ['admin', 'therapist', 'patient', 'site-admin', 'visitor'];
// 
    
    }


    public function PermissionSeeder(){
        $permissions =[
            'user-management-access',
            'visitor-access',
            'venue-access',
            'queue-management-access',
            'vedio-call-access',
            'vistor-schduling-access',
        ];
     
        foreach ($permissions as $permission) {
             Permission::create(['name' => $permission]);
        }
    }
}
