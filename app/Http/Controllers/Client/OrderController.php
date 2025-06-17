<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Repositories\MenuRepository;
use App\Repositories\UserRepository;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $menuRepository;
    protected $orderService;
    protected $userRepository;

    public function __construct(
        MenuRepository $menuRepository,
        OrderService $orderService,
        UserRepository $userRepository,
    )
    {
        $this->menuRepository = $menuRepository;
        $this->orderService = $orderService;
        $this->userRepository = $userRepository;
    }

    public function index()
    {
        $users = $this->userRepository->query()->where('status', config('constants.ACTIVE'))->get();
        $cart = session()->get('cart', []);

        $menu = $this->menuRepository->query()
            ->where('status', config('constants.ACTIVE'))
            ->whereDate('created_at', now())
            ->first();

        $foodCategories = [];
        $foodItems = [];
        if (!empty($menu->items)) {
            foreach ($menu->items as $item) {
                $foodCategories[] = $item->foodItem->food_category_id;
            }
            $foodItems = $menu->items->pluck('food_item_id')->toArray();
        }

        return view('client.order.index', [
            'menu' => $menu,
            'foodCategories' => $foodCategories,
            'foodItems' => $foodItems,
            'users' => $users,
            'cart' => $cart,
        ]);
    }

    public function getTotalOrder(Request $request)
    {
        $items = $request->items;
        return $this->orderService->getTotalOrder($items);
    }

    public function addToOrder(Request $request)
    {
        return $this->orderService->addToOrder($request->all());
    }

    public function removeItemToOrder(Request $request)
    {
        return $this->orderService->removeItemToOrder($request->all());
    }

    public function checkoutOrder(Request $request)
    {
        return $this->orderService->checkoutOrder($request->all());
    }
}
