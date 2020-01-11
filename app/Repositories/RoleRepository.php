<?php

namespace App\Repositories;

use DB;
use Exception;
use Illuminate\Support\Facades\Hash;
use Log;
use Auth;

class RoleRepository
{
    public static function get()
    {
        $q = DB::table('role')
            ->where('role_active','1')
            ->select(
                '*',
            )->first();

        $model = self::map($modelDb);

        return $model;
    }

    public static function save($filter)
    {
        $result = array('success' => false, 'errorMessages' => array(), 'debugMessages' => array(),'id' =>null);

        $params = array(
            null,
            $filter->username ?: null,
            Hash::make($filter->password) ? : null,   
            1 
        );

        $paramsQuery = implode(',', array_map(function ($val){ return '?'; }, $params));
        $resultRow = DB::selectOne('select * from role_save(' . $paramsQuery . ')', $params);
        
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

    public static function map($db)
    {
        $ui = new \stdClass();
        if (!isset($db)){
            $db = new \stdClass();           
        }

        $ui->role_id = isset($db->role_id) ? $db->role_id : 0;
        $ui->role_name = isset($db->role_name) ? $db->rolen_ame : null;
        $ui->role_description = isset($db->role_description) ? $db->role_description : null; 

        return $ui;
    }
}