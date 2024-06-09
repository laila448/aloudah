<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Truck;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class truckController extends Controller
{
    public function GetTrucks()
    {
        $trucks=Truck::get();
        return view('trucks.truckslist',compact('trucks'));
    }


    public function AddTruck(Request $request)
    {

        $validatedData = $request->validate([
            'number' => 'required|unique:trucks|max:255',
        ],[

            'number.required' =>' يرجي ادخال رقم الشاحنة ',
            'number.unique' =>' الرقم مسجل مسبقا',


        ]);

            Truck::create([
                'number' => $request->number,
                'line' => $request->line,
                'created_by' => ( Auth::guard('emp_web')->user()->name),
                'adding_data' =>Carbon::now() ,


            ]);
            session()->flash('Add', 'تم اضافة الشاحنة بنجاح ');
            return redirect('/employee/truckslist');


        
            }


    public function DeleteTruck(Request $request)
    {
        $id = $request->id;
        Truck::find($id)->delete();
        session()->flash('delete','تم الحذف  بنجاح');
        return redirect('/employee/truckslist');
    }
public function EditTruck(Request $request)
{
    $id = $request->id;

        $this->validate($request, [

            'number' => 'required|max:255|unique:trucks,number,'.$id,
            'line' => 'required',
        ],[

            'number.required' =>'يرجي ادخال الرقم ',
            'number.unique' =>'اسم القسم مسجل مسبقا',
            'line.required' =>'يرجي ادخال الخط',

        ]);

        $truck = Truck::find($id);
        $truck->update([
            'number' => $request->number,
            'line' => $request->line,
        ]);

        session()->flash('edit','تم التعديل  بنجاج');
        return redirect('/employee/truckslist');
    
 }


}