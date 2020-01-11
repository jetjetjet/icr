<?php

namespace App\Repositories;

use DB;
use Exception;
use Illuminate\Support\Facades\Hash;
use Log;
use Auth;

class UserRepository
{
    public static function getAllUsers(){
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

    public static function getById($id){
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
        $ui->user_name = isset($db->user_name) ? $db->user_name : null;
        $ui->user_fullname = isset($db->user_fullname) ? $db->user_fullname : null;
        $ui->user_address = isset($db->user_addres) ? $db->user_address : null;
        $ui->user_phone = isset($db->user_phone) ? $db->user_phone : null;

        $ui->userlastlogin = isset($db->user_last_login) ? $db->user_last_login : null;

        return $ui;
    }
}