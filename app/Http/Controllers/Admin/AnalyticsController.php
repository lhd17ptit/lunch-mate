<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\FloorRepository;
use App\Repositories\ShopRepository;
use App\Services\OrderService;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    protected $shopRepository;
    protected $floorRepository;
    protected $orderService;

    public function __construct(
        ShopRepository $shopRepository,
        FloorRepository $floorRepository,
        OrderService $orderService
    ) {
        $this->shopRepository = $shopRepository;
        $this->floorRepository = $floorRepository;
        $this->orderService = $orderService;
    }

    public function index()
    {
        $shops = $this->shopRepository->getAll();
        $floors = $this->floorRepository->getAll();

        return view('admin.analytics.index', [
            'shops' => $shops,
            'floors' => $floors,
        ]);
    }

    public function getList(Request $request)
    {
        return $this->orderService->getListAnalytics($request->all());
    }

    public function export(Request $request)
    {
        return $this->orderService->exportAnalytics($request->all());
    }

    public function detail(Request $request)
    {
        return $this->orderService->detailAnalytics($request->all());
    }
}
