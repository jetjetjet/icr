<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Repositories\AuthRepository;
use App\Repositories\RoleRepository;
use JWTAuth;
use JWTAuthException;

use Session;
use App\User;
use Auth;
use Validator;

class AuthController extends Controller
{
    
    public function authenticate(Request $request)
    {
        $credentials = $request->only('user_name', 'user_password');
        try {
            if (! $token = auth('api')->attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
        $userId = auth('api')->user()->getAuthIdentifier();
        $userRoleId = auth('api')->user()->getRoleId();
        $userRole = RoleRepository::getRoleName($userRoleId);
        $result = array(
            'token'=>compact('token')['token'],
            'user_id' => $userId,
            'user_name' => auth('api')->user()->getUserName(),
            'role_id' => $userRoleId,
            'role_name' => $userRole->role_name,
            'login' => true
        );
        
        return response()->json($result, 200);
    }

    public function getAuthenticatedUser(Request $request)
    {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['not_found'], 500);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        }

        $result = array(
            'userId' => auth('api')->user()->getAuthIdentifier(),
			'user_name' => auth('api')->user()->getUserName(),
            'role_id' => auth('api')->user()->getRoleId(),
            'login' => true
        );
        return response()->json($result, 200);
    }

    public function logOut(Request $request)
    {
        JWTAuth::invalidate($token);
    }
}