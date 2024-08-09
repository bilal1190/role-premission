<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


class RoleController extends Controller
{
    //this methode will show the role
    public function index(){
        // $roles = Role::orderBy('name','ASC')->paginate(10);       // old version show data to view
        $roles = Role::latest()->paginate(10);                      //   new version of show data to view
        return view('roles.list',compact('roles')); // new try if want to see old one then check permissions controller 


    }

    //this methode will create a new role   
    public function create(){
        $permissions = Permission::orderBy('name', 'ASC')->get();

        return view('roles.create',[
            'permissions' => $permissions,
            


        ]);

    }

    //this methode will store the new role
    public function store(Request $request){
        dd($request->all());

    $validator = Validator::make($request->all() , [
        'name' => 'required|unique:roles|min:3'
  
      ]);
    
      if ($validator->passes()) {
        dd($role->permissions);
        $role =  Role::create(['name' => $request->name]);
         
        if(!empty($request->permissions)){
            foreach ($request->permissions as $name) {
                $role->givePermissionTo($name);

                # code...
            }
            


        }

        return redirect()->route('roles.index')->with('success', 'Role Added Successfully');
      }
       else {
        return redirect()->route('roles.create')->withInput()->withErrors($validator);
      }








    }
  
    
}
