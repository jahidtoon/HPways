<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class StaffUsersSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name'=>'Admin User','username'=>'admin','email'=>'admin@example.com','password'=>'Admin!123','role'=>'admin'],
            ['name'=>'Case Manager','username'=>'casemgr','email'=>'casemgr@example.com','password'=>'Case!1234','role'=>'case_manager'],
            ['name'=>'Attorney User','username'=>'attorney','email'=>'attorney@example.com','password'=>'Law!12345','role'=>'attorney'],
            ['name'=>'Printing Staff','username'=>'printing','email'=>'printing@example.com','password'=>'Print!1234','role'=>'printing'],
            ['name'=>'Applicant Demo','username'=>'applicant','email'=>'applicant@example.com','password'=>'User!12345','role'=>'applicant'],
        ];

        foreach ($users as $u) {
            $user = User::firstOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'username' => $u['username'],
                    'first_name' => explode(' ', $u['name'])[0],
                    'last_name' => explode(' ', $u['name'])[1] ?? '',
                    'password' => Hash::make($u['password']),
                    'email_verified_at' => now()
                ]
            );
            if (!$user->hasRole($u['role'])) {
                $user->assignRole($u['role']);
            }
        }
    }
}
