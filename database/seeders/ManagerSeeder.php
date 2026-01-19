<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Creates a Manager role with specific permissions and assigns it to a manager user
     */
    public function run(): void
    {
        // ========================================
        // 1. CREATE MANAGER ROLE
        // ========================================
        
        $managerRole = Role::create([
            'name' => [
                'en' => 'Manager',
                'ar' => 'مدير'
            ],
        ]);

        $this->command->info("✅ Manager role created with ID: {$managerRole->id}");

        // ========================================
        // 2. DEFINE MANAGER PERMISSIONS
        // ========================================

        $managerPermissions = [
            // Orders permissions
            'admin.orders.management',
            'admin.orders.index',
            'admin.orders.show',
            'admin.orders.updateStatus',

            // Products permissions
            'admin.products.management',
            'admin.products.index',
            'admin.products.show',
            'admin.products.edit',
            'admin.products.update',
             'admin.cancel_request_orders.index' , 'admin.cancel_request_orders.show' , 'admin.cancel_request_orders.accept' , 'admin.cancel_request_orders.reject',
                 'admin.refund_orders.index' , 'admin.refund_orders.show' , 'admin.refund_orders.accept' , 'admin.refund_orders.refuse','admin.problem_orders.index'
        ];

        // ========================================
        // 3. CREATE PERMISSIONS FOR MANAGER ROLE
        // ========================================
        
        $permissionsData = [];
        foreach ($managerPermissions as $permission) {
            $permissionsData[] = [
                'role_id' => $managerRole->id,
                'permission' => $permission,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        Permission::insert($permissionsData);

        $this->command->info("✅ Created " . count($managerPermissions) . " permissions for Manager role");

        // ========================================
        // 4. CREATE MANAGER USER
        // ========================================
        
        $manager = Admin::create([
            'name' => 'Manager',
            'email' => 'manager@admin.com',
            'phone' => '0501234567',
            'password' => '123456', // Will be hashed by model mutator
            'role_id' => $managerRole->id,
            'is_blocked' => false,
            'is_notify' => true,
        ]);

        $this->command->info("✅ Manager user created:");
        $this->command->info("   Email: manager@admin.com");
        $this->command->info("   Phone: 0501234567");
        $this->command->info("   Password: 123456");
        $this->command->info("   Role ID: {$managerRole->id}");

        // ========================================
        // 5. SUMMARY
        // ========================================
        
        $this->command->newLine();
        $this->command->info("========================================");
        $this->command->info("Manager Setup Complete!");
        $this->command->info("========================================");
        $this->command->info("Role: Manager (ID: {$managerRole->id})");
        $this->command->info("Permissions:");
        foreach ($managerPermissions as $permission) {
            $this->command->info("  - {$permission}");
        }
        $this->command->newLine();
        $this->command->info("Manager Login Credentials:");
        $this->command->info("  Email: manager@admin.com");
        $this->command->info("  Phone: 0501234567");
        $this->command->info("  Password: 123456");
        $this->command->info("========================================");
    }
}

