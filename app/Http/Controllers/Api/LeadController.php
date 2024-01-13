<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;
use GuzzleHttp;

class LeadController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/lead/add",
     *     tags={"Lead API"},
     *     summary="Add new lead",
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="phone", type="string", example="998994630613"),
     *          ),
     *     ),
     *     @OA\Response(
     *     response="200",
     *     description="Add new lead",
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

    public function addLead(Request $request)
    {
        Lead::insert([
            "phone" => $request->phone
        ]);
        try {
            $client = new GuzzleHttp\Client();
            $client->get("http://leadsocket.abdullajonsoliyev.uz:3001/update");
        } catch (\Throwable $th) {
            //throw $th;
        }
        return $this->sendResponse(null, true, "Added successfully!");
    }

    /**
     * @OA\Post(
     *     path="/api/lead/list",
     *     tags={"Lead API"},
     *     summary="List lead",
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
     *     description="List lead",
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
     *   "new_lead": {},
     *   "my_lead": {
     *     {
     *       "id": 1,
     *       "admin_id": 3,
     *       "phone": "998994630613",
     *       "comment": null,
     *       "status": "my",
     *       "position": "active",
     *       "created_at": null,
     *       "updated_at": "2024-01-12T11:00:33.000000Z"
     *     },
     *     {
     *       "id": 2,
     *       "admin_id": 3,
     *       "phone": "998994630614",
     *       "comment": null,
     *       "status": "my",
     *       "position": "active",
     *       "created_at": null,
     *       "updated_at": "2024-01-12T11:07:15.000000Z"
     *     }
     *   },
     *   "recall_lead": {},
     *   "didntpickup_lead": {},
     *   "coming_lead": {}
     * }
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


    public function getLeads(Request $request)
    {
        $admin = $request->admin;
        $new_leads = Lead::where("status", "new")->get();
        $data = [
            "new_lead" => $new_leads,
            "my_lead" => $admin->listLeadActive("my")->get(),
            "recall_lead" => $admin->listLeadActive("recall")->get(),
            "didntpickup_lead" => $admin->listLeadActive("didntpickup")->get(),
            "coming_lead" => $admin->listLeadActive("coming")->get(),
        ];

        return $this->sendResponse($data, true, "");
    }

    /**
     * @OA\Get(
     *     path="/api/lead/detail/{id}",
     *     tags={"Lead API"},
     *     summary="Get detail lead",
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
     *          name="id",
     *          in="path",
     *          description="Paste lead id",
     *          required=true,
     *       ),
     *     @OA\Response(
     *     response="200",
     *     description="Check admin **token** and get detail lead.",
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
     *       "id": 2,
     *       "admin_id": 3,
     *       "phone": "998994630614",
     *       "comment": null,
     *       "status": "my",
     *       "position": "active",
     *       "created_at": null,
     *       "updated_at": "2024-01-12T11:07:15.000000Z"
     *     }
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

    public function detailLead(Request $request, $id)
    {
        $lead = Lead::where(['id' => $id, "admin_id" => $request->admin->id])->first();
        return $this->sendResponse($lead, true, "");
    }

    /**
     * @OA\Post(
     *     path="/api/lead/change",
     *     tags={"Lead API"},
     *     summary="Change lead",
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
     *              @OA\Property(property="lead_id", type="integer", example="1"),
     *              @OA\Property(property="to_status", type="string", example="my"),
     *              @OA\Property(property="comment", type="string", example=""),
     *          ),
     *     ),
     *     @OA\Response(
     *     response="200",
     *     description="Change lead",
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
     *                  default="Changed successfully!",
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

    public function changeLead(Request $request)
    {
        $admin = $request->admin;
        $check_lead = Lead::where("id", $request->lead_id)->first();
        if ($check_lead == null) {
            return $this->sendResponse(null, false, "Lead not found!");
        }

        $active_lead_count = $admin->listLeadActive()->get()->count();
        if ($check_lead->status == "new" && $active_lead_count == 10) {
            return $this->sendResponse(null, false, "The limit of active leads has been reached!");
        }

        if ($check_lead->admin_id > 0 && $check_lead->admin_id != $admin->id) {
            return $this->sendResponse(null, false, "You are not allowed to modify this lead!");
        }

        $check_lead->update([
            "admin_id" => $admin->id,
            "status" => $request->to_status,
            "position" => $request->to_status == "done" ? "done" : $check_lead->position,
            "comment" => $request->comment
        ]);

        return $this->sendResponse(null, true, "Changed successfully!");
    }
}
