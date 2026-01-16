<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Traits\Roles;
use App\Traits\Report;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Role\Create;

class RoleController extends Controller
{
    use Roles ; 
    public function index()
    {
        if (request()->ajax()) {
            $roles = Role::search(request()->searchArray)->paginate(30);
            $html = view('admin.roles.table' ,compact('roles'))->render() ;
            return response()->json(['html' => $html]);
        }
        return view('admin.roles.index');
    }
  
    public function create()
    {
        $html = $this->addRole();
        return view('admin.roles.create' , compact('html'));
    }

    public function store(Create $request)
    {
        if(!$request->permissions){
            return back()->with('danger', 'يجب اختيار صلاحيه واحده علي الاقل ');
        }
        $role = Role::create($request->validated());
        // dd($request->permissions);
        $permissions = [];
        foreach ($request->permissions ?? [] as $permission)
            $permissions[]['permission'] = $permission;

        $role->permissions()->createMany($permissions);
        Report::addToLog('  اضافه صلاحية') ;
        return redirect(route('admin.roles.index'))->with('success', 'تم الاضافه بنجاح');
    }

    /***************************  get all roles  **************************/
    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $html = $this->editRole($id);
        return view('admin.roles.edit', compact('role' , 'html'));
    }

    public function update(Create $request, $id)
    {
        $role = Role::findOrFail($id);
        $role->update($request->validated());
        
        $role->permissions()->delete();
        $permissions = [];
        foreach ($request->permissions ?? [] as $permission)
            $permissions[]['permission'] = $permission;

        $role->permissions()->createMany($permissions);
        Report::addToLog('  تعديل صلاحية') ;

        return redirect(route('admin.roles.index'))->with('success', 'تم التعديل بنجاح');
    }

   public function destroy($id)
{
    $role = Role::findOrFail($id);

    if ($role->admins()->count() > 0) {
        return response()->json([
            'status' => 'error',
'message' => __('admin.role_delete_has_admin'),
        ], 422);  // 422 Unprocessable Entity
    }

    if ($role->delete()) {
        return response()->json([
            'status' => 'success',
            'message' => 'تم الحذف بنجاح.'
        ], 200);
    }

    return response()->json([
        'status' => 'error',
        'message' => 'حدث خطأ أثناء الحذف، يرجى المحاولة مرة أخرى.'
    ], 500);
}
}
