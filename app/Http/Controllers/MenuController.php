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

    public function postSaveMenu(Request $request){
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
            $messages = $validator;
            $stateCode = "FAILED";
        } else {
            // Executes to Repo and DB
            $results = MenuRepository::save($inputs, 1);
            $inputs['user_id'] = $results['id'];
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

    public function getUserMenu(){
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
}