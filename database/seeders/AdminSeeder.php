<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role; 
use App\Models\{User,Country};
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->PermissionSeeder();
        $this->AdminSeeder(); 
        // $this->VenueCity(); 
        
 
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
        $roles = [ 
            'therapist',
            // 'patient', 
            'site-admin', 
            // 'visitor'
        ];
        $this->CraeteAllUsers($roles);
        $this->InsertCountryData(); 
        $this->InsertTimeZone(); 

   
    }

    public function CraeteAllUsers($roles){

        foreach($roles as $role){
            $roleName =$role.' User'; 
            if($role == 'therapist'){
              $roleName =   'Qibla Syed Sarfraz Ahmed Shah Sahab'; 	
            }
            if($role == 'site-admin'){
                $roleName =   'Ahmad Ali'; 	
            }

            $user = User::create([
                'name' => $roleName, 
                'email' => $role.'@gmail.com',
                'password' => bcrypt('123456'), 
                'email_verified_at' => date('Y-m-d H:i:s'),
            ]);
            $role = Role::create(['name' => $role]);
         
            $permissions = Permission::pluck('id','id')->all();
       
            $role->syncPermissions($permissions);
         
            $user->assignRole([$role->id]);
            
        }
        
    }


    public function PermissionSeeder(){
        $permissions =[
            'user-management-access',
            'visitor-access',
            'venue-access',
            'queue-management-access',
            'vedio-call-access',
            'vistor-schduling-access',
            'site-admin-access',
            'visitor-booking-access'
        ];
     
        foreach ($permissions as $permission) {
             Permission::create(['name' => $permission]);
        }
    }


    public function InsertCountryData(){
        $sqlDumpPath = __DIR__ . '/countries.sql';
        $sql = file_get_contents($sqlDumpPath);
        DB::unprepared($sql); 
    }

    // public function InsertCountryData(){
    //     $sqlDumpPath = __DIR__ . '/all_countries_with_state_city.sql';
    //     $sql = file_get_contents($sqlDumpPath);
     
    //      DB::unprepared($sql); 
    // }
    public function VenueCity(){
        
        Artisan::call('migrate', [
            '--path' => 'database/migrations/other/2023_12_01_040239_create_venue_state_cities_table.php',
        ]);
         
    }  
    public function InsertTimeZone(){
        $sqlDumpPath = __DIR__ . '/timezone.sql';
        $sql = file_get_contents($sqlDumpPath);
        DB::unprepared($sql); 
    }

    
}
