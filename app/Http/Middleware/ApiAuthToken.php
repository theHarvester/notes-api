<?php

namespace App\Http\Middleware;

use Cache;
use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Validation\UnauthorizedException;

class ApiAuthToken
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     * @throws \Exception
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->guest()) {
            $token = $request->header('x-token');
            if(!$token){
                throw new \Exception('Token header required. Please send x-token with a valid token from the auth/login endpoint');
            }
            $cache_key = "auth::{$token}";
            $user_id = Cache::get($cache_key);
            if(!$user_id){
                throw new UnauthorizedException('Invalid authentication token, please login and try again.');
            }
            $this->auth->loginUsingId($user_id);
        }

        return $next($request);
    }
}
