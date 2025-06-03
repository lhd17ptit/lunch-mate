<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveUserRequest;
use App\Repositories\FloorRepository;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $userService;
    protected $floorRepository;

    public function __construct(
        UserService $userService,
        FloorRepository $floorRepository
    )
    {
        $this->userService = $userService;
        $this->floorRepository = $floorRepository;
    }

    public function index()
    {
        $floors = $this->floorRepository->getAll();
        $admin = Auth::guard('admin')->user();

        return view('admin.user.index', [
            'floors' => $floors,
            'admin' => $admin,
        ]);
    }

    public function getList(Request $request)
    {
        return $this->userService->getList($request->all());
    }

    public function save(SaveUserRequest $request)
    {
        return $this->userService->save($request->all());
    }

    public function changeStatus(Request $request)
    {
        return $this->userService->changeStatus($request->id);
    }

    public function delete(Request $request)
    {
        return $this->userService->delete($request->id);
    }
}
