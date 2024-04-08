<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
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
         Route::get('test' , [AuthController::class , 'test']);
              });

Route::group(['middleware' => 'Customer',
              'prefix' => 'customer'], function() {
        
              });   
              
Route::group(['middleware' => 'Employee',
              'prefix' => 'employee'], function() {
        
              });  

Route::group(['middleware' => 'WarehouseManager',
              'prefix' => 'warehousemanager'], function() {
        
              });  

              ///////////////Admin_APIs////////////////
Route::group(['middleware' => 'Admin',
              'prefix' => 'admin'], function() {
         //Route::post('addbranchmanager' , [AuthController::class , 'AddBranchManager']);
         Route::post('addbranch' , [AdminController::class , 'AddBranch']);
         Route::post('addwarehouse' , [AdminController::class , 'addwarehouse']);

         Route::post('updatebranch', [AdminController::class , 'UpdateBranch'] );
         
         Route::post('UpdateWarehouse', [AdminController::class , 'UpdateWarehouse'] );

         Route::post('deleteBranch', [AdminController::class , 'deleteBranch'] );
         Route::post('deleteWarehouse', [AdminController::class , 'deleteWarehouse'] );


     
     
     });












