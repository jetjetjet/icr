<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Repositories\CustomerRepository;

use Session;
use Validator;

class CustomerController extends Controller
{
    private $success;
    private $messages;
    private $stateCode;
    private $httpCode;
    private $statusCode;

    public function getAllCustomer(){
        $data = CustomerRepository::getAllCustomer();
        
        $data->success = $data != null  ? true : false;
        $data->state_code = $data != null ? "SUCCESS" : "FAILED";

        return response()->json($data);
    }

    public function getCustomerById($customer_id)
    {
        if ($customer_id){
            $data = CustomerRepository::getCustomerById($customer_id);
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

    public function postSaveCustomer(Request $request)
    {
        // Validation rules.
		$rules = array(
            'customer_name' => 'required|max:50',
            'customer_address' => 'max:800',
            'customer_phone' => 'max:15',
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
            $results = CustomerRepository::save($inputs, session('userId'));
            $inputs['customer_id'] = $results['customer_id'];
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

    public function deleteCustomer($customer_id){
        $results = CustomerRepository::deleteCustomerById($customer_id, session('userId'));
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