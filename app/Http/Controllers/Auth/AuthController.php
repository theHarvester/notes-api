<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Cache;
use Illuminate\Cache\RateLimiter;
use Illuminate\Contracts\Validation\UnauthorizedException;
use Illuminate\Contracts\Validation\ValidationException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
//use Illuminate\Validation\Validator;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use ThrottlesLogins, AuthenticatesUsers;

    /**
     * Create a new authentication controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'getLogout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function postRegister(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            throw new ValidationException($validator->getMessageBag());
        }

        $this->create($request->all());

        return (new Response())
            ->setStatusCode(201)
            ->setContent([
                'message' => 'User created successfully!'
            ]);
    }

    /**
     * @param Request $request
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function authenticated(Request $request, User $user)
    {
        // Create token
        $token = (string)\Uuid::generate();
        $cache_key = "auth::{$token}";
        $expire_minutes = 60; // Expire in one hour

        if(Cache::get($cache_key)){
            // We have a collision
            throw new UnauthorizedException("Unexpected token collission, please login again");
        }

        Cache::put($cache_key, $user->id, $expire_minutes);

        return (new Response())
            ->setStatusCode(200)
            ->setContent([
                'token' => $token
            ]);
    }

    /**
     * @param Request $request
     * @return $this
     */
    protected function sendLockoutResponse(Request $request)
    {
        $seconds = app(RateLimiter::class)->availableIn(
            $request->input($this->loginUsername()).$request->ip()
        );

        return (new Response())
            ->setStatusCode(429)
            ->setContent([
                'data' => null,
                'error' => [
                    'message' => 'Too many failed login attempts. Please wait and try again'
                ]
            ]);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function postLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            throw new ValidationException($validator->getMessageBag());
        }

        $throttles = $this->isUsingThrottlesLoginsTrait();

        if ($throttles && $this->hasTooManyLoginAttempts($request)) {
            return $this->sendLockoutResponse($request);
        }

        $credentials = $this->getCredentials($request);

        if (Auth::attempt($credentials, $request->has('remember'))) {
            return $this->handleUserWasAuthenticated($request, $throttles);
        }

        if ($throttles) {
            $this->incrementLoginAttempts($request);
        }

        return (new Response())
            ->setStatusCode(403)
            ->setContent([
                'error' => [
                    'message' => 'Incorrect email or password, please try again.'
                ]
            ]);
    }
}
