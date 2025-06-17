<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Services\AdminService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function index()
    {
        $admin = Auth::guard('admin')->user();

        return view('admin.dashboard.index', compact('admin'));
    }

    /**
     * Show the login form.
     *
     * @return \Illuminate\View\View
     */
    public function indexLogin()
    {
        return view('admin.auth.login');
    }

    /**
     * Handle the login request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (auth()->guard('admin')->attempt($credentials)) {
            return redirect()->route('admin.dashboard')->with('success', 'Đăng nhập thành công');
        }
        return redirect()->back()->withErrors(['username' => 'Thông tin đăng nhập không hợp lệ'])->withInput();
    }

    /**
     * Handle the logout request.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        auth()->guard('admin')->logout();
        return redirect()->route('admin.login.index')->with('success', 'Đăng xuất thành công');
    }
}
