<?php
namespace App\Http\Middleware;
use Closure;
use JWTAuth;
use Auth;
use Exception;
class jwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if($guard == 'api') {
            try {
                if(!auth($guard)->check()) {
                    return response()->json(['status' => 'Token is Invalid1']);
                }
            } catch (\Exception $e) {
                if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                    return response()->json(['status' => 'Token is Invalid']);
                }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                    return response()->json(['status' => 'Token is Expired']);
                }else{
                    return response()->json(['status' => 'Authorization Token not found']);
                }
            }

            return $next($request);
        }
        // try {
        //     // $token = JWTAuth::getToken();
        //     // $apy = JWTAuth::getPayload($token)->toArray();
        //     //     dd($apy);
        //         if (! $user = JWTAuth::parseToken()->authenticate()) {
        //             return response()->json(['user_not_found'], 404);
        //         }
        //     } catch (Exception $e) {
        //         if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
        //             return response()->json(['status' => 'Token is Invalid']);
        //         }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
        //             return response()->json(['status' => 'Token is Expired']);
        //         }else{
        //             return response()->json(['status' => 'Authorization Token not found']);
        //         }
        //     }
        //return $next($request);
    }
}