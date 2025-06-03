<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveShopRequest;
use App\Services\ShopService;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    protected $shopService;

    public function __construct(
        ShopService $shopService,
    )
    {
        $this->shopService = $shopService;
    }

    public function index()
    {
        return view('admin.shop.index');
    }

    public function getList()
    {
        return $this->shopService->getList();
    }

    public function save(SaveShopRequest $request)
    {
        return $this->shopService->save($request->all());
    }

    public function changeStatus(Request $request)
    {
        return $this->shopService->changeStatus($request->id);
    }

    public function delete(Request $request)
    {
        return $this->shopService->delete($request->id);
    }

    public function detail(Request $request)
    {
        return $this->shopService->detail($request->id);
    }
}
