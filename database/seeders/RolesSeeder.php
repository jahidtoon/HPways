<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'applicant',
            'admin',
            'case_manager',
            'attorney',
            'printing'
        ];

        foreach ($roles as $r) {
            Role::firstOrCreate(['name' => $r]);
        }

        // Basic permissions scaffold (extend later)
        $perms = [
            'application.view.own',
            'application.create',
            'application.manage',
            'documents.upload',
            'documents.review',
            'feedback.create',
            'shipments.manage',
        ];
        foreach ($perms as $p) {
            Permission::firstOrCreate(['name' => $p]);
        }

        // Simple assignment
        Role::where('name','applicant')->first()?->givePermissionTo(['application.view.own','application.create','documents.upload']);
        Role::where('name','case_manager')->first()?->givePermissionTo(['application.manage','documents.review']);
        Role::where('name','attorney')->first()?->givePermissionTo(['documents.review','feedback.create']);
        Role::where('name','printing')->first()?->givePermissionTo(['shipments.manage']);
        Role::where('name','admin')->first()?->givePermissionTo($perms);
    }
}
