<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Repositories\MenuRepository;

use Session;
use Validator;

class MenuController extends Controller
{
    private $success;
    private $messages;
    private $stateCode;
    private $httpCode;
    private $statusCode;

    public function getAllMenu(){
        $data = MenuRepository::getAllMenu();
        
        $data->success = $data != null  ? true : false;
        $data->state_code = $data != null ? "SUCCESS" : "FAILED";

        return response()->json($data);
    }

    public function getMenuById($menu_id)
    {
        if ($menu_id){
            $data = MenuRepository::getMenuById($menu_id);
            $success = $data != null  ? true : false;
            $stateCode = $data != null ? "SUCCESS" : "FAILED";

            return response()->json(
                array(
                    'state_code' => $stateCode ,
                    'success' => $success,
                    'messages' => null,
                    'data' => $data
                )
            );
        } else {
            return response()->json(
                array(
                    'state_code' => "FAILED",
                    'success' => false,
                    'messages' => $messages,
                    'data' => null
                )
            );
        }
    }

    public function postSaveMenu(Request $request)
    {
        // Validation rules.
		$rules = array(
            'menu_name' => 'required|max:30',
            'menu_url' => 'max:100',
            'menu_icon' => 'max:25',
        );
        
        $inputs = $request->all();

        // Validates.
        $validator = Validator::make($inputs, $rules);
        if ($validator->fails()) {
            $success = false;
            $messages = $validator->messages()->get('*');
            $stateCode = "FAILED";
        } else {
            // Executes to Repo and DB
            $results = MenuRepository::save($inputs, session('userId'));
            $inputs['menu_id'] = $results['menu_id'];
            $success = $results['success'] ? true : false;
            $messages = $results['errorMessages'];
            $stateCode = $results['success'] ? "SUCCESS" : "FAILED";
        }

        return response()->json(
            array(
                'state_code' => $stateCode,
                'success' => $success,
                'messages' => $messages,
                'data' => $inputs
            )
        );
    }

    public function getUserMenu()
    {
        $id = session('userId');

        if ($id){
            $menu = MenuRepository::getUserMenu($id);
            
            $success = $menu != null ? true : false;
            //$messages = $menu['errorMessages'];
            $stateCode = $menu != null ? "SUCCESS" : "FAILED";

            return response()->json(
                array(
                    'state_code' => $stateCode,
                    'success' => $success,
                    //'messages' => $messages,
                    'data' => $menu->data
                )
            );
        } else {
            return response()->json(
                array(
                    'state_code' => 'FAILED',
                    'success' => false,
                    'messages' => 'Data tidak ditemukan',
                    'data' => null
                )
            );
        }

    }

    public function deleteMenu($menu_id){
        $results = RoleRepository::deleteMenuById($menu_id, session('userId'));
        $success = $results['success'] ? true : false;
        $messages = $results['errorMessages'];
        $stateCode = $results['success'] ? "SUCCESS" : "FAILED";

        return response()->json(
            array(
                'state_code' => $stateCode,
                'success' => $success,
                'messages' => $messages,
                'data' => $results
            )
        );
    }
}