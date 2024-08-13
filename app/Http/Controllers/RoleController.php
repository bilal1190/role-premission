<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


class RoleController extends Controller
{
  //this methode will show the role
  public function index()
  {
    // $roles = Role::orderBy('name','ASC')->paginate(10);       // old version show data to view
    $roles = Role::latest()->paginate(10);                      //   new version of show data to view
    return view('roles.list', compact('roles')); // new try if want to see old one then check permissions controller 


  }

  //this methode will create a new role   
  public function create()
  {
    $permissions = Permission::orderBy('name', 'ASC')->get();

    return view('roles.create', [
      'permissions' => $permissions,



    ]);
  }

  //this methode will store the new role
  public function store(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'name' => 'required|unique:roles|min:3'

    ]);

    if ($validator->passes()) {
      $role =  Role::create(['name' => $request->name]);

      if (!empty($request->permissions)) {
        foreach ($request->permissions as $name) {
          $role->givePermissionTo($name);

          # code...
        }
      }

      return redirect()->route('roles.index')->with('success', 'Role Added Successfully');
    } else {
      return redirect()->route('roles.create')->withInput()->withErrors($validator);
    }
  }


  public function edit($id)
  {

    $role = Role::findOrFail($id);
    $hasPermissions = $role->permissions->pluck('name');
    $permissions = Permission::latest()->get();


    return view('roles.edit', compact('hasPermissions', 'permissions', 'role'));
  }

  public function update(Request $request, $id)
  {
    $role = Role::findOrFail($id);
    $validator = Validator::make($request->all(), [
      'name' => 'required|unique:roles,name,' . $role->id . ',id'

    ]);

    if ($validator->passes()) {

      $role->name = $request->name;
      $role->save();

      if (!empty($request->permission)) {
        $role->syncPermissions($request->permission);
      } else {
        $role->syncPermissions([]);
      }

      return redirect()->route('roles.index')->with('success', 'Roles Updated Successfully');
    } else {
      return redirect()->route('roles.edit')->withInput()->withErrors($validator);
    }
  }

  public function destroy(Request $request)
  {
    $id = $request->id;
    $role = Role::find($id);

    if ($role == null) {
      session()->flash('error', 'Role Not Found');
      return response()->json([
        'status' => false

      ]);
    }

    $role->delete();

    session()->flash('success', 'Role Deleted Successfully');
    return response()->json([
      'status' => true

    ]);
  }
}
