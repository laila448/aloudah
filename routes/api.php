<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\BranchManagerController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\TruckController;
use App\Http\Controllers\VacationController;
use App\Http\Controllers\WarehouseController;
use App\Models\Employee;
use App\Models\Truck;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post( 'register' , [AuthController::class , 'Register']);
Route::post( 'login' , [AuthController::class , 'Login']);





Route::group(['middleware' => 'BranchManager',
              'prefix' => 'branchmanager'], function() {
       Route::post('addemployee' , [EmployeeController::class , 'AddEmployee']);
       Route::post('updateemployee' , [EmployeeController::class , 'UpdateEmployee']);
       Route::post('updatedriver' , [EmployeeController::class , 'UpdateDriver']);
       Route::post('deleteemployee' , [EmployeeController::class , 'DeleteEmployee']);
       Route::post('deletedriver' , [EmployeeController::class , 'DeleteDriver']);
       Route::post('promoteemployee' , [EmployeeController::class , 'PromoteEmployee']);
       Route::post('rateemployee' , [EmployeeController::class , 'RateEmployee']);
       Route::post('addtruck' , [TruckController::class , 'AddTruck']);
       Route::post('updatetruck' , [TruckController::class , 'UpdateTruck']);
       Route::post('deletetruck' , [TruckController::class , 'DeleteTruck']);
       Route::get('getemployees' , [EmployeeController::class , 'GetAllEmployees']);
       Route::get('getbranches', [BranchController::class , 'GetAllBranches'] );
       Route::get('gettrucks' , [TruckController::class , 'GetTrucks']);      
       Route::post('addvacationforemployee' , [VacationController::class , 'AddVacationForEmployee']);
       Route::post('addvacationforwmanager' , [VacationController::class , 'AddVacationForWhManager']);
       Route::post('getemployeevacation' , [VacationController::class , 'GetEmployeeVacation']);
       Route::post('getwmanagervacation' , [VacationController::class , 'GetWhManagerVacation']);
       Route::post('truckrecord' , [TruckController::class , 'GetTruckRecord']);       
       Route::post('truckinformation' , [TruckController::class , 'GetTruckInformation']);       
       Route::post('editpermissions' , [EmployeeController::class , 'EditPermissions']);


              });

                   /////////Employee///////  
              
Route::group(['middleware' => 'Employee',
              'prefix' => 'employee'], function() {
        Route::post('addtrip' , [TripController::class , 'AddTrip']);
        Route::post('addtripinvoice' , [TripController::class , 'AddTripInvoice']);
        Route::post('edittrip' , [TripController::class , 'EditTrip']);
        Route::post('canceltrip' , [TripController::class , 'CancelTrip']);
        Route::post('archiveData' , [TripController::class , 'ArchiveData']);
        Route::get('GetArchiveData' , [TripController::class , 'GetArchiveData']);         
        Route::get('getbranches' , [BranchController::class , 'GetBranches']);  
        Route::get('gettrips' , [TripController::class , 'GetAllTrips']);    
        Route::get('getallactivetrips' , [TripController::class , 'GetActiveTrips']);

        ////////////////emp adm BM////////////////////////////
        Route::get('gettrucks' , [TruckController::class , 'GetTrucks']);       
     Route::post('truckinformation' , [TruckController::class , 'GetTruckInformation']);       
     Route::post('truckrecord' , [TruckController::class , 'GetTruckRecord']);       

      
      });
              


              ///////////////Admin_APIs////////////////
 Route::group(['middleware' => 'Admin',
              'prefix' => 'admin'], function() {
         Route::post('addbranch' , [BranchController::class , 'AddBranch']);
         Route::post('addwarehouse' , [WarehouseController::class , 'addwarehouse']);
         Route::post('addwarehousemanager' , [WarehouseController::class , 'AddWarehouseManager']);
         Route::post('updatebranch', [BranchController::class , 'UpdateBranch'] );         
         Route::post('UpdateWarehouse', [WarehouseController::class , 'UpdateWarehouse'] );
         Route::post('deleteBranch', [BranchController::class , 'deleteBranch'] );
         Route::post('deleteWarehouse', [WarehouseController::class , 'deleteWarehouse'] );
         Route::get('getbranches', [BranchController::class , 'GetAllBranches'] );
         Route::post('getemployeevacation' , [VacationController::class , 'GetEmployeeVacation']);
         Route::post('getwmanagervacation' , [VacationController::class , 'GetWhManagerVacation']);
         Route::get('gettrucks' , [TruckController::class , 'GetTrucks']);       
         Route::post('truckrecord' , [TruckController::class , 'GetTruckRecord']);       
         Route::post('truckinformation' , [TruckController::class , 'GetTruckInformation']);       

     
     
     });



     Route::group(['middleware' => 'Customer',
     'prefix' => 'customer'], function() {

     });   

Route::group(['middleware' => 'WarehouseManager',
     'prefix' => 'warehousemanager'], function() {

     });  

