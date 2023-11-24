<?php


namespace App\Http\Controllers\Auth;

use App\Http\Controllers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\Auth\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiAuthController extends Controller
{
    
    /**
     * Generates a token and saves it in the personal_access_token table.
     * It avoids to create many tokens for the same user. If a token already exists, it returns it.
     */
    private function generateToken()
    {
        /**
         * @var User $user
         */
        $user = Auth::user();

        // Buscar un token existente para el usuario
        $tokenName = 'UserToken';
        $user->tokens->where('name', $tokenName)->each(function ($token) {
            $token->delete();
        });

        return $user->createToken($tokenName, ['*'], now()->addSeconds(30));
    }

    /**
     * Creates a new token for the already logued user
     */
    public function refresh()
    {
        $token = $this->generateToken();

        $response = ApiResponse::success(
            data: [
                'token' => [
                    'token' => $token->plainTextToken,
                    'expires_at' => $token->accessToken->expires_at
                ]
            ],
            message: 'Token refreshed'
        );

        return $response->send();
    }

    public function login(Request $request)
    {
            // Lógica de autenticación para la API
            $credentials = $request->only('email', 'password');

            if (!Auth::attempt($credentials)) {
                $response = ApiResponse::error(
                    message: 'Invalid credentials',
                    code: ApiResponse::HTTP_UNAUTHORIZED
                );
                return $response->send();
            }

            $token = $this->generateToken();

            $response = ApiResponse::success(
                data: [
                    'user' => new UserResource(Auth::user()),
                'token' => [
                    'token' => $token->plainTextToken,
                    'expires_at' => $token->accessToken->expires_at
                ]
                ],
                message: 'Login successful'
            );

            return $response->send();
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        $response = ApiResponse::success(
            message: 'Logout successful'
        );

        return $response->send();
    }
}