<?php 

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use stdClass;

class ApiResponse
{

    const HTTP_OK = 200;
    const HTTP_CREATED = 201;
    const HTTP_ACCEPTED = 202;
    const HTTP_NO_CONTENT = 204;
    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_FORBIDDEN = 403;
    const HTTP_NOT_FOUND = 404;
    const HTTP_UNPROCESSABLE_ENTITY = 422;
    const HTTP_INTERNAL_SERVER_ERROR = 500;

    const STATUS_OK = 'OK';
    const STATUS_ERROR = 'ERROR';

    public $success = true;
    public $message = '';
    public $data = null;
    public $metadata = null;
    public $code = self::HTTP_OK;

    public function __construct()
    {
        $this->data = new stdClass();
        $this->metadata = new stdClass();
    }

    /**
     * Send a success response.
     *
     * @param  string|array|null  $data
     * @param  string  $message
     * @param  int  $status
     * @return \Illuminate\Http\JsonResponse
     */
    public static function success(
        $data = null,
        string $message = '',
        $metadata = null,
        $code = self::HTTP_OK
    ): ApiResponse
    {
        $response = new ApiResponse();
        $response->success = true;
        $response->message = $message;
        $response->data = $data;
        $response->metadata = $metadata;
        $response->code = $code;

        return $response;
    }

    public static function error(
        string $message = '', 
        $metadata = null,
        $code = self::HTTP_INTERNAL_SERVER_ERROR
    ): ApiResponse
    {
        $response = new ApiResponse();
        $response->success = false;
        $response->message = $message;
        $response->metadata = $metadata;
        $response->code = $code;

        return $response;
    }

    /**
     * Send the response.
     */
    public function send(): JsonResponse
    {
        return response()->json($this, $this->code);
    }

    /**
     * Convert the object to an array.
     */
    public function toArray()
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'data' => $this->data,
            'metadata' => $this->metadata,
        ];
    }
}
