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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::group(['middleware' => ['jwt.auth','api-header']], function () {
  
//     // all routes to protected resources are registered here  
//     Route::get('users/list', function(){
//         $users = App\User::all();
        
//         $response = ['success'=>true, 'data'=>$users];
//         return response()->json($response, 201);
//     });
// });
// Route::group(['middleware' => 'api-header'], function () {
  
//     // The registration and login requests doesn't come with tokens 
//     // as users at that point have not been authenticated yet
//     // Therefore the jwtMiddleware will be exclusive of them
//     Route::post('user/login', 'UserController@login');
//     Route::post('user/auth', 'UserController@getToken');
//     Route::post('user/register', 'UserController@register');
// });

// //Route::post('user/register', 'UserController@register');

Route::post('register', 'UserController@register');
Route::get('open', 'RoleController@open');

Route::group(['middleware' => ['api-header']], function() {
    
Route::post('login', 'AuthController@authenticate');
Route::post('user/saveUser', 'UserController@postSaveUser');
Route::group(['middleware' => ['jwt-auth:api']], function() {
    Route::get('auth/user', 'AuthController@getAuthenticatedUser');
    
    Route::post('user/deleteUser/{user_id?}', 'UserController@deleteUserById');
    Route::get('user/getAll', 'UserController@getAllUsers');
    Route::get('user/get/{user_id?}', 'UserController@getUserById');

    Route::post('menu/saveMenu', 'MenuController@postSaveMenu');
    Route::get('menu/getMenuTree', 'MenuController@getUserMenu');
    });
});