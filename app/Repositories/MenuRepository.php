<?php

namespace App\Repositories;

use DB;
use Exception;
use Log;
use Auth;

class MenuRepository {

    public static function getAllMenu()
    {
        $q = DB::table('menu')
            ->where('menu_active', '1')
            ->select('*')
            ->paginate(10);

        return $q;
    }

    public static function getMenuById($id)
    {
        $q = DB::table('menu')
            ->join('user as cu', 'cu.userid', 'menu_created_user_id')
            ->join('user as mu', 'mu.userid', 'menu_modified_user_id')
            ->where('menu_active', '1')
            ->select('menu_id', 
                'menu_is_parent'
                ,'menu_parent_id'
                ,'menu_name'
                ,'menu_url'
                ,'menu_icon'
                ,'cu.user_name as created_user_name'
                ,'menu_created_date'
                ,'mu.user_name as modified_user_name'
                ,'menu_modified_date')
            ->first();

        return $q;
    }

    public static function save($inputs, $userLoginId)
    {
        $result = array('success' => false, 'errorMessages' => array(),'menu_id' =>null);
        $params = array(
            $inputs['menu_id'] ?: null,
            $inputs['menu_parent_id']  ?: null,
            $inputs['menu_name']  ?: null,
            $inputs['menu_url']  ?: null,
            $inputs['menu_icon']  ?: null,
            $userLoginId 
        );
        
        $paramsQuery = implode(',', array_map(function ($val){ return '?'; }, $params));
        $resultRow = DB::selectOne('select * from menu_save(' . $paramsQuery . ')', $params);
        
        if (empty($resultRow)){
            array_push($result['errorMessages'], trans('messages.errorAssert'));
        } else {
            if (!empty($resultRow->errorcode)){
                array_push($result['errorMessages'], $resultRow->errorcode);
            } else {
                $result['success'] = true;
            } 
        }

        $result['menu_id'] = $resultRow->insert_id ?: $inputs['menu_id'];
        return $result;
    }

    public function deleteMenuById($id, $userLoginId)
    {
        $result = array('success' => false, 'errorMessages' => array());
        $params = array(
            $id,
            $userLoginId 
        );
        
        $paramsQuery = implode(',', array_map(function ($val){ return '?'; }, $params));
        $resultRow = DB::selectOne('select * from menu_delete(' . $paramsQuery . ')', $params);
        
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

    public static function getUserMenu($id){
        $menu = new \stdClass();
        $menu->data = array();
        $q = DB::table('menu')
                ->join('menurole', 'menu_id', 'menurole_menu_id')
                ->where('menu_active', '1')
                ->where('menurole_role_id', $id)
                ->where('menurole_active', '1')
                ->orderBy('menu_id')
                ->select('menu.*');

        $parent = $q->get();
        foreach ($parent as $dt){
            $mn = self::map($dt);
            if ($mn->menu_isparent){
                $qChild = DB::table('menu')
                    ->join('menu_role', 'menu_id', 'menurole_menu_id')
                    ->where('menu_active', '1')
                    ->where('menurole_active', '1')
                    ->where('menurole_role_id', $id)
                    ->where('menu_parent_id', $mn->menu_id)
                    ->orderBy('menu_id')
                    ->select('menu.*');
                $childMenu = $qChild->get();
                foreach ($childMenu as $cm){
                    $mnChild = self::map($cm);
                    array_push($mn->menu_child, $mnChild);
                }
                array_push($menu->data, $mn);
            }
        }
        return $menu;
    }

    public static function map($db)
    {
        $ui = new \stdClass();
        if (!isset($db)){
            $db = new \stdClass();
        }
        
        $ui->menu_id = isset($db->menu_id) ? $db->menu_id : 0;
        $ui->menu_is_parent = isset($db->menu_is_parent) ? $db->menu_is_parent : 0;
        $ui->menu_parent_id = isset($db->menu_parent_id) ? $db->menu_parent_id : 0;
        $ui->menu_name = isset($db->menu_name) ? $db->menu_name : null;
        $ui->menu_url = isset($db->menu_url) ? $db->menu_url : null;
        $ui->menu_icon = isset($db->menu_icon) ? $db->menu_icon : null;

        $ui->menu_child = $ui->menu_is_parent == '1' ? array() : null;
        
        return $ui;
    }

}