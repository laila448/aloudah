<?php

namespace App\Http\Controllers;

use App\Mail\PasswordMail;
use App\Models\Branch;
use App\Models\Branch_Manager;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BranchController extends Controller
{

     public function GetAllBranches()
      {
      //  json(['Archived trips' => $archivedRecords]);
        $branches=Branch::select('address','desk')->get();
        return response()->json(['address  ' =>$branches]);

      }

    public function AddBranch (Request $request)
    {
        $validator =  $request->validate([
          'desk'=>'required|min:3',
            'address'=>'required',
            'phone'=>'required|min:4|max:15',
             'manager_name'=>'required',
             'email'=>'required',
             //'password'=>'required',
             'phone_number'=>'required ',
             'gender'=>'required',   
             'mother_name'=>'required',
             'date_of_birth'=>'required|date_format:Y-m-d',
             'manager_address'=>'required',
            'salary'=>'required',
            'rank'=> ['required',Rule::in(['Branch_manager'])  ],
          
        ]);
      

        $branch = new Branch();
      $branch->desk = $validator['desk'];
        $branch->address = $validator['address'];
        $branch->phone = $validator['phone'];
        $branch->opening_date = now()->format('Y-m-d');
        $branch->created_by = Auth::guard('admin')->user()->name;
        $branch->save();
      

        $password = Str::random(8);
        $branchmanager = new Branch_Manager();
                $branchmanager->name = $validator['manager_name'];
                $branchmanager->email = $validator['email'];
                $branchmanager->password = Hash::make($password); 
               // $branchmanager->password = Hash::make($validator['password']); 
                $branchmanager->phone_number = $validator['phone_number'];
               $branchmanager->branch_id = $branch->id;
                $branchmanager->gender = $validator['gender'];
                $branchmanager->mother_name = $validator['mother_name']; 
                $branchmanager->date_of_birth = $validator['date_of_birth'];
                 $branchmanager->manager_address = $validator['manager_address'];
                //  $branchmanager->vacations = $validator['vacations'];
                 $branchmanager->salary = $validator['salary'];
                 $branchmanager->rank = $validator['rank'];
                  $branchmanager->employment_date = now()->format('Y-m-d');
                $branchmanager->save();

                if($branchmanager){
                  Mail::to($request->email)->send(new PasswordMail($request->manager_name , $password));
                }
                return response()->json([
                    'message'=>'branch added successfully',  
                ],201);
   
    }


    public function UpdateBranch(Request $request)
    {
      $user = Auth::guard('admin')->user();
      
      $validator =Validator::make($request->all(),[
        'branch_id'=>'numeric',
        'address'=>'string',
        'phone'=>'numeric|min:4',
        'name'=>'min:5|max:255',
        'phone_number'=> 'max:10',
        'manager_address'=>'string',
        'gender'=>'in:male,female',
        'mother_name'=>'string',
        'birth_date'=>'date_format:Y-m-d',
        'salary'=>'string',
        'rank'=>'string',
        
    ]);
    if ($validator->fails())
    {
        return response()->json($validator->errors()->toJson(),400);
    }
      

      $branch = Branch::find($request->branch_id);
      $updatedbranch= $branch->update(array_merge($request->all() ,[
      'edited_by' => $user->name,
      'editing_date' => now()->format('Y-m-d')
      ]
    ));


      $branchManager = Branch_Manager::where('branch_id', $request->branch_id)->first();
      $updatedmanager= $branchManager->update($request->all());

      return response()->json(['message' => 'Branch updated successfully']);


    }


    public function deleteBranch(Request $request)
    {
      $validator =Validator::make($request->all(),[
        'branch_id'=>'required|numeric',
    ]);

    if ($validator->fails())
    {
        return response()->json($validator->errors()->toJson(),400);
    }

        $branch = Branch::find($request->branch_id)->delete();
        $branchManager = Branch_Manager::where('branch_id', $request->branch_id)->delete();

        return response()->json(['msg'=>'Branch has been deleted'], 200) ;
    }


    public function GetBranches()
    {

        $branches = Branch::pluck('desk');
        if ($branches->isEmpty()) {
            return response()->json(['message' => 'No branches found']);
        }
        return response()->json(['branches' => $branches]);

    }





  
}
