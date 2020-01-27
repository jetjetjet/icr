<?php
namespace App\Http\Middleware;
use Closure;
use JWTAuth;
use Auth;
use Exception;use Illuminate\Support\Str;
class jwtMiddleware
{
    private static $unauhorizedMessage = "You are not authorized to access this resource / execute the action!";
    private static $unauthenticatedMessage = "You are not logged in!";

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        // $route   = $request->route();
        // $actions = $route->getAction()['middleware'];
        // dd( $actions[3],$request->route());
        if($guard == 'api') {
            try {
                if(!auth($guard)->check()) {
                    return response()->json(['status' => 'Token is Invalid2', 'status_code' => 401]);
                }
            } catch (\Exception $e) {
                if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                    return response()->json(['status' => 'Token is Invalid', 'status_code' => 401]);
                }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                    return response()->json(['status' => 'Token is Expired', 'status_code' => 401]);
                }else{
                    return response()->json(['status' => 'Authorization Token not found', 'status_code' => 401]);
                }
            }

            return $next($request);
        }
    }

    private static function terminateRequest($request, $message, $code)
    {
        if ($request->ajax()){
            return response()->json([$message], $code);
        }

        $request->session()->flash('globalErrorMessages', [$message]);
        return redirect('/');
    }
}