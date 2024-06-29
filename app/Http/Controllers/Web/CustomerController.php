<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function GetCustomers()
    {
     $customers=Customer::get();
     return view('customers.customerslist',compact('customers'));
   
   
    }


    public function AddCustomer(Request $request)
    {
  
        //  $customers=Customer::get();
  

        Customer::create([
            'name' => $request->name,
            'phone_number' => $request->phone,
            'national_id' => $request->national_id,
            'gender' => $request->gender,
            'mobile' => $request->mobile,
            'address' => $request->address,
            'address_detail' => $request->address_detail,
            'notes' => $request->notes,
            'added_by' => ( Auth::guard('emp_web')->user()->name),
           


        ]);
        session()->flash('Add', 'Added Successffully    ');
        return redirect('/employee/getcustomers');


    
   
   
    }


    public function DeleteCustomer()
    {
     $customers=Customer::get();
     return view('customers.customerslist',compact('customers'));
   
   
    }
    public function EditCustomer(Request $request)
    {


     $id = $request->id;
     
    

     $customer = Customer::find($id);
     $customer->update([
         'name' => $request->name,
         'phone_number' => $request->phone_number,
         'national_id' => $request->national_id,
         'gender' => $request->gender,
         'mobile' => $request->mobile,
         'address' => $request->address,
         'address_detail' => $request->address_detail,
     ]);

     session()->flash('edit','  Edit Successfully ');
     return redirect('/employee/getcustomers');
   
    }
   

public function getCompliant()
{

    $comps=Complaint::with('customer')->get();
    return view ('customers.compliantlist',compact('comps'));
}

}
