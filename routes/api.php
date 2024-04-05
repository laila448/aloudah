<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post( 'register' , [AuthController::class , 'Register']);
Route::post( 'login' , [AuthController::class , 'Login']);


Route::group(['midlleware' => ['auth','UserType:admin'],
              'prefix' => 'admin'], function() {
         Route::post('addbranchmanager' , [AuthController::class , 'AddBranchManager']);
              });


Route::group(['midlleware' => ['auth','UserType:branch-manager'],
              'prefix' => 'branchmanager'], function() {
         Route::get('test' , [AuthController::class , 'test']);
              });
