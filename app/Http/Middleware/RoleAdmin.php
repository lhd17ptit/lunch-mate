<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use Closure;
use Illuminate\Http\Request;

class RoleAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $admin = auth()->guard('admin')->user();
        if ($admin->role != Admin::ADMIN) {
            return redirect()->route('admin.dashboard')->with('error', 'Bạn không có quyền truy cập vào trang này');
        }
        return $next($request);
    }
}
