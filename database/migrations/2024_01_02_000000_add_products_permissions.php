<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Permission;

class AddProductsPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $productRoutes = [
            'admin.products.index',
            'admin.products.create', 
            'admin.products.store',
            'admin.products.edit',
            'admin.products.update',
            'admin.products.show',
            'admin.products.delete',
            'admin.products.deleteAll',
            'admin.products.toggleStatus'
        ];

        foreach($productRoutes as $route) {
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
        $productRoutes = [
            'admin.products.index',
            'admin.products.create', 
            'admin.products.store',
            'admin.products.edit',
            'admin.products.update',
            'admin.products.show',
            'admin.products.delete',
            'admin.products.deleteAll',
            'admin.products.toggleStatus'
        ];

        Permission::whereIn('permission', $productRoutes)->delete();
    }
}
