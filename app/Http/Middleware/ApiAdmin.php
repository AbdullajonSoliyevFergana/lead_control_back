<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Closure;
use Illuminate\Http\Request;
use App\Http\Controllers\RoleActions;
use App\Http\Controllers\UserActions;

class ApiAdmin extends Controller
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $admin = Admin::where('token', $this->getToken())->where('status', 'active')->first();
        if ($admin == null) {
            return $this->sendResponse(null, false, "Admin not found!", 1);
        }
        $request->admin = $admin;
        return $next($request);
    }
}
