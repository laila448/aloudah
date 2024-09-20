<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Branch;
use App\Models\Branch_Manager;
use App\Models\Employee;
use App\Models\Price;
use App\Models\Warehouse;
use App\Models\Warehouse_Manager;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class BranchController extends Controller
{
public function  getmyprofile()
{
 return view('profile');
}
public function editprofile(Request $request)
{

    $id = $request->id;

    $admin = Admin::find($id);
    $admin->update([
        'name' => $request->name,
        'email' => $request->email,
        'phone_number' => $request->phone_number,
        'password' =>  Hash::make($request->password),

    ]);

    session()->flash('edit','تم التعديل  بنجاج');
    return view('profile');

}

public function getBranches()
{
    $branches = Branch::with('branch_manager')->get();
        return view('branches.brancheslist',compact('branches'));

}


public function AddBranch(Request $request)
{
    $request->validate([
        'address' => 'required|string',
        'desk' => 'required|string',
        'phone' => 'required|string',
        'branch_lat' => 'required|numeric',
        'branch_lng' => 'required|numeric',
    ]);

    Branch::create([
        'address' => $request->address,
        'desk' => $request->desk,
        'phone' => $request->phone,
        'branch_lat' => $request->branch_lat, // Save latitude
        'branch_lng' => $request->branch_lng, // Save longitude
        'created_by' => Auth::guard('admin_web')->user()->name,
        'opening_date' => Carbon::now(),
    ]);

    session()->flash('Add', 'Added Successfully');
    return redirect('/employee/getallbranches');
}
public function AddBranchManager(Request $request)
{ 
 
    $validatedData = $request->validate([
        'name' => 'required',
        'email' => 'required',
        'password' => 'required',
        'manager_address' => 'required',
        'phone_number' => 'required',
        'national_id' => 'required',
        'gender' => '',
        'mother_name' => '',
        'date_of_birth' => '',
        'branch_id' => 'required',
    ]);

    $validatedData['password'] = Hash::make($validatedData['password']);

        $branchManager = Branch_Manager::create($validatedData);
        $branch = Branch::find($request->branch_id);
        $branch->branchmanager_id = $branchManager->id;
        $branch->save();

    session()->flash('Add', ' Added Successfully ');
    return redirect('/employee/getallbranches');

}


public function GetAllManagers()
{
    $branches = Branch::whereNull('branchmanager_id')->get();
    $managers = Branch_Manager::with('branch')->get();
    return view('branches.managerslist',compact('managers','branches'));

}

public function DeleteBranch(Request $request)
{     
       $id = $request->id;

       $branch = Branch::find($id)->delete();
       Branch_Manager::where('branch_id', $id)->delete();
       session()->flash('delete',' Deleted Successfully');
       return redirect('/employee/getallbranches');
}

public function EditBranch( Request $request)
{
   
    $id = $request->id;

        $branch = Branch::find($id);
        $branch->update([
           
            'phone' => $request->phone,
            'edited_by'=> ( Auth::guard('admin_web')->user()->name),
            'editing_date' => now()->format('Y-m-d'),
        ]);

        session()->flash('edit','تم التعديل  بنجاج');
        return redirect('/employee/getallbranches');
    
 }
public function EditBranchManager( Request $request)
{
    
    $id = $request->id;

    

        $bm = Branch_Manager::find($id);
        $bm->update([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'national_id' => $request->national_id,
            'manager_address' => $request->manager_address,

        ]);

        session()->flash('edit','تم التعديل  بنجاج');
        return redirect('/employee/getallmanagers');
    
 }

public function DeleteBranchManager(Request $request)
{
    $id = $request->id;
    $branch_manager = Branch_Manager::find($id);

    $branch = Branch::where('branchmanager_id', $branch_manager->id)->first();
    if ($branch) {
        $branch->branchmanager_id = null;
        $branch->save();
    }

    $branch_manager->delete();
    
    session()->flash('delete','تم الحذف  بنجاح');
    return redirect('/employee/getallmanagers');

}


public function getprices()
{ 
    $types = Price::get();
    return view('Price.priceslist',compact('types'));




}
public function addprice(Request $request)
{ 
    Price::create([
        'type' => $request->type,
        'cost' => $request->cost,
        


    ]);
    session()->flash('Add', 'تمت الاضافة  بنجاح ');
    return redirect('/employee/getprices');






}
public function editprice(Request $request)
{ 
    $id = $request->id;
    $price = Price::find($id);
    $price->update([
        'type' => $request->type,
        'cost' => $request->cost,
    ]);

    session()->flash('edit','تم التعديل  بنجاج');
    return redirect('/employee/getprices');




}
public function deleteprice(Request $request)
{ 
    $id = $request->id;
    Price::find($id)->delete();
    session()->flash('delete','تم الحذف  بنجاح');
    return redirect('/employee/getprices');



}



public function ShowBranchEmp(Request $request)
{

    $id = $request->id;


    $emps=Employee::where('branch_id',$id)->get();
    return view('branches.emps',compact('emps'));




}

public function PromoteEmployee(Request $request)
{
   

        $employee = Employee::where('id', $request->id)->first();

     
        $branch = Branch::find($request->branch_id);

        if ($branch && is_null($branch->branchmanager_id)) {
            $branchManager= Branch_Manager::create([
                'national_id' => $employee->national_id,
                'name' => $employee->name,
                'email' => $employee->email,
                'password' => $employee->password,
                'phone_number' => $employee->phone_number,
                'branch_id' => $request->branch_id,
                'gender' => $employee->gender,
                'mother_name' => $employee->mother_name,
                'date_of_birth' => $employee->birth_date,
                'manager_address' => $employee->address,
                'employment_date' => now()->format('Y-m-d'),
            ]);
            $branch->branchmanager_id = $branchManager->id;
        $branch->save();

         
            $employee->delete();

            session()->flash('edit','تم الترقية  بنجاج');

          
           $branches = Branch::with('branch_manager')->get();
           return view('branches.brancheslist',compact('branches'));
        }

        else {

            session()->flash('delete', 'لا يمكن الترقية، الفرع لديه مدير بالفعل.');
            $branches = Branch::with('branch_manager')->get();
            return view('branches.brancheslist',compact('branches'));
        
        }
            
          
        }
        

        public function PromoteEmployeeWH(Request $request)
{
   

        $employee = Employee::where('id', $request->id)->first();

        $warehouse = Warehouse::where('branch_id', $request->branch_id)->first();

        if ($warehouse && is_null($warehouse->warehouse_manager_id)) {
         
            $warehouseManager =   Warehouse_Manager::create([
                'national_id' => $employee->national_id,
                'name' => $employee->name,
                'email' => $employee->email,
                'password' => $employee->password,
                'phone_number' => $employee->phone_number,
                'warehouse_id' => $warehouse->id, 
                'branch_id' => $request->branch_id,
                'gender' => $employee->gender,
                'mother_name' => $employee->mother_name,
                'date_of_birth' => $employee->birth_date,
                'manager_address' => $employee->address,
                'employment_date' => now()->format('Y-m-d'),
            ]);
                // Update the warehouse with the new warehouse manager's ID
                 $warehouse->warehouse_manager_id = $warehouseManager->id;
                 $warehouse->save();
         
            $employee->delete();

            session()->flash('edit','تم الترقية  بنجاج');

          
           $branches = Branch::with('branch_manager')->get();
           return view('branches.brancheslist',compact('branches'));
        }

        else {

            session()->flash('delete', 'لا يمكن الترقية، المستودع لديه مدير بالفعل.');
            $branches = Branch::with('branch_manager')->get();
            return view('branches.brancheslist',compact('branches'));
        
        }
          
 }
        
  

}    


