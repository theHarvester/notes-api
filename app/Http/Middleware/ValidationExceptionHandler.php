<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Validation\ValidationException;
use Illuminate\Http\Response;

class ValidationExceptionHandler
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;


    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            return $next($request);
        } catch (ValidationException $e) {
            $error = [
                'message' => $e->getMessage(),
                'message_ext' => $e->getMessageProvider(),
                'code' => $e->getCode(),
            ];
            if (env('APP_DEBUG')) {
                $error['file'] = $e->getFile();
                $error['line'] = $e->getLine();
                $error['previous'] = $e->getPrevious();
//                $error['trace'] = $e->getTrace();
            }
            $response = new Response();
            $response->setContent([
                'data' => null,
                'error' => $error
            ]);
            $response->setStatusCode(400);
//            die();
            return $response;
        }
    }
}
