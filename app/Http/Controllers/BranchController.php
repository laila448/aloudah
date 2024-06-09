<?php

namespace App\Http\Controllers;

use App\Mail\PasswordMail;
use App\Models\Branch;
use App\Models\Branch_Manager;
use Dotenv\Parser\Value;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;

class BranchController extends Controller
{

  public function GetAllBranches()
  {
      try {
          $branches = Branch::select('id', 'address', 'desk')->paginate(5);
          return response()->json([
              'success' => true,
              'data' => $branches,
              'message' => 'Branches retrieved successfully.'
          ], 200);
  
      } catch (\Exception $e) {
          return response()->json([
              'success' => false,
              'message' => 'Failed to retrieve branches.',
              'error' => $e->getMessage()
          ], 500);
      }
  }

<<<<<<< HEAD

     public function getBranchlatlng( $id)
     {
       

      $branch = Branch::select('branch_lat', 'branch_lng')->where('id', $id)->first();

    
      if (!$branch) {
          return response()->json([
            'success' => false ,
            'message' => 'Branch not found'
          ], 404);
      }
  
       return response()->json([
        'success' => true ,
        'data' => $branch 
        ] , 200) ;
  

     }
    public function AddBranch (Request $request)
    {
        $validator =  Validator::make($request->all(),[
          'desk'=>'required|min:3',
            'address'=>'required',
            'phone'=>'required|min:4|max:15',
=======
    // public function AddBranch (Request $request)
    // {
    //     $validator =  Validator::make($request->all(),[
    //       'desk'=>'required|min:3',
    //         'address'=>'required',
    //         'phone'=>'required|min:4|max:15',
>>>>>>> b8d37973ef91fa1b55801f44f3c24c3fdf7e92f1
          
    //     ]);
      
    //     if ($validator->fails())
    //     {
    //         return response()->json([
    //          'success' => false,
    //          'message' => $validator->errors()->toJson()
    //         ],400);
    //     }

    //     $branch = new Branch();
    //   $branch->desk = $validator['desk'];
    //     $branch->address = $validator['address'];
    //     $branch->phone = $validator['phone'];
    //     $branch->opening_date = now()->format('Y-m-d');
    //     $branch->created_by = Auth::guard('admin')->user()->name;
    //     $branch->save();
      

    //             return response()->json([
    //               'success' => true ,
    //               'message'=>'branch added successfully',  
    //             ],200);
   
    // }
//     public function AddBranch(Request $request)
// {
//     $validator = Validator::make($request->all(), [
//         'desk' => 'required|min:3',
//         'address' => 'required',
//         'phone' => 'required|min:4|max:15',
//     ]);

//     if ($validator->fails()) {
//         return response()->json([
//             'success' => false,
//             'message' => $validator->errors()->toJson()
//         ], 400);
//     }

//     $branch = new Branch();
//     $branch->desk = $request->input('desk');
//     $branch->address = $request->input('address');
//     $branch->phone = $request->input('phone');
//     $branch->opening_date = now()->format('Y-m-d');
//     $branch->created_by = Auth::guard('admin')->user()->name;

//     $branch->save();

//     return response()->json([
//         'success' => true,
//         'message' => 'Branch added successfully'
//     ], 200);
// }
public function AddBranch(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'desk' => 'required|min:3',
            'address' => 'required|string',
            'phone' => 'required|min:4|max:15',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toJson()
            ], 400);
        }

        $branch = new Branch();
      $branch->desk = $request->desk;
        $branch->address = $request->address;
        $branch->phone = $request->phone;
        $branch->opening_date = now()->format('Y-m-d');
        $branch->created_by = Auth::guard('admin')->user()->name;

        $branch->save();

        return response()->json([
            'success' => true,
            'message' => 'Branch added successfully'
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while adding the branch',
            'error' => $e->getMessage()
        ], 500);
    }
}

    // public function AddBranchManager (Request $request)
    // {
    //     $validator = Validator::make($request->all() ,[
    //         'branch_id'=>'required',
    //         'national_id'=>'required|max:11',
    //          'manager_name'=>'required',
    //          'email'=>'required',
    //          'phone_number'=>'required ',
    //          'gender'=>'required',   
    //          'mother_name'=>'required',
    //          'date_of_birth'=>'required|date_format:Y-m-d',
    //          'manager_address'=>'required',
    //         'salary'=>'required',
    //         'rank'=> ['required',Rule::in(['Branch_manager'])  ],
          
    //     ]);
      
    //     if ($validator->fails())
    //     {
    //         return response()->json([
    //          'success' => false,
    //          'message' => $validator->errors()->toJson()
    //         ],400);
    //     }

    //    $password = Str::random(8);
    //    $branchmanager = new Branch_Manager();
    //            $branchmanager->national_id = $request->national_id;
    //            $branchmanager->name = $request->manager_name;
    //            $branchmanager->email = $request->email;
    //            $branchmanager->password = Hash::make($password); 
    //            $branchmanager->phone_number = $request->phone_number;
    //            $branchmanager->branch_id = $request->branch_id;
    //            $branchmanager->gender = $request->gender;
    //            $branchmanager->mother_name = $request->mother_name; 
    //            $branchmanager->date_of_birth = $request->date_of_birth;
    //            $branchmanager->manager_address = $request->manager_address;
    //            $branchmanager->salary = $request->salary;
    //           $branchmanager->rank = $request->rank;
    //            $branchmanager->employment_date = now()->format('Y-m-d');
    //            $branchmanager->save();

    //             if($branchmanager){
    //               Mail::to($request->email)->send(new PasswordMail($request->manager_name , $password));
    //             }
    //             return response()->json([
    //               'success' => true ,
    //               'message'=>'branch manager added successfully',  
    //             ],200);
   
    // }

    public function AddBranchManager(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'branch_id' => 'required',
            'national_id' => 'required|max:11|unique:branch_managers,national_id',
            'manager_name' => 'required|unique:branch_managers,name',
            'email' => 'required|email|unique:branch_managers,email',
            'phone_number' => 'required|min:4|max:15|unique:branch_managers,phone_number',
            'gender' => 'required',
            'mother_name' => 'required',
            'date_of_birth' => 'required|date_format:Y-m-d',
            'manager_address' => 'required',
            'salary' => 'required',
            'rank' => ['required', Rule::in(['Branch_manager'])],
        ]);
            if ($validator->fails()) {
          $errors = $validator->errors()->all();
          return response()->json([
              'success' => false,
              'message' => 'Validation failed. Please check the following errors:',
              'errors' => $errors
          ], 400);
      }
    
        try {
            $password = Str::random(8);
            $branchManager = new Branch_Manager();
            $branchManager->national_id = $request->input('national_id');
            $branchManager->name = $request->input('manager_name');
            $branchManager->email = $request->input('email');
            $branchManager->password = Hash::make($password); 
            $branchManager->phone_number = $request->input('phone_number');
            $branchManager->branch_id = $request->input('branch_id');
            $branchManager->gender = $request->input('gender');
            $branchManager->mother_name = $request->input('mother_name'); 
            $branchManager->date_of_birth = $request->input('date_of_birth');
            $branchManager->manager_address = $request->input('manager_address');
            $branchManager->salary = $request->input('salary');
            $branchManager->rank = $request->input('rank');
            $branchManager->employment_date = now()->format('Y-m-d');
            $branchManager->save();
                Mail::to($branchManager->email)->send(new PasswordMail($branchManager->name, $password));
                return response()->json([
                'success' => true,
                'message' => 'Branch manager added successfully'
            ], 200);
        } catch (QueryException $e) {
            $errorCode = $e->errorInfo[1];
            if($errorCode == 1062){
                return response()->json([
                    'success' => false,
                    'message' => 'A manager with the same National ID, Email, Phone Number, or Manager Name already exists. Please ensure all fields are unique.'
                ], 400);
            }
    
            return response()->json([
                'success' => false,
                'message' => 'Failed to add branch manager.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    // public function UpdateBranch(Request $request)
    // {
    //   $user = Auth::guard('admin')->user();
      
    //   $validator =Validator::make($request->all(),[
    //     'branch_id'=>'numeric',
    //     'address'=>'string',
    //     'phone'=>'numeric|min:4',
    //     'name'=>'min:5|max:255',
    //     'phone_number'=> 'max:10',
    //     'manager_address'=>'string',
    //     'gender'=>'in:male,female',
    //     'mother_name'=>'string',
    //     'birth_date'=>'date_format:Y-m-d',
    //     'salary'=>'string',
    //     'rank'=>'string',
        
    // ]);
    // if ($validator->fails()){
    //     return response()->json([
    //     'success' => false,
    //     'message' => $validator->errors()->toJson()
    //     ],400);
    //     }
      

    //   $branch = Branch::find($request->branch_id);
    //   $updatedbranch= $branch->update(array_merge($request->all() ,[
    //   'edited_by' => $user->name,
    //   'editing_date' => now()->format('Y-m-d')
    //   ]
    // ));


    //   $branchManager = Branch_Manager::where('branch_id', $request->branch_id)->first();
    //   $updatedmanager= $branchManager->update($request->all());

    //   return response()->json([
    //     'success' => true ,
    //     'message' => 'Branch updated successfully'
    //   ] ,200 );


    // }
    public function UpdateBranch(Request $request)
    {
        try {
            $user = Auth::guard('admin')->user();
                $validator = Validator::make($request->all(), [
                'branch_id' => 'required|numeric',
                'address' => 'string',
                'phone' => 'numeric|min:4',
                'name' => 'min:5|max:255',
                'phone_number' => 'max:10',
                'manager_address' => 'string',
                'gender' => 'in:male,female',
                'mother_name' => 'string',
                'birth_date' => 'date_format:Y-m-d',
                'salary' => 'string',
                'rank' => 'string',
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->toJson()
                ], 400);
            }
                $branch = Branch::find($request->branch_id);
    
            if (!$branch) {
                return response()->json([
                    'success' => false,
                    'message' => 'Branch not found'
                ], 404);
            }
                $branch->update(array_merge($validator->validated(), [
                'edited_by' => $user->name,
                'editing_date' => now()->format('Y-m-d')
            ]));
                $branchManager = Branch_Manager::where('branch_id', $request->branch_id)->first();
    
            if ($branchManager) {
                $branchManager->update($validator->validated());
            }
            return response()->json([
                'success' => true,
                'message' => 'Branch updated successfully'
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the branch',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    

    // public function deleteBranch(Request $request)
    // {
    //   $validator =Validator::make($request->all(),[
    //     'branch_id'=>'required|numeric',
    // ]);

    // if ($validator->fails()){
    //   return response()->json([
    //   'success' => false,
    //   'message' => $validator->errors()->toJson()
    //   ],400);
    //   }

    //     $branch = Branch::find($request->branch_id)->delete();
    //     $branchManager = Branch_Manager::where('branch_id', $request->branch_id)->delete();

    //     return response()->json([
    //       'success' => true ,
    //       'msg'=>'Branch has been deleted'], 200) ;
    // }
    public function deleteBranch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'branch_id' => 'required|numeric',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toJson()
            ], 400);
        }
    
        try {
            $branch = Branch::find($request->branch_id);
    
            if ($branch) {
                $branch->delete();
                    Branch_Manager::where('branch_id', $request->branch_id)->delete();
    
                return response()->json([
                    'success' => true,
                    'message' => 'Branch has been deleted'
                ], 200);
            }
    
            return response()->json([
                'success' => false,
                'message' => 'Branch not found'
            ], 404);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the branch',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    

    public function GetBranches()
    {
        try {
            $branches = Branch::select('id', 'address', 'desk')->paginate(5);
                if ($branches->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No branches found'
                ], 200);
            }
                return response()->json([
                'success' => true,
                'data' => $branches,
                'message' => 'Branches retrieved successfully.'
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve branches.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    


  
}
