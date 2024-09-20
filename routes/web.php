<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\Web\BranchController;
use App\Http\Controllers\Web\CustomerController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\DriverController;
use App\Http\Controllers\Web\EmployeeController;
use App\Http\Controllers\Web\ReportController;
use App\Http\Controllers\Web\tripController;
use App\Http\Controllers\Web\truckController;
use App\Http\Controllers\Web\WarehouseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});
//////reset paasword //////
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('updatepassword' , [ResetPasswordController::class , 'ResetPassword'])->name('password.update');
Route::get('password/reset/result', function () {
    return view('mail.resetresult');
})->name('password.reset.result');

Route::get('/empty', [App\Http\Controllers\HomeController::class, 'empty'])->name('empty');


//Auth::routes();


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


    Route::group(['prefix' => 'admin'], function() {
    Route::get('/login', [App\Http\Controllers\Auth\AdminController::class, 'showLoginFormadmin'])->name('admin2login');
    Route::post('/login', [App\Http\Controllers\Auth\AdminController::class, 'loginadmin'])->name('adminlogin');
    Route::post('/logout',[App\Http\Controllers\Auth\AdminController::class, 'logoutadmin'])->name('adminlogout');
    
     });

     Route::get('/indexadmin', [App\Http\Controllers\AdminController::class, 'index'])->name('indexadmin');



Route::group(['prefix' => 'employee'], function() {
Route::get('/login', [App\Http\Controllers\Auth\AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/login', [App\Http\Controllers\Auth\AdminLoginController::class, 'login'])->name('login');
Route::post('/logout',[App\Http\Controllers\Auth\AdminLoginController::class, 'logout'])->name('logout');

 });
 Route::get('/index', [App\Http\Controllers\AdminController::class, 'index'])->name('index');

 
Route::group(['middleware' => 'EmployeeAuthMiddleware',
 'prefix' => 'employee'], function() { 
Route::get('/truckslist' , [truckController::class , 'GetTrucks']);
Route::post('/trips', [truckController::class, 'showtrucktrips'])->name('trips.store');
   
Route::post('/addtruck' , [truckController::class , 'AddTruck'])->name('addtruck');
Route::post('/deletetruck' , [truckController::class , 'DeleteTruck'])->name('deletetruck');
Route::post('/edittruck' , [truckController::class , 'EditTruck'])->name('edittruck');

Route::get('/tripslist' , [tripController::class , 'GetTrips'])->name('tripslist');
Route::get('/temporarytrips' , [tripController::class , 'GetTemporaryTrips']);
Route::get('/closedtrips' , [tripController::class , 'GetClosedTrips']);
Route::post('/archivetrip', [tripController::class, 'ArchiveTrip'])->name('archivetrip');
Route::get('/archivedtrips' , [tripController::class , 'GetArchiveTrips']);
Route::post('/edittrip' , [tripController::class , 'EditTrip'])->name('edittrip');
Route::post('/archivetrip' , [tripController::class , 'ArchiveTrip'])->name('archivetrip');
Route::post('/deletetrip' , [tripController::class , 'DeleteTrip'])->name('deletetrip');
Route::get('/manifests' , [tripController::class , 'GetManifests'])->name('manifests');
Route::post('/manifestinformation' , [tripController::class , 'GetManifestinformation'])->name('manifestinformation');



Route::post('/addbranch' , [BranchController::class , 'AddBranch'])->name('addbranch');
Route::post('/editbranch' , [BranchController::class , 'EditBranch'])->name('editbranch');
Route::post('/addbranchmanager' , [BranchController::class , 'AddBranchManager'])->name('addbranchmanager');
Route::get('/getallbranches' , [BranchController::class , 'getBranches'])->name('getallbranches');
Route::get('/getallmanagers' , [BranchController::class , 'GetAllManagers'])->name('getallmanagers');
Route::post('/deletebranch' , [BranchController::class , 'DeleteBranch'])->name('deletebranch');
Route::post('/editbranchmanager' , [BranchController::class , 'EditBranchManager'])->name('editbranchmanager');
Route::post('/deletebranchmanager' , [BranchController::class , 'DeleteBranchManager'])->name('deletebranchmanager');
Route::post('/branchemp', [BranchController::class, 'ShowBranchEmp'])->name('branch.emp');
Route::post('/PromoteEmp', [BranchController::class, 'PromoteEmployee'])->name('PromoteEmp');
Route::post('/PromoteEmpwh', [BranchController::class, 'PromoteEmployeeWH'])->name('PromoteEmpwh');




Route::get('/getAllWarehouses' , [WarehouseController::class , 'getAllWarehouses'])->name('getAllWarehouses');
Route::get('/getallwmanagers' , [WarehouseController::class , 'getAllWarehousesM'])->name('getallwmanagers');

Route::post('/addwmanager' , [WarehouseController::class , 'Addwmanager'])->name('addwmanager');
Route::post('/editwmanager' , [WarehouseController::class , 'editwmanager'])->name('editwmanager');
Route::post('/deletemanager' , [WarehouseController::class , 'deletewmanager'])->name('deletemanager');

Route::post('/addwarehouse' , [WarehouseController::class , 'addwarehouse'])->name('addwarehouse');
Route::post('/editwarehouse' , [WarehouseController::class , 'editwarehouse'])->name('editwarehouse');
Route::post('/deletewarehouse' , [WarehouseController::class , 'deletewarehouse'])->name('deletewarehouse');

Route::get('/getallemployees' , [EmployeeController::class , 'GetAllEmployees'])->name('getallemployees');
Route::get('/searchemployees' , [EmployeeController::class , 'searchemployees'])->name('searchemployees');

Route::get('/getdrivers' , [DriverController::class , 'GetDrivers'])->name('getdrivers');
Route::post('/deletedriver' , [DriverController::class , 'DeleteDriver'])->name('deletedriver');
Route::post('/editdriver' , [DriverController::class , 'EditDriver'])->name('editdriver');


Route::get('/getcustomers' , [CustomerController::class , 'GetCustomers'])->name('getcustomers');
Route::post('/addcustomer' , [CustomerController::class , 'AddCustomer'])->name('addcustomer');
Route::post('/editcustomer' , [CustomerController::class , 'EditCustomer'])->name('editcustomer');
Route::post('/deletecustomer' , [CustomerController::class , 'DeleteCustomer'])->name('deletecustomer');


Route::get('/getcompliant' , [CustomerController::class , 'getCompliant'])->name('getcompliant');


Route::get('/getmyprofile' , [BranchController::class , 'getmyprofile'])->name('getmyprofile');

Route::post('/editprofile' , [BranchController::class , 'editprofile'])->name('editprofile');

Route::get('/getprices' , [BranchController::class , 'getprices'])->name('getprices');
Route::post('/addprice' , [BranchController::class , 'addprice'])->name('addprice');
Route::post('/editprice' , [BranchController::class , 'editprice'])->name('editprice');
Route::post('/deleteprice' , [BranchController::class , 'deleteprice'])->name('deleteprice');


Route::get('/reports' , [ReportController::class , 'Reports'])->name('reports');
Route::post('/generate-report', [ReportController::class, 'generateReport'])->name('generateReport');
Route::post('/export-report', [ReportController::class, 'exportReportToPDF'])->name('exportReport');

});
