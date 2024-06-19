<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Web\BranchController;
use App\Http\Controllers\Web\CustomerController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\DriverController;
use App\Http\Controllers\Web\tripController;
use App\Http\Controllers\Web\truckController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


//Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

 Route::group(['prefix' => 'employee'], function() {
Route::get('/login', [App\Http\Controllers\Auth\AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/login', [App\Http\Controllers\Auth\AdminLoginController::class, 'login'])->name('login');
Route::post('/logout',[App\Http\Controllers\Auth\AdminLoginController::class, 'logout'])->name('logout');

 });
 Route::get('/index', [App\Http\Controllers\AdminController::class, 'index'])->name('index');

 
 Route::group(['middleware' => 'EmployeeAuthMiddleware',
 'prefix' => 'employee'], function() { 
Route::get('/truckslist' , [truckController::class , 'GetTrucks']);
Route::post('/addtruck' , [truckController::class , 'AddTruck'])->name('addtruck');
Route::post('/deletetruck' , [truckController::class , 'DeleteTruck'])->name('deletetruck');
Route::post('/edittruck' , [truckController::class , 'EditTruck'])->name('edittruck');

Route::get('/tripslist' , [tripController::class , 'GetTrips']);
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
Route::get('/getallbranches' , [BranchController::class , 'getBranches'])->name('getallbranches');
Route::get('/getallmanagers' , [BranchController::class , 'GetAllManagers'])->name('getallmanagers');
Route::post('/deletebranch' , [BranchController::class , 'DeleteBranch'])->name('deletebranch');
Route::post('/editbranchmanager' , [BranchController::class , 'EditBranchManager'])->name('editbranchmanager');
Route::post('/deletebranchmanager' , [BranchController::class , 'DeleteBranchManager'])->name('deletebranchmanager');


Route::get('/getdrivers' , [DriverController::class , 'GetDrivers'])->name('getdrivers');
Route::post('/deletedriver' , [DriverController::class , 'DeleteDriver'])->name('deletedriver');
Route::post('/editdriver' , [DriverController::class , 'EditDriver'])->name('editdriver');


Route::get('/getcustomers' , [CustomerController::class , 'GetCustomers'])->name('getcustomers');
Route::post('/addcustomer' , [CustomerController::class , 'AddCustomer'])->name('addcustomer');
Route::post('/editcustomer' , [CustomerController::class , 'EditCustomer'])->name('editcustomer');
Route::post('/deletecustomer' , [CustomerController::class , 'DeleteCustomer'])->name('deletecustomer');



});
 
 
 // Route::group(['middleware' => 'Admin',
// 'prefix' => 'admin'], function() {
//     Route::get('/{page}', [App\Http\Controllers\AdminController::class, 'index']);


// });