<?php

namespace App\Repositories;

use DB;
use Exception;
use Illuminate\Support\Facades\Hash;
use Log;
use Auth;

class RoleRepository
{
    public static function getAllRole()
    {
        $data = new \stdClass();
        $q = DB::table('role')
            ->where('role_active', '1');
			
		// if($filter->search){
			// foreach($filter->search as $qCol){
				// $sCol = explode('|', $qCol);
				// $fCol = str_replace('"', '', $sCol[0]);
				// $q = $q->where($sCol[0], 'like', '%'.$sCol[1].'%');
			// }
		// }
		
        // $cGet = $q->select(DB::raw('count (1)'))->first();
		
		// if ($filter->sortColumns){
			// $order = $filter->sortColumns[0];
			// $q = $q->orderBy($order->column, $order->order);
		// } else {
			// $q = $q->orderBy('role_created_date');
		// }
		
		// $q = $q->skip($filter->offset);
		// $q = $q->take($filter->limit);
        $qGet = $q->select('role_id', 'role_name', 'role_description')->get();
        // $data->count = $cGet->count;
        $data->data = $qGet;

        return $data;
    }

    private function mapFilter($q, $filter){
        $f = new \stdClass();
        $orderBy = $filter->orderBy !=null ? $filter->orderBy : null;

        if ($orderBy){

        } else {
            $q->orderBy('role_created_date');
        }
    }

    public static function getRoleById($id)
    {
        $q = DB::table('role')
            ->leftJoin('user as cu', 'cu.user_id', 'role_created_user_id')
            ->leftJoin('user as mu', 'mu.user_id', 'role_modified_user_id')
            ->where('role_active', '1')
            ->where('role_id', $id)
            ->select('role_id'
                ,'role_name'
                ,'role_description'
                ,'cu.user_name as role_created_name'
                ,'role_created_date'
                ,'mu.user_name as role_modified_name'
                ,'role_modified_date')
            ->first();
        return $q;
    }

    public static function getRoleName($id)
    {
        $q = DB::table('role')
            ->where('role_active', '1')
            ->where('role_id', $id)
            ->select('role_name')
            ->first();
        return $q;
    }

    public static function save($filter, $loginId)
    {
        $result = array('success' => false, 'errorMessages' => array(), 'debugMessages' => array(),'id' =>null);

        $getId = isset($filter['id']) ? $filter['id'] : null ;
        $oldData = RoleRepository::getRoleById($getId);

        $params = array(
            $getId,
            isset($filter['role_name']) ? $filter['role_name'] : $oldData->role_name ,
            isset($filter['role_description']) ? $filter['role_description'] : $oldData->role_description,
            $loginId 
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
        $result['id'] = $resultRow->insertid ?: $getId;

        return $result;
    }

    public static function deleteById($id, $loginId)
    {
        $result = array('success' => false, 'errorMessages' => array());
        $params = array(
            $id,
            $loginId 
        );
        
        $paramsQuery = implode(',', array_map(function ($val){ return '?'; }, $params));
        $resultRow = DB::selectOne('select * from role_delete(' . $paramsQuery . ')', $params);
        
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