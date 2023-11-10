<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Exception;
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

    use AuthenticatesUsers {
        login as parentLogin;
    }

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

    public function login(Request $request)
    {
        if ($request->is('api/*')) {
            // Lógica de autenticación para la API
            $credentials = $request->only('email', 'password');
    
            if (!Auth::attempt($credentials)) {
                $response = ApiResponse::error(
                    message: 'Invalid credentials',
                    code: ApiResponse::HTTP_UNAUTHORIZED
                );
                return $response->send();
            }

            $response = ApiResponse::success(
                data: [
                    'user' => Auth::user(),
                    'token' => Auth::user()->createToken('My-Token')->plainTextToken,
                ],
                message: 'Login successful'
            );

            return $response->send();
        } else {
            // Lógica de autenticación para la web
            try {
                return $this->parentLogin($request);
            } catch (Exception $e) {
                return redirect()->intended($this->redirectPath());
            }
        }
    }
}
