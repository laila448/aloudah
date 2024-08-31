<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Branch_Manager;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class BranchController extends Controller
{
public function getBranches()
{
    $branches = Branch::with('branch_manager')->get();
        return view('branches.brancheslist',compact('branches'));

}


public function AddBranch(Request $request)
{
    Branch::create([
        'address' => $request->address,
        'desk' => $request->desk,
        'phone' => $request->phone,
        'created_by' => ( Auth::guard('admin_web')->user()->name),
        'opening_date' =>Carbon::now() ,
    


    ]);
    session()->flash('Add', ' Added Successfully ');
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
}
