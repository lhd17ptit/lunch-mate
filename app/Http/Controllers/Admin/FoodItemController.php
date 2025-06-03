<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveFoodItemRequest;
use App\Repositories\FoodCategoryRepository;
use App\Services\FoodItemService;
use Illuminate\Http\Request;

class FoodItemController extends Controller
{
    protected $foodItemService;
    protected $foodCategoryRepository;

    public function __construct(
        FoodItemService $foodItemService,
        FoodCategoryRepository $foodCategoryRepository,
    )
    {
        $this->foodItemService = $foodItemService;
        $this->foodCategoryRepository = $foodCategoryRepository;
    }

    public function index()
    {
        $foodCategories = $this->foodCategoryRepository->getAll();

        return view('admin.food-item.index', [
            'foodCategories' => $foodCategories,
        ]);
    }

    public function getList()
    {
        return $this->foodItemService->getList();
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
