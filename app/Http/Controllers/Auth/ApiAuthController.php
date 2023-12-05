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

        return $user->createToken($tokenName, ['*'], now()->addHours(2));
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
        try {

            // Lógica de autenticación para la API
            $credentials = $request->only('email', 'password');
            
            if (!Auth::attempt($credentials)) {
                throw new \Exception('Invalid credentials', ApiResponse::HTTP_UNAUTHORIZED);
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
        } catch (\Throwable $th) {
            $response = ApiResponse::error(
                message: $th->getMessage(),
                metadata: [
                    'errorDetails' => $th->getMessage()
                ],
                code: $th->getCode(),
                originalException: $th
            );
        }
        return $response->send();
    }

    public function logout(Request $request)
    {
        try {

            $request->user()->currentAccessToken()->delete();
            
            $response = ApiResponse::success(
                message: 'Logout successful'
            );
            
            return $response->send();
        } catch (\Throwable $th) {
            $response = ApiResponse::error(
                message: 'Error login out',
                metadata: [
                    'errorDetails' => $th->getMessage()
                ],
                code: $th->getCode(),
                originalException: $th
            );
        }
    }
}