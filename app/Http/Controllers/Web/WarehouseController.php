<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Branch_Manager;
use App\Models\Warehouse;
use App\Models\Warehouse_Manager;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth ;
use Illuminate\Support\Facades\Hash;

class WarehouseController extends Controller
{
    public function getAllWarehouses()
    {
        $branches=Branch::get();
        $warehouses = Warehouse::with('branch','wmanager')->get();
            return view('warehouses.warehouseslist',compact('warehouses','branches'));
    
    }

    public function getAllWarehousesM()
    {
        $warehouses=Warehouse::whereNull('warehouse_manager_id')->get();
        $managers = Warehouse_Manager::with('warehouse')->get();
            return view('warehouses.wmanagers',compact('managers','warehouses'));
    
    }
  
    public function addwarehouse(Request $request)
    {

        $validatedData = $request->validate([
            'address' => 'required',
            'area' => 'required',
            'notes' => 'required',
            'warehouse_name' => 'required',
            'branch_id' => 'required',
        ]);
    
    
            $wh = Warehouse::create($validatedData);
           
    
        session()->flash('Add', ' Added Successfully ');
        return redirect('/employee/getAllWarehouses');


    }
    
    public function Addwmanager(Request $request)
    {
    
        $validatedData = $request->validate([
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'manager_address' => 'required',
            'phone_number' => 'required',
            'warehouse_id' => 'required',
        ]);
    
        $validatedData['password'] = Hash::make($validatedData['password']);
    
            $wManager = Warehouse_Manager::create($validatedData);
            $wh = Warehouse::find($request->warehouse_id);
            $wh->warehouse_manager_id = $wManager->id;
            $wh->save();
    
        session()->flash('Add', ' Added Successfully ');
        return redirect('/employee/getallwmanagers');
    
    }
    
    
    public function GetAllWmanagers()
    {
        $branches = Branch::whereNull('branchmanager_id')->get();
        $managers = Warehouse_Manager::with('branch')->get();
        return view('branches.managerslist',compact('managers','branches'));
    
    }
    
    public function deletewarehouse(Request $request)
    {     
           $id = $request->id;
    
           $wh = Warehouse::find($id)->delete();
           Warehouse_Manager::where('warehouse_id', $id)->delete();
           session()->flash('delete',' Deleted Successfully');
           return redirect('/employee/getAllWarehouses');
    }
    
    public function editwarehouse( Request $request)
    {
       
        $id = $request->id;
    
            $wh = Warehouse::find($id);
            $wh->update([
               
                'warehouse_name' => $request->warehouse_name,
            ]);
    
            session()->flash('edit','تم التعديل  بنجاج');
            return redirect('/employee/getAllWarehouses');
        
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
    