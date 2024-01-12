<?php

namespace App\Http\Controllers;

use App\Jobs\SendHttpRequest;
use App\Models\Admin;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use GuzzleHttp;


use OpenApi\Attributes as OA;
/**
 * @OA\Info(
 *     title="LEAD CONTROL API",
 *     version="1.0"
 * )
 */

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function sendResponse($result, $success = NULL, $message = NULL, $error_code = 0)
    {
        $response = [
            'success' => $success,
            'data' => $result,
            'message' => $message,
            'error_code' => $error_code,

        ];

        return response()->json($response, 200);
    }

    public function getToken()
    {
        $headers = getallheaders();
        return (isset($headers['Token'])) ? $headers['Token'] : $headers['token'];
    }

}
