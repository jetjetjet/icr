<?php

namespace App\Repositories;

use DB;
use Exception;
use Illuminate\Support\Facades\Hash;
use Log;
use Auth;

class UserRepository
{
    public static function getAllUsers()
    {
        $q = DB::table('user')
            ->where('user_active', '1')
            ->orderBy('user_id')
            ->select('user_id'
                ,'user_role_id'
                ,'user_name'
                ,'user_fullname'
                ,'user_address'
                ,'user_phone'
                , 'user_last_login')
            ->paginate(10);
        return $q;
    }

    public static function getUserById($id)
    {
        $q = DB::table('user')
            ->join('role', 'user_role_id', 'role_id')
            ->join('user as cu', 'cu.user_id', 'user_created_user_id')
            ->leftJoin('user as mu', 'mu.user_id', 'user_modified_user_id')
            ->where('user_active', '1')
            ->where('user_id', $id)
            ->select('user_id'
                ,'user_name'
                ,'role.role_name as user_role_name'
                ,'user_role_id'
                ,'user_fullname'
                ,'user_phone'
                ,'user_last_login'
                ,'cu.user_name as user_created_name'
                ,'user_created_date'
                ,'mu.user_name as user_modified_name'
                ,'user_modified_date'
            )
            ->first();
        return $q;
    }

    public static function save($inputs, $userLoginId)
    {
        $result = array('success' => false, 'errorMessages' => array(),'id' =>null);
        $params = array(
            $inputs['user_id'] ?: null,
            $inputs['user_role_id'] ?: null,
            $inputs['user_name']  ?: null,
            Hash::make($inputs['user_password']) ?: null,   
            $inputs['user_fullname']  ?: null,
            $inputs['user_address']  ?: null,
            $inputs['user_phone']  ?: null,
            $userLoginId 
        );
        
        $paramsQuery = implode(',', array_map(function ($val){ return '?'; }, $params));
        $resultRow = DB::selectOne('select * from user_save(' . $paramsQuery . ')', $params);
        
        if (empty($resultRow)){
            array_push($result['errorMessages'], trans('messages.errorAssert'));
        } else {
            if (!empty($resultRow->errorcode)){
                array_push($result['errorMessages'], $resultRow->errorcode);
            } else {
                $result['success'] = true;
            } 
        }

        $result['user_id'] = $resultRow->insertid ?: $inputs['user_id'];
        return $result;
    }

    public static function getById($id)
    {
        $modelDb = DB::table('user')
            ->where('user_active','1')
            ->where('user_id',$id)
            ->select(
                'user.*',
            )->first();

        return $modelDb;
    }

    public function deleteUserById($id, $userLoginId)
    {
        $result = array('success' => false, 'errorMessages' => array());
        $params = array(
            $id,
            $userLoginId 
        );
        
        $paramsQuery = implode(',', array_map(function ($val){ return '?'; }, $params));
        $resultRow = DB::selectOne('select * from user_delete(' . $paramsQuery . ')', $params);
        
        if (empty($resultRow)){
            array_push($result['errorMessages'], trans('messages.errorAssert'));
        } else {
            if (!empty($resultRow->errorcode)){
                array_push($result['errorMessages'], $resultRow->errorcode);
            } else {
                $result['success'] = true;
            } 
        }
        return $result;
    }

}