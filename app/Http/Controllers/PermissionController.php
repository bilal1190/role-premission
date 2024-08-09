<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
  public function index()
  {
    $permissions = Permission::latest()->paginate(10);
    return view('permissions.list', [
      'permissions' => $permissions
    ]);
    // compact('permissions')
  }
  
  public function create()
  {

    return view('permissions.create');
  }

  // store in DB
  public function store(Request $request)
  {

    $validator = Validator::make($request->all(), [
      'name' => 'required|string|unique:permissions|max:20'

    ]);
    
    if ($validator->passes()) {
      Permission::create(['name' => $request->name]);


      return redirect()->route('permissions.index')->with('success', 'Permission Created Successfully');
    } else {
      return redirect()->route('permissions.create')->withInput()->withErrors($validator);
    }
  }

  /*
     '.$id.': This is a dynamic value that is concatenated with the string. It represents the ID of the current record being updated. When you are editing an existing record, you don't want to check the current record for uniqueness because it will obviously already have that name. This helps to exclude the current record from the uniqueness check.
    */

  public function edit($id)
  {
    $permission = Permission::findOrFail($id);
    return view('permissions.edit', ['permission' => $permission]);
  }

  public function update(Request $request, Permission $permission)
  {
    // $permission = Permission::findOrFail($id);
    $validator = Validator::make($request->all(), [
      'name' => 'required|max:20|unique:permissions,name,' . $permission->id . ',id'

    ]);
    if ($validator->passes()) {
      $permission->name = $request->name;
      $permission->save();


      return redirect()->route('permissions.index')->with('success', 'Permission Update Successfully');
    } else {
      return redirect()->route('permissions.create')->withInput()->withErrors($validator);
    }
  }

  public function destroy(Request $request)
  {
    $id = $request->id;
    $permission = Permission::find($id);

    if ($permission == null) {

      session()->flash('error', 'Permission Not Found');
      return response()->json([
        'status' => false

      ]);
    }

    $permission->delete();

    session()->flash('success', 'Permission Deleted');
    return response()->json([
      'status' => true

    ]);
  }
}
