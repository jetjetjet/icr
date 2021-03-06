<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Repositories\RoleRepository;

use Validator;

class RoleController extends Controller
{
    private $success;
    private $messages;
    private $stateCode;
    private $httpCode;
    private $statusCode;

    public function getAllRole(Request $request)
    {
        $data = new \stdClass();
        //$filter = $this->mapFilter($request);
        $results = RoleRepository::getAllRole();
        $data->success = $data != null  ? true : false;
        $data->state_code = $data != null ? "SUCCESS" : "FAILED";
        //$data->totalCount = $results->count;
        $data->data = $results->data;

        return response()->json($data);
    }

    private function mapFilter($req){
        $filter = new \stdClass();

        $filter->limit = $req->input('take') !== null ? $req->input('take') : 10 ;
        $filter->offset = $req->input('skip') !== null ? $req->input('skip') : 0;

        // Sort columns.
        $filter->sortColumns = array();
        $orderColumns = $req->input('sort') != null ? $req->input('sort') : array();
        if ($orderColumns){
            $orderParse = json_decode($orderColumns, true);
            $filterColumn = new \stdClass();
            $filterColumn->column = $orderParse[0]['selector'];
            $filterColumn->order = $orderParse[0]['desc'] == true ? 'DESC' : 'ASC';
            array_push($filter->sortColumns, $filterColumn);
        }

        //Search Column
        $filter->search = $req->input('filter') != null ? json_decode($req->input('filter'), true) : array();
        
        return $filter;
    }

    public function getRoleById($role_id){
        if ($role_id){
            $data = RoleRepository::getRoleById($role_id);
            $success = $data != null  ? true : false;
            $stateCode = $data != null ? "SUCCESS" : "FAILED";

            return response()->json(
                array(
                    'state_code' => $stateCode,
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

    public function saveRole(Request $request)
    {
        // Validation rules.
		$rules = array(
            'role_name' => 'max:25',
            'role_description' => 'max:200',
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
            $results = RoleRepository::save($inputs, auth('api')->user()->getAuthIdentifier());
            $inputs['role_id'] = $results['id'];
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

    public function deleteRole($role_id)
    {
        $results = RoleRepository::deleteById($role_id, auth('api')->user()->getAuthIdentifier());
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