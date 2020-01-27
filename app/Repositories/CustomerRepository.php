<?php

namespace App\Repositories;

use DB;
use Exception;
use Log;

class CustomerRepository {
    public static function getAllCustomer()
    {
        $q = DB::table('customer')
            ->where('customer_active', '1')
            ->select('*')
            ->paginate(10);

        return $q;
    }

    public static function getCustomerById($id)
    {
        $q = DB::table('customer')
            ->join('user as cu', 'cu.user_id', 'customer_created_user_id')
            ->leftJoin('user as mu', 'mu.user_id', 'customer_modified_user_id')
            ->where('customer_active', '1')
            ->where('customer_id', $id)
            ->select('customer_id', 
                'customer_name'
                ,'customer_address'
                ,'customer_phone'
                ,'cu.user_name as created_user_name'
                ,'customer_created_date'
                ,'mu.user_name as modified_user_name'
                ,'customer_modified_date')
            ->first();

        return $q;
    }

    public static function save($inputs, $userLoginId)
    {
        $result = array('success' => false, 'errorMessages' => array(),'menu_id' =>null);
        $params = array(
            $inputs['customer_id'] ?: null,
            $inputs['customer_name']  ?: null,
            $inputs['customer_address']  ?: null,
            $inputs['customer_phone']  ?: null,
            $userLoginId 
        );
        
        $paramsQuery = implode(',', array_map(function ($val){ return '?'; }, $params));
        $resultRow = DB::selectOne('select * from customer_save(' . $paramsQuery . ')', $params);
        
        if (empty($resultRow)){
            array_push($result['errorMessages'], trans('messages.errorAssert'));
        } else {
            if (!empty($resultRow->errorcode)){
                array_push($result['errorMessages'], $resultRow->errorcode);
            } else {
                $result['success'] = true;
            } 
        }

        $result['customer_id'] = $resultRow->customer_id ?: $inputs['customer_id'];
        return $result;
    }

    public static function deleteCustomerById($id, $userLoginId)
    {
        $result = array('success' => false, 'errorMessages' => array());
        $params = array(
            $id,
            $userLoginId 
        );
        
        $paramsQuery = implode(',', array_map(function ($val){ return '?'; }, $params));
        $resultRow = DB::selectOne('select * from customer_delete(' . $paramsQuery . ')', $params);
        
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