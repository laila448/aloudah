<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Web\DashboardController;
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

 
 Route::prefix('employee')->middleware('check.emp_web')->group(function () {
Route::get('/truckslist' , [truckController::class , 'GetTrucks']);
Route::post('/addtruck' , [truckController::class , 'AddTruck'])->name('addtruck');
Route::post('/deletetruck' , [truckController::class , 'DeleteTruck'])->name('deletetruck');
Route::post('/edittruck' , [truckController::class , 'EditTruck'])->name('edittruck');

Route::get('/tripslist' , [tripController::class , 'GetTrips']);

});
 
 
 // Route::group(['middleware' => 'Admin',
// 'prefix' => 'admin'], function() {
//     Route::get('/{page}', [App\Http\Controllers\AdminController::class, 'index']);


// });