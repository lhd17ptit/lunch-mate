<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveFoodItemRequest;
use App\Repositories\FoodCategoryRepository;
use App\Repositories\ShopRepository;
use App\Services\FoodItemService;
use Illuminate\Http\Request;

class FoodItemController extends Controller
{
    protected $foodItemService;
    protected $foodCategoryRepository;
    protected $shopRepository;

    public function __construct(
        FoodItemService $foodItemService,
        FoodCategoryRepository $foodCategoryRepository,
        ShopRepository $shopRepository,
    )
    {
        $this->foodItemService = $foodItemService;
        $this->foodCategoryRepository = $foodCategoryRepository;
        $this->shopRepository = $shopRepository;
    }

    public function index()
    {
        $shops = $this->shopRepository->getAll();
        $foodCategories = $this->foodCategoryRepository->getAll();

        return view('admin.food-item.index', [
            'foodCategories' => $foodCategories,
            'shops' => $shops,
        ]);
    }

    public function getList(Request $request)
    {
        return $this->foodItemService->getList($request->all());
    }

    public function save(SaveFoodItemRequest $request)
    {
        return $this->foodItemService->save($request->all());
    }

    public function changeStatus(Request $request)
    {
        return $this->foodItemService->changeStatus($request->id);
    }

    public function delete(Request $request)
    {
        return $this->foodItemService->delete($request->id);
    }

    public function detail(Request $request)
    {
        return $this->foodItemService->detail($request->id);
    }
}
