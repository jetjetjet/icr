<?php

namespace App\Repositories;

use DB;
use Exception;
use Log;

class CategoryRepository {
    public static function getAllCategory()
    {
        $q = DB::table('ctg')
            ->where('ctg_active', '1')
            ->select('*')
            ->paginate(10);

        return $q;
    }

    public static function getCategoryById($id)
    {
        $q = DB::table('ctg')
            ->join('user as cu', 'cu.user_id', 'ctg_created_user_id')
            ->leftJoin('user as mu', 'mu.user_id', 'ctg_modified_user_id')
            ->where('ctg_active', '1')
            ->where('ctg_id', $id)
            ->select('ctg_id' 
                ,'ctg_name'
                ,'ctg_detail'
                ,'ctg_price'
                ,'ctg_est_days'
                ,'cu.user_name as created_user_name'
                ,'ctg_created_date'
                ,'mu.user_name as modified_user_name'
                ,'ctg_modified_date')
            ->first();

        return $q;
    }

    public static function save($inputs, $userLoginId)
    {
        $result = array('success' => false, 'errorMessages' => array(),'menu_id' =>null);
        $params = array(
            $inputs['ctg_id'] ?: null,
            $inputs['ctg_name']  ?: null,
            $inputs['ctg_detail']  ?: null,
            $inputs['ctg_price']  ?: null,
            $inputs['ctg_est_days']  ?: null,
            $userLoginId 
        );
        
        $paramsQuery = implode(',', array_map(function ($val){ return '?'; }, $params));
        $resultRow = DB::selectOne('select * from ctg_save(' . $paramsQuery . ')', $params);
        
        if (empty($resultRow)){
            array_push($result['errorMessages'], trans('messages.errorAssert'));
        } else {
            if (!empty($resultRow->errorcode)){
                array_push($result['errorMessages'], $resultRow->errorcode);
            } else {
                $result['success'] = true;
            } 
        }

        $result['ctg_id'] = $resultRow->ctg_id ?: $inputs['ctg_id'];
        return $result;
    }

    public static function deletectgById($id, $userLoginId)
    {
        $result = array('success' => false, 'errorMessages' => array());
        $params = array(
            $id,
            $userLoginId 
        );
        
        $paramsQuery = implode(',', array_map(function ($val){ return '?'; }, $params));
        $resultRow = DB::selectOne('select * from ctg_delete(' . $paramsQuery . ')', $params);
        
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