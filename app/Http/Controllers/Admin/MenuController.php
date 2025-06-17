<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveMenuItemRequest;
use App\Http\Requests\Admin\SaveMenuRequest;
use App\Repositories\MenuRepository;
use App\Repositories\ShopRepository;
use App\Services\MenuService;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    protected $menuService;
    protected $shopRepository;
    protected $menuRepository;

    public function __construct(
        MenuService $menuService,
        ShopRepository $shopRepository,
        MenuRepository $menuRepository,
    )
    {
        $this->menuService = $menuService;
        $this->shopRepository = $shopRepository;
        $this->menuRepository = $menuRepository;
    }

    public function index()
    {
        $shops = $this->shopRepository->getAll();

        return view('admin.menu.index', [
            'shops' => $shops,
        ]);
    }

    public function getList(Request $request)
    {
        return $this->menuService->getList($request->all());
    }

    public function save(SaveMenuRequest $request)
    {
        return $this->menuService->save($request->all());
    }

    public function changeStatus(Request $request)
    {
        return $this->menuService->changeStatus($request->id, $request->status);
    }

    public function delete(Request $request)
    {
        return $this->menuService->delete($request->id);
    }

    public function indexMenuItem($id)
    {
        $menu = $this->menuRepository->find($id);

        $foodCategories = [];
        $foodItems = [];
        if (!empty($menu->items)) {
            foreach ($menu->items as $item) {
                $foodCategories[] = $item->foodItem->food_category_id;
            }
            $foodItems = $menu->items->pluck('food_item_id')->toArray();
        }

        return view('admin.menu.index-menu-item', [
            'menu' => $menu,
            'foodCategories' => $foodCategories,
            'foodItems' => $foodItems,
        ]);
    }

    public function previewMenuItem(Request $request)
    {
        $menu = $this->menuRepository->find($request->menu_id);

        return response()->json([
            'view' => view('admin.menu.preview-menu-item', [
                'menu' => $menu,
                'foodCategories' => $request->categories,
                'foodItems' => $request->items,
            ])->render(),
        ], 200);
    }

    public function storeMenuItem(SaveMenuItemRequest $request)
    {
        return $this->menuService->storeMenuItem($request->all());
    }

    public function detail(Request $request)
    {
        return $this->menuService->detail($request->id);
    }
}
