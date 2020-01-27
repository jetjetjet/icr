<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Repositories\CategoryRepository;

use Session;
use Validator;

class CategoryController extends Controller
{
    private $success;
    private $messages;
    private $stateCode;
    private $httpCode;
    private $statusCode;

    public function getAllCategory()
    {
        $data = CategoryRepository::getAllCategory();
        
        $data->success = $data != null  ? true : false;
        $data->state_code = $data != null ? "SUCCESS" : "FAILED";

        return response()->json($data);
    }

    public function getCategoryById($category_id)
    {
        if ($category_id){
            $data = CategoryRepository::getCategoryById($category_id);
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

    public function postSaveCategory(Request $request)
    {
        // Validation rules.
		$rules = array(
            'ctg_name' => 'required|max:50',
            'ctg_detail' => 'max:200',
            'ctg_price' => 'required',
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
            $results = CategoryRepository::save($inputs, session('userId'));
            $inputs['ctg_id'] = $results['ctg_id'];
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

    public function deleteCategory($category_id){
        $results = CustomerRepository::deleteCategoryById($category_id, session('userId'));
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