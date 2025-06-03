<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveFloorRequest;
use App\Services\FloorService;
use Illuminate\Http\Request;

class FloorController extends Controller
{
    protected $floorService;

    public function __construct(
        FloorService $floorService,
    )
    {
        $this->floorService = $floorService;
    }

    public function index()
    {
        return view('admin.floor.index');
    }

    public function getList()
    {
        return $this->floorService->getList();
    }

    public function save(SaveFloorRequest $request)
    {
        return $this->floorService->save($request->all());
    }

    public function changeStatus(Request $request)
    {
        return $this->floorService->changeStatus($request->id);
    }

    public function delete(Request $request)
    {
        return $this->floorService->delete($request->id);
    }

    public function detail(Request $request)
    {
        return $this->floorService->detail($request->id);
    }
}
