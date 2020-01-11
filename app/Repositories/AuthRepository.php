<?php

namespace App\Repositories;

use DB;
use Exception;
use Illuminate\Support\Facades\Hash;
use Log;
use Auth;

class AuthRepository
{
    private function getToken($username, $password)
    {
        $token = null;
        $credentials = $request->only('user_name', 'user_password');
        try {
            if (!$token = JWTAuth::attempt( ['user_name'=>$username, 'password'=>$password])) {
                return response()->json([
                    'response' => 'error',
                    'message' => 'Password or email is invalid',
                    'token'=>$token
                ]);
            }
        } catch (JWTAuthException $e) {
            return response()->json([
                'response' => 'error',
                'message' => 'Token creation failed',
            ]);
        }
        return $token;
    }

    public static function authenticate($username, $plainPassword)
    {
        $user_id = null;
        
        try {
            $user = DB::selectOne('
                SELECT user_id, user_password FROM "user" 
                WHERE user_active = \'1\'
                AND user_name = ?
            ', [
               $username
            ]);
            if(!empty($user) && Hash::check($plainPassword, $user->user_password)) {
                $user_id = $user->user_id;
            }

        } catch (Exception $e){
            Log::error($e);
        }

        return $user_id;
    }

    public static function get($id){
        $modelDb = DB::table('user')
            ->where('user_active','1')
            ->where('user_id',$id)
            ->select(
                'user.*',
            )->first();

        $model = self::map($modelDb);

        return $model;
    }

    public static function map($db)
    {
        $ui = new \stdClass();
        if (!isset($db)){
            $db = new \stdClass();           
        }

        $ui->user_id = isset($db->user_id) ? $db->user_id : 0;
        $ui->user_password = isset($db->user_password) ? $db->user_password : null;
        $ui->user_name = isset($db->user_name) ? $db->user_name : null;

        return $ui;
    }
}