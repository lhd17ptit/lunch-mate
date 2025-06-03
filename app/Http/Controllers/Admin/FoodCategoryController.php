<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveFoodCategoryRequest;
use App\Repositories\ShopRepository;
use App\Services\FoodCategoryService;
use Illuminate\Http\Request;

class FoodCategoryController extends Controller
{
    protected $foodCategoryService;
    protected $shopRepository;

    public function __construct(
        FoodCategoryService $foodCategoryService,
        ShopRepository $shopRepository,
    )
    {
        $this->foodCategoryService = $foodCategoryService;
        $this->shopRepository = $shopRepository;
    }

    public function index()
    {
        $shops = $this->shopRepository->getAll();

        return view('admin.food-category.index', [
            'shops' => $shops,
        ]);
    }

    public function getList()
    {
        return $this->foodCategoryService->getList();
    }

    public function save(SaveFoodCategoryRequest $request)
    {
        return $this->foodCategoryService->save($request->all());
    }

    public function changeStatus(Request $request)
    {
        return $this->foodCategoryService->changeStatus($request->id);
    }

    public function delete(Request $request)
    {
        return $this->foodCategoryService->delete($request->id);
    }

    public function detail(Request $request)
    {
        return $this->foodCategoryService->detail($request->id);
    }
}
