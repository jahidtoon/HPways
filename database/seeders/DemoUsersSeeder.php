<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name'=>'Demo Applicant','email'=>'applicant@example.com','username'=>'demo_applicant','password'=>Hash::make('password')],
            ['name'=>'Demo Case Manager','email'=>'case@example.com','username'=>'demo_case','password'=>Hash::make('password')],
        ];
        foreach($users as $u){
            $defaults = $u;
            $user = User::firstOrCreate(['email'=>$u['email']],$defaults);
            try {
                if(str_contains($u['email'],'case')){ $user->assignRole('case_manager'); }
                else { $user->assignRole('applicant'); }
            } catch(\Throwable $e) { /* ignore role errors */ }
        }
    }
}
