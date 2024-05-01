<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Vacation;
use App\Models\Warehouse;
use App\Models\Warehouse_Manager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class VacationController extends Controller
{
    public function AddVacationForEmployee(Request $request){

        $validator = Validator::make($request->all(),[
            'user_id'=>'required',
            'start'=>'required|date_format:d/m/y',
            'end'=>'required|date_format:d/m/y',
            'reason'=>'required|string'      
        ]);

        if ($validator->fails())
        {
            return response()->json($validator->errors()->toJson(),400);
        }

        $employee = Employee::where('id' , $request->user_id)->first();
        if($employee){
            $manager = Auth::guard('branch_manager')->user();
            $vacation = Vacation::create([
                'user_id'=> $request->user_id,
                'user_type'=> 'employee',
                'start'=> $request->start,
                'end'=> $request->end,
                'reason'=>$request->reason,
                'created_by'=> $manager->name
            ]);
            return response()->json([
                'message'=>'Vacation addedd successfully'], 201);
        }
        return response()->json(['message'=>'Employee not found'], 400);  
    }

    public function AddVacationForWhManager(Request $request){

        $validator = Validator::make($request->all(),[
            'user_id'=>'required',
            'start'=>'required|date_format:d/m/y',
            'end'=>'required|date_format:d/m/y',
            'reason'=>'required|string'      
        ]);

        if ($validator->fails())
        {
            return response()->json($validator->errors()->toJson(),400);
        }

        $whmanager = Warehouse_Manager::where('id' , $request->user_id)->first();
        if($whmanager){
            $manager = Auth::guard('branch_manager')->user();
            $vacation = Vacation::create([
                'user_id'=> $request->user_id,
                'user_type'=> 'warehouse_manager',
                'start'=> $request->start,
                'end'=> $request->end,
                'reason'=>$request->reason,
                'created_by'=> $manager->name
            ]);
            return response()->json([
                'message'=>'Vacation addedd successfully'], 201);
        }
        return response()->json(['message'=>'Warehouse manager not found'], 400);  
    }

    public function GetEmployeeVacation(Request $request){

        $validator = Validator::make($request->all() , [
            'employee_id'=>'required'
        ]);

        if ($validator->fails())
        {
            return response()->json($validator->errors()->toJson(),400);
        }

        $employee = Employee::where('id' , $request->employee_id)->first();

        if($employee){
            $vacations = Vacation::where('user_type' , 'employee')
                                ->where('user_id' , $request->employee_id)
                                ->get();
            if(!$vacations){
                return response()->json(['message'=>'No vacations found'], 400);  
            }

            return response()->json(['Vacations'=> $vacations], 200);  
        }

        return response()->json(['message'=>'Employee not found'], 400);  
    }

    public function GetWhManagerVacation(Request $request){

        $validator = Validator::make($request->all() , [
            'wmanger_id'=>'required'
        ]);

        if ($validator->fails())
        {
            return response()->json($validator->errors()->toJson(),400);
        }

        $wmanager = Warehouse::where('id' , $request->wmanager)->first();

        if($wmanager){
            $vacations = Vacation::where('user_type' , 'warehouse_manager')
                                ->where('user_id' , $request->wmanager)
                                ->get();
            if(!$vacations){
                return response()->json(['message'=>'No vacations found'], 400);  
            }

            return response()->json(['Vacations'=> $vacations], 200);  
        }

        return response()->json(['message'=>'Warehouse manager not found'], 400);  
    }
}