<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveAdminRequest;
use App\Repositories\FloorRepository;
use App\Services\AdminService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    protected $adminService;
    protected $floorRepository;

    public function __construct(
        AdminService $adminService,
        FloorRepository $floorRepository
    )
    {
        $this->adminService = $adminService;
        $this->floorRepository = $floorRepository;
    }

    public function index()
    {
        $floors = $this->floorRepository->getAll();

        return view('admin.user-admin.index', [
            'floors' => $floors,
        ]);
    }

    public function getList(Request $request)
    {
        return $this->adminService->getList($request->all());
    }

    public function save(SaveAdminRequest $request)
    {
        return $this->adminService->save($request->all());
    }

    public function changeStatus(Request $request)
    {
        return $this->adminService->changeStatus($request->id);
    }

    public function delete(Request $request)
    {
        return $this->adminService->delete($request->id);
    }
}
