<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/admin/registration",
     *     tags={"Admin API"},
     *     summary="Register admin/operator",
     *     @OA\Parameter(
     *          name="Token",
     *          in="header",
     *          description="Admin token",
     *          @OA\Schema(
     *              type="string"
     *          ),
     *          required=true,
     *      ),
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="role", type="string", example="operator"),
     *              @OA\Property(property="login", type="string", example="998994630613"),
     *              @OA\Property(property="password", type="string", example="123456"),
     *          ),
     *     ),
     *     @OA\Response(
     *     response="200",
     *     description="Registration admin and to login.",
     *     @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  format="boolean",
     *                  default="true",
     *                  description="success",
     *                  property="success"
     *              ),
     *              @OA\Property(
     *                  format="object",
     *                  description="data",
     *                  property="data",
     *                  example=null
     *              ),
     *              @OA\Property(
     *                  format="string",
     *                  default="Added successfully!",
     *                  description="message",
     *                  property="message"
     *              ),
     *              @OA\Property(
     *                  format="integer",
     *                  default="0",
     *                  description="error_code",
     *                  property="error_code"
     *              ),
     *          ),
     *     ),
     * )
     */

    public function registerAdmin(Request $request)
    {

        $check_admin = Admin::where(['status' => 'active', 'login' => $request->login])->first();
        if ($check_admin != null) {
            return $this->sendResponse(null, false, "Such an admin exists!");
        }
        if (strlen($request->password) >= 6) {
            $password = Hash::make($request->password);
            Admin::create([
                'login' => $request->login,
                'password' => $password,
                'role' => $request->role,
            ]);
            return $this->sendResponse(null, true, "Added successfully!");
        }

        return $this->sendResponse(null, false, "The length of the password should not be less than 6 characters!");
    }

    /**
     * @OA\Post(
     *     path="/api/admin/login",
     *     tags={"Admin API"},
     *     summary="Login admin/operator",
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="login", type="string", example="998994630613"),
     *              @OA\Property(property="password", type="string", example="123456"),
     *          ),
     *     ),
     *     @OA\Response(
     *     response="200",
     *     description="Check admin **login** & **password** and to login.",
     *     @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  format="boolean",
     *                  default="true",
     *                  description="success",
     *                  property="success"
     *              ),
     *              @OA\Property(
     *                  format="object",
     *                  description="data",
     *                  property="data",
     *                  example={
     *                      "id": 2,
     *   "admin_socket_id": null,
     *   "login": "998994630613",
     *   "password": "$2y$10$veIbPHbXbOknIz1qHRhNze3mIOI2DmX4jcJWupT96tA3vK8XAIZ5q",
     *   "token": "XsfiFH57c8AdWSC9EzquHWAkPPKnQS",
     *   "role": "operator",
     *   "status": "active",
     *   "created_at": "2024-01-12T09:13:46.000000Z",
     *   "updated_at": "2024-01-12T09:17:38.000000Z"
     *                  }
     *              ),
     *              @OA\Property(
     *                  format="string",
     *                  default="Welcome!",
     *                  description="message",
     *                  property="message"
     *              ),
     *              @OA\Property(
     *                  format="integer",
     *                  default="0",
     *                  description="error_code",
     *                  property="error_code"
     *              ),
     *          ),
     *     ),
     * )
     */

    public function loginAdmin(Request $request)
    {
        $admin = Admin::where('login', $request->login)->where('status', 'active')->first();
        if (Hash::check($request->password, $admin->password) === FALSE) {
            return $this->sendResponse(null, false, "Wrong password!");
        } else {
            $token = Str::random(30);
            $admin->update([
                'token' => $token
            ]);
            $admin = Admin::where('id', $admin->id)->first();

            return $this->sendResponse($admin, true, "Welcome!");
        }
    }

    /**
     * @OA\Get(
     *     path="/api/admin/get",
     *     tags={"Admin API"},
     *     summary="Get admin/operator",
     *     @OA\Parameter(
     *          name="Token",
     *          in="header",
     *          description="Admin token",
     *          @OA\Schema(
     *              type="string"
     *          ),
     *          required=true,
     *      ),
     *     @OA\Response(
     *     response="200",
     *     description="Check admin **token** and get this admin.",
     *     @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  format="boolean",
     *                  default="true",
     *                  description="success",
     *                  property="success"
     *              ),
     *              @OA\Property(
     *                  format="object",
     *                  description="data",
     *                  property="data",
     *                  example={
     *                      "id": 2,
     *   "admin_socket_id": null,
     *   "login": "998994630613",
     *   "password": "$2y$10$veIbPHbXbOknIz1qHRhNze3mIOI2DmX4jcJWupT96tA3vK8XAIZ5q",
     *   "token": "XsfiFH57c8AdWSC9EzquHWAkPPKnQS",
     *   "role": "operator",
     *   "status": "active",
     *   "created_at": "2024-01-12T09:13:46.000000Z",
     *   "updated_at": "2024-01-12T09:17:38.000000Z"
     *                  }
     *              ),
     *              @OA\Property(
     *                  format="string",
     *                  default="",
     *                  description="message",
     *                  property="message"
     *              ),
     *              @OA\Property(
     *                  format="integer",
     *                  default="0",
     *                  description="error_code",
     *                  property="error_code"
     *              ),
     *          ),
     *     ),
     * )
     */

    public function getAdmin(Request $request)
    {
        $admin = $request->admin;

        return $this->sendResponse($admin, true, "");
    }

    /**
     * @OA\Post(
     *     path="/api/admin/list",
     *     tags={"Admin API"},
     *     summary="Get admin list",
     *     @OA\Parameter(
     *          name="Token",
     *          in="header",
     *          description="Admin token",
     *          @OA\Schema(
     *              type="string"
     *          ),
     *          required=true,
     *      ),
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="word", type="string", example=""),
     *              @OA\Property(property="page", type="integer", example="1"),
     *          ),
     *     ),
     *     @OA\Response(
     *     response="200",
     *     description="Check admin **token** and get admin list.",
     *     @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  format="boolean",
     *                  default="true",
     *                  description="success",
     *                  property="success"
     *              ),
     *              @OA\Property(
     *                  format="object",
     *                  description="data",
     *                  property="data",
     *                  example={
     *                     
     * "total_count": 1,
     * "page": 1,
     * "limit": 15,
     * "items": {
     *   {
     *     "id": 2,
     *     "admin_socket_id": null,
     *     "login": "998994630613",
     *     "password": "$2y$10$veIbPHbXbOknIz1qHRhNze3mIOI2DmX4jcJWupT96tA3vK8XAIZ5q",
     *     "token": "wj8yLsFAoyWcXwdkR1gUHht16PCwsh",
     *     "role": "operator",
     *     "status": "active",
     *     "created_at": "2024-01-12T09:13:46.000000Z",
     *     "updated_at": "2024-01-12T09:20:33.000000Z"
     *   }
     * }
     *                  }
     *              ),
     *              @OA\Property(
     *                  format="string",
     *                  default="",
     *                  description="message",
     *                  property="message"
     *              ),
     *              @OA\Property(
     *                  format="integer",
     *                  default="0",
     *                  description="error_code",
     *                  property="error_code"
     *              ),
     *          ),
     *     ),
     * )
     */

    public function getAdmins(Request $request)
    {

        $admin = $request->admin;

        $get_word = $request->word;
        $admins = Admin::where('status', 'active');
        if ($admin->role != "admin") {
            $admins->where("role", "!=", "admin");
        }

        if (strlen($get_word) > 2) {
            $admins = $admins->where(function ($query) use ($get_word) {
                $query->where('login', 'like', '%' . $get_word . '%');
            });
        }


        $perPage = 15;
        $page = $request->page;
        $total_count = $admins->count();
        $admins = $admins->offset(($page - 1) * $perPage)->limit($perPage)->get();

        $data = [
            "total_count" => $total_count,
            "page" => $page,
            "limit" => $perPage,
            "items" => $admins
        ];

        return $this->sendResponse($data, true, "");
    }

    /**
     * @OA\Get(
     *     path="/api/admin/detail/{admin_id}",
     *     tags={"Admin API"},
     *     summary="Get detail admin/operator",
     *     @OA\Parameter(
     *          name="Token",
     *          in="header",
     *          description="Admin token",
     *          @OA\Schema(
     *              type="string"
     *          ),
     *          required=true,
     *      ),
     *     @OA\Parameter(
     *          name="admin_id",
     *          in="path",
     *          description="Paste admin id",
     *          required=true,
     *       ),
     *     @OA\Response(
     *     response="200",
     *     description="Check admin **token** and get detail admin.",
     *     @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  format="boolean",
     *                  default="true",
     *                  description="success",
     *                  property="success"
     *              ),
     *              @OA\Property(
     *                  format="object",
     *                  description="data",
     *                  property="data",
     *                  example={
     *                      "id": 2,
     *   "admin_socket_id": null,
     *   "login": "998994630613",
     *   "password": "$2y$10$veIbPHbXbOknIz1qHRhNze3mIOI2DmX4jcJWupT96tA3vK8XAIZ5q",
     *   "token": "XsfiFH57c8AdWSC9EzquHWAkPPKnQS",
     *   "role": "operator",
     *   "status": "active",
     *   "created_at": "2024-01-12T09:13:46.000000Z",
     *   "updated_at": "2024-01-12T09:17:38.000000Z"
     *                  }
     *              ),
     *              @OA\Property(
     *                  format="string",
     *                  default="",
     *                  description="message",
     *                  property="message"
     *              ),
     *              @OA\Property(
     *                  format="integer",
     *                  default="0",
     *                  description="error_code",
     *                  property="error_code"
     *              ),
     *          ),
     *     ),
     * )
     */

    public function detailAdmin($admin_id)
    {
        $admin = Admin::where('id', $admin_id)->where('status', 'active')->first();
        return $this->sendResponse($admin, true, "");
    }

    /**
     * @OA\Post(
     *     path="/api/admin/update/{admin_id}",
     *     tags={"Admin API"},
     *     summary="Update admin/operator",
     *     @OA\Parameter(
     *          name="Token",
     *          in="header",
     *          description="Admin token",
     *          @OA\Schema(
     *              type="string"
     *          ),
     *          required=true,
     *      ),
     *     @OA\Parameter(
     *          name="admin_id",
     *          in="path",
     *          description="Paste admin id",
     *          required=true,
     *       ),
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="login", type="string", example="998994630613"),
     *              @OA\Property(property="password", type="string", example="123456"),
     *          ),
     *     ),
     *     @OA\Response(
     *     response="200",
     *     description="Registration admin and to login.",
     *     @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  format="boolean",
     *                  default="true",
     *                  description="success",
     *                  property="success"
     *              ),
     *              @OA\Property(
     *                  format="object",
     *                  description="data",
     *                  property="data",
     *                  example=null
     *              ),
     *              @OA\Property(
     *                  format="string",
     *                  default="Update successfully!",
     *                  description="message",
     *                  property="message"
     *              ),
     *              @OA\Property(
     *                  format="integer",
     *                  default="0",
     *                  description="error_code",
     *                  property="error_code"
     *              ),
     *          ),
     *     ),
     * )
     */

    public function updateAdmin(Request $request, $admin_id)
    {
        $admin = Admin::where('id', $admin_id)->where('status', 'active')->first();
        if ($admin != null) {

            $check_admin = Admin::where(['status' => 'active', 'login' => $request->login])
                ->where('id', '!=', $admin_id)
                ->first();
            if ($check_admin != null) {
                return $this->sendResponse(null, false, "Such an admin exists!");
            }
            $password = $admin->password;
            if (strlen($request->password) > 0) {
                if (strlen($request->password) < 6) {
                    return $this->sendResponse(null, false, "The length of the password should not be less than 6 characters!");
                }

                $password = Hash::make($request->password);
            }
            $admin->update([
                'login' => $request->login,
                'password' => $password,
            ]);
            return $this->sendResponse(null, true, "Update successfully!");
        }
        return $this->sendResponse(null, false, "Admin not found!");
    }
    /**
     * @OA\Get(
     *     path="/api/admin/delete/{admin_id}",
     *     tags={"Admin API"},
     *     summary="Delete admin/operator",
     *     @OA\Parameter(
     *          name="Token",
     *          in="header",
     *          description="Admin token",
     *          @OA\Schema(
     *              type="string"
     *          ),
     *          required=true,
     *      ),
     *     @OA\Parameter(
     *          name="admin_id",
     *          in="path",
     *          description="Paste admin id",
     *          required=true,
     *       ),
     *     @OA\Response(
     *     response="200",
     *     description="Check admin **token** and delete admin.",
     *     @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  format="boolean",
     *                  default="true",
     *                  description="success",
     *                  property="success"
     *              ),
     *              @OA\Property(
     *                  format="object",
     *                  description="data",
     *                  property="data",
     *                  example=null
     *              ),
     *              @OA\Property(
     *                  format="string",
     *                  default="Delete successfully!",
     *                  description="message",
     *                  property="message"
     *              ),
     *              @OA\Property(
     *                  format="integer",
     *                  default="0",
     *                  description="error_code",
     *                  property="error_code"
     *              ),
     *          ),
     *     ),
     * )
     */

    public function deleteAdmin($admin_id)
    {

        $admin = Admin::where('id', $admin_id)->where('status', 'active')->first();
        if ($admin != null) {
            $admin->update([
                "status" => "deleted"
            ]);
            return $this->sendResponse(null, true, "Delete successfully!");
        }
        return $this->sendResponse(null, false, "Admin not found!");
    }

    public function setSocket($token, $socket_id){
        $admin = Admin::where("token", $token)->first();
        $admin->update([
            "admin_socket_id" => $socket_id
        ]);
        return response()->json(null, 200);
    }
}
