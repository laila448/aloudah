<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function GetAllEmployees()
    {
        $employees = Employee::with('branch')
        ->orderBy('branch_id')
        ->get();
            return view('Employee.Employeelist',compact('employees'));
    
    }

    public function searchemployees(Request $request)
{
    $query = Employee::with('branch');

    if ($request->has('search')) {
        $search = $request->input('search');
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%')
              ->orWhereHas('branch', function ($q) use ($search) {
                  $q->where('desk', 'like', '%' . $search . '%');
                  
              });
        });
    }

    $employees = $query->get();

    return view('Employee.Employeelist', compact('employees'));
}


}
