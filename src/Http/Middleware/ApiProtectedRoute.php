<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class ApiProtectedRoute
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = $this->authenticate();
        } catch (TokenInvalidException $e) {
            return $this->jwtExceptionHandler($e, 'Token is Invalid', 403);
        } catch (TokenExpiredException $e) {
            return $this->jwtExceptionHandler($e, 'Token is Expired', 401);
        } catch (Exception $e) {
            return $this->jwtExceptionHandler($e, 'Authorization Token not found', 404);
        }

        return $next($request);
    }

    protected function parseToken()
    {
        return JWTAuth::parseToken();
    }

    protected function authenticate()
    {
        return $this->parseToken()->authenticate();
    }
    
    protected function getFailResponse(Exception $e, string $message)
    {
        $response = [
            'status'      => 'error',
            'message'     => $message,
            'jwt-message' => $e->getMessage()
        ];

        return $response;
    }
    
    protected function jwtExceptionHandler(Exception $e, string $message, int $code)
    {
        return response()->json($this->getFailResponse($e, $message), $code);
    }
}