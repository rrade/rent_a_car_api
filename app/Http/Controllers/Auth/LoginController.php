<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    protected function guard()
    {
        return Auth::guard("web");
    }
    protected function attemptLogin(Request $request)
    {
        $user = User::where("email",$request->email)->first();
        //dd($user);
        if($user){
            if(!$user->role_id == Role::EMPLOYEE){
                \auth('web')->logout();
                return abort(403);
            }
        }
        //return Auth::attempt($this->credentials($request));
        //dd($this->credentials($request));
        return $this->guard()->attempt(
            $this->credentials($request)
        );
    }
}
