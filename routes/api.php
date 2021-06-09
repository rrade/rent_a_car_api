<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CountryController;
use App\Http\Controllers\API\LocationController;
use App\Http\Controllers\API\PhotoController;
use App\Http\Controllers\API\ReservationController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\VehicleController;
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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});
Route::group([

    'middleware' => 'api',
    'prefix' => 'auth',


], function ($router) {

    Route::post('login', [AuthController::class,'login']);
    Route::post('change-password', [AuthController::class,'changePassword']);
    Route::post('logout', [AuthController::class,'logout']);
    Route::post('refresh', [AuthController::class,'refresh']);
    Route::post('me', [AuthController::class,'me']);


});

Route::group([
    'middleware' => ['api','api.guard'],
], function ($router) {
    //reservations
    Route::get('reservations', [ReservationController::class,'index']);
    Route::get('cars-available', [ReservationController::class,'avaliable']);
    Route::post('reservation-store', [ReservationController::class,'store']);
    Route::get('reservation-show/{id}', [ReservationController::class,'show']);
    Route::post('reservation-update/{id}', [ReservationController::class,'update']);
    Route::delete('reservation-delete/{id}', [ReservationController::class,'destroy']);

    //users
    Route::get('users', [UserController::class,'index']);
    Route::post('user-store', [UserController::class,'store'])->name('user-store');
    Route::get('user-show/{id}', [UserController::class,'show']);
    Route::post('user-update/{id}', [UserController::class,'update'])->name('user-update');
    Route::delete('user-delete/{id}', [UserController::class,'destroy']);

    //vehicles
    Route::get('vehicles', [VehicleController::class,'index']);
    Route::post('vehicle', [VehicleController::class,'store'])->name('vehicle-store');
    Route::get('vehicle-show/{id}', [VehicleController::class,'show']);
    Route::post('vehicle-update/{id}', [VehicleController::class,'update'])->name('vehicle-update');
    Route::delete('vehicle-delete/{id}', [VehicleController::class,'destroy']);

    //Photo
    Route::delete('photo-delete/{id}', [PhotoController::class,'destroy']);

    //Country
    Route::get('countries', [CountryController::class,'index']);
    //Locations
    Route::get('locations', [LocationController::class,'index']);
});


