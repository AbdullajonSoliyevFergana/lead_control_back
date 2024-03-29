<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Closure;
use Illuminate\Http\Request;

class ApiLoginAdmin extends Controller
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
        $admin = Admin::where('login', $request->login)->where('status', 'active')->first();
        if ($admin !== null) {
            $request->admin = $admin;

            return $next($request);
        }

        return $this->sendResponse(null, false, "Admin not found!", 1);
    }
}
