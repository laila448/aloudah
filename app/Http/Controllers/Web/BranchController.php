<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Branch_Manager;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        'created_by' => ( Auth::guard('emp_web')->user()->name),
        'opening_date' =>Carbon::now() ,
    


    ]);
    session()->flash('Add', ' Added Successfully ');
    return redirect('/employee/getallbranches');

}


public function GetAllManagers()
{

    $managers = Branch_Manager::with('branch')->get();
    return view('branches.managerslist',compact('managers'));

}

public function DeleteBranch(Request $request)
{     
       $id = $request->id;

       $branch = Branch::find($id)->delete();
       session()->flash('delete',' Deleted Successfully');
       return redirect('/employee/getallbranches');
}


public function EditBranchManager( Request $request)
{
   
    $id = $request->id;

    

        $truck = Branch_Manager::find($id);
        $truck->update([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
        ]);

        session()->flash('edit','تم التعديل  بنجاج');
        return redirect('/employee/getallmanagers');
    
 }

public function DeleteBranchManager(Request $request)
{
    $id = $request->id;
    Branch_Manager::find($id)->delete();
    session()->flash('delete','تم الحذف  بنجاح');
    return redirect('/employee/getallmanagers');

}
}
