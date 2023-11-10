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

                $this->validateLogin($request);

                // If the class is using the ThrottlesLogins trait, we can automatically throttle
                // the login attempts for this application. We'll key this by the username and
                // the IP address of the client making these requests into this application.
                if (method_exists($this, 'hasTooManyLoginAttempts') &&
                    $this->hasTooManyLoginAttempts($request)) {
                    $this->fireLockoutEvent($request);
        
                    return $this->sendLockoutResponse($request);
                }
        
                if ($this->attemptLogin($request)) {
                    if ($request->hasSession()) {
                        $request->session()->put('auth.password_confirmed_at', time());
                    }

                    $request->session()->regenerate();
            
                    $this->clearLoginAttempts($request);
            
                    if ($response = $this->authenticated($request, $this->guard()->user())) {
                        return $response;
                    }

                    $token = Auth::user()->createToken('My-Token')->plainTextToken;
                    $request->session()->put('apiToken', $token);
                    $request->session()->save();

                    return redirect()->intended('home');
                }
            } catch (Exception $e) {
                return redirect()->intended($this->redirectPath());
            }
        }
    }
}
