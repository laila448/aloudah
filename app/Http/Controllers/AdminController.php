<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Branch_Manager;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class AdminController extends Controller
{
   // use Validator;
    public function AddBranch (Request $request)
    {
        $validator =  $request->validate([
//'desk'=>'required|min:3',
            'address'=>'required',
            'phone'=>'required|min:4|max:15',
             'manager_name'=>'required',
             'email'=>'required',
             'password'=>'required',
             'phone_number'=>'required ',
             'gender'=>'required',
             'warehouse_address'=>'required',
              'warehouse_number'=>'required ',
        ]);
      


        $branch = new Branch();
       //$branch->desk = $validator['desk'];
        $branch->address = $validator['address'];
        $branch->phone = $validator['phone'];
        $branch->save();
      

        $branchmanager = new Branch_Manager();
                $branchmanager->name = $validator['manager_name'];
                $branchmanager->email = $validator['email'];
                $branchmanager->password = $validator['password'];
                $branchmanager->phone_number = $validator['phone_number'];
               $branchmanager->branch_id = $branch->id;
                $branchmanager->gender = $validator['gender'];
                $branchmanager->save();


          $warehouse = new Warehouse();
                $warehouse->address = $validator['warehouse_address'];
                $warehouse->phone_number = $validator['warehouse_number'];  
              // $warehouse->branch_id = $branch->id;
                $warehouse->save();



                return response()->json([
                    'message'=>'added successfully',  
                ],201);
   
    }
}
