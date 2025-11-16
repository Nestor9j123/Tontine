<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer les rôles
        $superAdmin = Role::create(['name' => 'super_admin']);
        $secretary = Role::create(['name' => 'secretary']);
        $agent = Role::create(['name' => 'agent']);

        // Créer les permissions
        $permissions = [
            // Users
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            
            // Clients
            'view_clients',
            'create_clients',
            'edit_clients',
            'delete_clients',
            
            // Products
            'view_products',
            'create_products',
            'edit_products',
            'delete_products',
            
            // Tontines
            'view_tontines',
            'create_tontines',
            'edit_tontines',
            'delete_tontines',
            'validate_tontines',
            
            // Payments
            'view_payments',
            'create_payments',
            'edit_payments',
            'delete_payments',
            'validate_payments',
            'reject_payments',
            
            // Reports
            'view_reports',
            'export_reports',
            
            // Activity Logs
            'view_activity_logs',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Assigner toutes les permissions au Super Admin
        $superAdmin->givePermissionTo(Permission::all());

        // Assigner les permissions au Secrétaire
        $secretary->givePermissionTo([
            'view_users',
            'view_clients',
            'view_products',
            'view_tontines',
            'validate_tontines',
            'view_payments',
            'validate_payments',
            'reject_payments',
            'view_reports',
            'export_reports',
            'view_activity_logs',
        ]);

        // Assigner les permissions à l'Agent
        $agent->givePermissionTo([
            'view_clients',
            'create_clients',
            'edit_clients',
            'view_products',
            'view_tontines',
            'create_tontines',
            'view_payments',
            'create_payments',
        ]);
    }
}
