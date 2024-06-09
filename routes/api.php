<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\BranchManagerController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\compliantController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\TruckController;
use App\Http\Controllers\VacationController;
use App\Http\Controllers\WarehouseController;
use App\Models\Branch;
use App\Models\Complaint;
use App\Models\Employee;
use App\Models\Shipping;
use App\Models\Truck;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use PHPOpenSourceSaver\JWTAuth\Claims\Custom;
use App\Http\Controllers\DriverController;

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
Route::post('logout' , [AuthController::class , 'Logout']);





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
       Route::get('getemployeevacation/{id}' , [VacationController::class , 'GetEmployeeVacation']);
       Route::get('getwmanagervacation/{id}' , [VacationController::class , 'GetWhManagerVacation']);      
       Route::post('editpermissions' , [EmployeeController::class , 'EditPermissions']);
       Route::get('truckrecord/{desk}' , [TruckController::class , 'GetTruckRecord']);       
       Route::get('truckinformation/{truck_number}' , [TruckController::class , 'GetTruckInformation']);   
       Route::get('getcustomers', [CustomerController::class , 'GetCustomers']);
       Route::post('shippingprices', [ShippingController::class , 'DetermineShippingPrices']);
       Route::post('editshippingprices', [ShippingController::class , 'EditShippingPrices']);
       Route::get('priceslist', [ShippingController::class , 'GetPricesList']);
       Route::post('addDriver', [EmployeeController::class, 'addDriver']);
       Route::get('branchdrivers' , [EmployeeController::class , 'GetDriversForMyBranch']);



              });

                   /////////Employee///////  
              
Route::group(['middleware' => 'Employee',
              'prefix' => 'employee'], function() {
        Route::post('addtrip' , [TripController::class , 'AddTrip']);
        Route::post('addinvoice' , [ShippingController::class , 'AddInvoice']);
        Route::post('edittrip' , [TripController::class , 'EditTrip']);
        Route::post('canceltrip' , [TripController::class , 'CancelTrip']);
        Route::post('archiveData' , [TripController::class , 'ArchiveData']);
        Route::get('GetArchiveData' , [TripController::class , 'GetArchiveData']);         
        Route::get('getbranches' , [BranchController::class , 'GetBranches']);  
        Route::get('gettrips' , [TripController::class , 'GetAllTrips']);    
        Route::get('getallactivetrips' , [TripController::class , 'GetActiveTrips']);
        Route::post('addcustomer' , [CustomerController::class , 'AddCustomer']);
        Route::post('updatecustomer'  ,[CustomerController::class , 'UpdateCustomer']);
        Route::post('deletecustomer' , [CustomerController::class , 'DeleteCustomer']);
        Route::post('getcustomer' , [CustomerController::class , 'GetCustomer']);
        Route::get('gettrucks' , [TruckController::class , 'GetTrucks']);       
        Route::get('truckrecord/{desk}' , [TruckController::class , 'GetTruckRecord']);       
        Route::get('truckinformation/{truck_number}' , [TruckController::class , 'GetTruckInformation']);            
        Route::get('priceslist', [ShippingController::class , 'GetPricesList']);
        Route::get('allreceipts/{destination_id}', [ShippingController::class , 'GetAllRceipts']);//الايصالات
        Route::post('addcompliant', [compliantController::class , 'AddCompliantEmp']);
        Route::get('GetTripInformation/{trip_number}' , [TripController::class , 'GetTripInformation']);    
        Route::get('getManifest/{manifest_number}' , [ShippingController::class , 'GetManifestWithInvoices']);    
        Route::post('updatemanifest', [ShippingController::class , 'UpdateManifest']);
        Route::post('createtripreport', [ReportController::class , 'CreateTripReport']);
        Route::post('createtruckreport', [ReportController::class , 'CreateTruckReport']);
        Route::post('createempreport', [ReportController::class , 'CreateEmpReport']);
        Route::get('reports/{reportId}/download', [ReportController::class, 'downloadTruckReport']);
        Route::get('tripreports/{reportId}/download', [ReportController::class, 'downloadTripReport']);
        Route::get('alltrucksreports', [ReportController::class, 'getTruckReports']);
        Route::get('alltripsreports', [ReportController::class, 'getTripReports']);
        Route::get('getbranchlatlng/{id}', [BranchController::class, 'getBranchlatlng']);
        Route::get('getdrivertrips/{id}', [DriverController::class, 'GetDriverTrips']);



      });
              


              ///////////////Admin_APIs////////////////
 Route::group(['middleware' => 'Admin',
              'prefix' => 'admin'], function() {
         Route::post('addbranch' , [BranchController::class , 'AddBranch']);
         Route::post('addbranchmanager' , [BranchController::class , 'AddBranchManager']);
         Route::post('addwarehouse' , [WarehouseController::class , 'addwarehouse']);
         Route::post('addwarehousemanager' , [WarehouseController::class , 'AddWarehouseManager']);
         Route::post('updatebranch', [BranchController::class , 'UpdateBranch'] );         
         Route::post('UpdateWarehouse', [WarehouseController::class , 'UpdateWarehouse'] );
         Route::post('deleteBranch', [BranchController::class , 'deleteBranch'] );
         Route::post('deleteWarehouse', [WarehouseController::class , 'deleteWarehouse'] );
         Route::get('getbranches', [BranchController::class , 'GetAllBranches'] );
         Route::get('getemployeevacation/{id}' , [VacationController::class , 'GetEmployeeVacation']);
         Route::get('getwmanagervacation/{id}' , [VacationController::class , 'GetWhManagerVacation']);
         Route::get('gettrucks' , [TruckController::class , 'GetTrucks']);       
         Route::get('truckrecord/{desk}' , [TruckController::class , 'GetTruckRecord']);       
         Route::get('truckinformation/{truck_number}' , [TruckController::class , 'GetTruckInformation']);       
         Route::get('gettrips' , [TripController::class , 'GetAllTrips']);    
         Route::get('getallactivetrips' , [TripController::class , 'GetActiveTrips']);
         Route::get('GetArchiveData' , [TripController::class , 'GetArchiveData']);         
         Route::get('GetTripInformation/{trip_number}' , [TripController::class , 'GetTripInformation']);    
         Route::get('getemployees' , [EmployeeController::class , 'GetEmployees']);
         Route::post('promoteemployee' , [EmployeeController::class , 'PromoteEmployee']);
         Route::get('getemployee/{id}' , [EmployeeController::class , 'GetEmployee']);
         Route::get('getbranchemployees/{id}' , [EmployeeController::class , 'GetBranchEmployees']);
         Route::get('getwarehouses'  ,[WarehouseController::class , 'GetWarehouses']);
         Route::get('getwarehousemanager/{id}' , [WarehouseController::class , 'GetWarehouseManager']);
         Route::post('getcustomer' , [CustomerController::class , 'GetCustomer']);
         Route::get('getcustomers', [CustomerController::class , 'GetCustomers']);
     
     
     });



     Route::group(['middleware' => 'Customer',
     'prefix' => 'customer'], function() {
          Route::post('addcompliant', [compliantController::class , 'AddCompliant']);
     });   

     Route::group(['middleware' => 'Driver',
     'prefix' => 'driver'], function() {
          Route::get('getprofile', [DriverController::class , 'GetProfile']);
          Route::get('getbranchlatlng/{id}', [BranchController::class, 'getBranchlatlng']);
          Route::get('getmytrips', [DriverController::class, 'GetMyTrips']);
  

     });   

Route::group(['middleware' => 'WarehouseManager',
     'prefix' => 'warehousemanager'], function() {

     });  

