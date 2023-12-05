<?php 

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use stdClass;
use Throwable;

/**
 * This class stores and manage all the responses for the API.
 * @property boolean $success Indicates if the response is successful or not.
 * @property string $message Message to be displayed to the final user.
 * @property stdClass $data Data to be returned to the final user.
 *                          This is the data that a frontend will use.
 * @property stdClass $metadata Metadata to be returned to the final user. 
 *                              This includes pagination information, errors details etc.
 * @property int $code HTTP code to be returned to the final user.
 */
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
        array | null $metadata = null,
        $code = self::HTTP_OK
    ): ApiResponse
    {
        $response = new ApiResponse();
        $response->success = true;
        $response->message = $message;
        if ($data !== null) {
            $response->data = $data;
        }
        if ($metadata !== null) {
            $response->metadata = $metadata;
        }
        $response->code = $code;

        return $response;
    }

    public static function error(
        string $message = '', 
        $metadata = null,
        $code = self::HTTP_INTERNAL_SERVER_ERROR,
        Throwable $originalException = null
    ): ApiResponse
    {
        $response = new ApiResponse();
        $response->success = false;
        $response->message = $message;
        if ($metadata !== null) {
            $response->metadata = $metadata;
        }
        $response->code = $code;

        // We only want to show the error details in development environments
        if (env('APP_ENV') !== 'production') {
            $response->metadata['errorData'] = [
                'trace' => $originalException?->getTrace() ?? [],
                'file' => $originalException->getFile() ?? '',
                'line' => $originalException->getLine() ?? '',
            ];

        }

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
