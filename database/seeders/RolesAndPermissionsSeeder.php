<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $superAdminPermissions = [
            'can_create_admins',
            'can_edit_admins',
            'can_delete_admins',
        ];

        $adminPermissions = [
            'can_create_user',
            'can_edit_user',
            'can_delete_user',
            'can_view_user',
            'can_view_all_user',

            'can_create_client',
            'can_edit_client',
            'can_delete_client',
            'can_view_client',
            'can_view_all_clients',

            'can_create_pg_connection',
            'can_edit_pg_connection',
            'can_delete_pg_connection',
            'can_view_pg_connection',
            'can_view_all_pg_connection',

            'can_create_client_pg_connection',
            'can_edit_client_pg_connection',
            'can_delete_client_pg_connection',
            'can_view_client_pg_connection',
            'can_view_all_client_pg_connection',

            'can_create_transaction',
            'can_view_transaction',
            'can_view_all_transactions',
        ];

        $userPermissions = [
            'can_view_client',
            'can_view_transaction',
        ];

        $roles = [
            'superadmin' => $superAdminPermissions,
            'admin' => $adminPermissions,
            'user' => $userPermissions,
        ];

        $superAdminPermissions = array_merge($superAdminPermissions, $adminPermissions, $userPermissions);

        foreach ($roles as $role => $permissions) {

            $role = Role::firstOrCreate([
                'name' => $role,
                'guard_name' => 'web',
            ]);

            foreach ($superAdminPermissions as $permission) {
                $permission_db = Permission::firstOrCreate(['name' => $permission]);
                $role->givePermissionTo($permission_db);
            }

        }

    }
}
