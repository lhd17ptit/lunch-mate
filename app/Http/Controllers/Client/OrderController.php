<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Shop;
use App\Repositories\FloorRepository;
use App\Repositories\MenuRepository;
use App\Repositories\UserRepository;
use App\Services\OrderService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $menuRepository;
    protected $orderService;
    protected $userRepository;
    protected $floorRepository;

    public function __construct(
        MenuRepository $menuRepository,
        OrderService $orderService,
        UserRepository $userRepository,
        FloorRepository $floorRepository
    )
    {
        $this->menuRepository = $menuRepository;
        $this->orderService = $orderService;
        $this->userRepository = $userRepository;
        $this->floorRepository = $floorRepository;
    }

    public function landingPage()
    {
        $lunchMate = $this->menuRepository->query()
            ->where('status', config('constants.ACTIVE'))
            ->whereHas('shop', function ($q) {
                $q->where('slug', Shop::LUNCH_MATE);
            })
            ->whereDate('created_at', now())
            ->first();

        $breakfastMate = $this->menuRepository->query()
            ->where('status', config('constants.ACTIVE'))
            ->whereHas('shop', function ($q) {
                $q->where('slug', Shop::BREAKFAST_MATE);
            })
            ->whereDate('created_at', Carbon::now()->subDay())
            ->first();
        
        $afternoonMate = $this->menuRepository->query()
            ->where('status', config('constants.ACTIVE'))
            ->whereHas('shop', function ($q) {
                $q->where('slug', Shop::AFTERNOON_MATE);
            })
            ->whereDate('created_at', now())
            ->first();


        return view('client.landing-page.index', [
            'lunchMate' => !empty($lunchMate) ? Shop::LUNCH_MATE : false,
            'breakfastMate' => !empty($breakfastMate) ? Shop::BREAKFAST_MATE : false,
            'afternoonMate' => !empty($afternoonMate) ? Shop::AFTERNOON_MATE : false,
        ]);
    }

    public function menuByShop(Request $request, $shop)
    {

        // handle cancel payment
        if(($request->cancel ?? null) == 'true'){
            $orderCode = $request->orderCode;
            $order = Order::where('order_code', $orderCode)->first();
            if(!empty($order)){
                $order->update([
                    'status' => config('constants.ORDER_STATUS_CANCELLED'),
                ]);
            }
        }

        $floors = $this->floorRepository->getAll();
        $users = $this->userRepository->query()->where('status', config('constants.ACTIVE'))->where('is_from_client', false)->whereNotNull('name')->orderBy('name', 'asc')->get();
        $cart = session()->get('cart', []);

        $menu = $this->menuRepository->query()
            ->where('status', config('constants.ACTIVE'))
            ->whereHas('shop', function ($q) use ($shop) {
                $q->where('slug', $shop);
            })
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
            'floors' => $floors,
            'selectedItemIds' => $request->selectedItemIds ?? [],
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
		$request['ip_address'] = $request->ip() ?? null;
        return $this->orderService->checkoutOrder($request->all());
    }

    public function listOrder(Request $request)
    {
        $listOrders = $this->orderService->getListOrder($request->all());

        return view('client.order-history.index', [
            'listOrders' => $listOrders,
        ]);
    }

    public function leaderboard()
    {
        $listUserTip = $this->orderService->leaderboard();

        return view('client.leaderboard.index', [
            'listUserTip' => $listUserTip,
        ]);
    }

    public function getNewsTip()
    {
        $tipToday = $this->orderService->tipToday();

        return response()->json([
            'status' => true,
            'message' => 'Get list tip successfully',
            'data' => [
                'view' => view('client.common.noti-tip', [
                    'tipToday' => $tipToday,
                ])->render(),
                'tipToday' => $tipToday,
            ],
        ], 200);
    }
}
