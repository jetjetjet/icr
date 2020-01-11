<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use JWTAuth;
use JWTAuthException;

use Session;
use App\User;
use Auth;
use Validator;

class UserController extends Controller
{
    private $success;
    private $messages;
    private $stateCode;
    private $httpCode;
    private $statusCode;

    public function getAllUsers(){
        $data = UserRepository::getAllUsers();
        
        $data->success = $data != null  ? true : false;
        $data->state_code = $data != null ? "SUCCESS" : "FAILED";

        return response()->json($data);
    }

    public function getUserById($user_id){
        if ($id){
            $data = UserRepository::getById($user_id);
            $success = $data != null  ? true : false;
            $stateCode = $data != null ? "SUCCESS" : "FAILED";

            return response()->json(
                array(
                    'state_code' => "SUCCESS",
                    'success' => true,
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

    public function postSaveUser(Request $request)
    {
        // Validation rules.
		$rules = array(
            'user_role_id' => 'required',
            'user_name' => 'required|max:25',
            'user_fullname' => 'max:50',
            'user_phone' => 'max:15',
            'user_address' => 'max:800'
        );
        
        $inputs = $request->all();
        if (!empty($inputs['user_id'])){
            // Existing user, password is not mandatory, only length validation.
            $rules['user_password'] = 'max:100';
        } else {
            $rules['user_password'] = 'required|max:100';
        }

        // Validates.
        $validator = Validator::make($inputs, $rules);
        if ($validator->fails()) {
            $success = false;
            $messages = $validator->messages()->get('*');
            $stateCode = "FAILED";
        } else {
            // Executes to Repo and DB
            $results = UserRepository::save($inputs, 1);
            $inputs['user_id'] = $results['user_id'];
            $success = $results['success'] ? true : false;
            $messages = $results['errorMessages'];
            $stateCode = $results['success'] ? "SUCCESS" : "FAILED";
        }

        return response()->json(
            array(
                'success' => $success,
                'messages' => $messages,
                'data' => $inputs
            )
        );
    }
}