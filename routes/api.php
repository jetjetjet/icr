<?php

use Illuminate\Http\Request;

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
Route::group(['middleware' => ['api-header']], function() {
Route::post('login', 'AuthController@authenticate');
Route::group(['middleware' => ['jwt-auth:api']], function() {
    Route::get('auth/user', 'AuthController@getAuthenticatedUser');
    
    Route::post('user/delete/{user_id?}', 'UserController@deleteUserById');
    Route::post('user/save', 'UserController@postSaveUser');
    Route::get('user/getAll', 'UserController@getAllUsers');
    Route::get('user/get/{user_id?}', 'UserController@getUserById');

    Route::post('menu/save', 'MenuController@postSaveMenu');
    Route::post('menu/delete/{menu_id?}', 'RoleController@deleteMenu');
    Route::get('menu/getAll', 'MenuController@getAllMenu');
    Route::get('menu/get/{menu_id?}', 'MenuController@getMenuById');
    Route::get('menu/getMenuTree', 'MenuController@getUserMenu');

    Route::get('role/getAll', 'RoleController@getAllRole');
    Route::get('role/get/{role_id?}', 'RoleController@getRoleById');
    Route::post('role/delete/{role_id?}', 'RoleController@deleteRole');
    Route::post('role/save', 'RoleController@saveRole');

    Route::get('customer/getAll', 'CustomerController@getAllCustomer');
    Route::get('customer/get/{customer_id?}', 'CustomerController@getCustomerById');
    Route::post('customer/delete/{customer_id?}', 'CustomerController@deleteCustomer');
    Route::post('customer/save', 'CustomerController@postSaveCustomer');

    });
});