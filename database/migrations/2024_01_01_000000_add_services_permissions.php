<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Permission;

class AddServicesPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $serviceRoutes = [
            'admin.services.index',
            'admin.services.create', 
            'admin.services.store',
            'admin.services.edit',
            'admin.services.update',
            'admin.services.show',
            'admin.services.delete',
            'admin.services.deleteAll',
            'admin.services.toggleStatus'
        ];

        foreach($serviceRoutes as $route) {
            Permission::create([
                'role_id' => 1, // Admin role
                'permission' => $route
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $serviceRoutes = [
            'admin.services.index',
            'admin.services.create', 
            'admin.services.store',
            'admin.services.edit',
            'admin.services.update',
            'admin.services.show',
            'admin.services.delete',
            'admin.services.deleteAll',
            'admin.services.toggleStatus'
        ];

        Permission::whereIn('permission', $serviceRoutes)->delete();
    }
}
