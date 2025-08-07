<?php

namespace App\Services;

use App\Exports\OrderExport;
use App\Repositories\FoodCategoryRepository;
use App\Repositories\FoodItemRepository;
use App\Repositories\OrderRepository;
use App\Repositories\OrderServingFoodItemRepository;
use App\Repositories\OrderServingRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\VNP\ProcessPaymentRequest;
use App\Http\Requests\PayOS\ProcessPaymentRequest as PayOsPaymentRequest;

class OrderService
{

    protected $foodItemRepository;
    protected $foodCategoryRepository;
    protected $userRepository;
    protected $orderRepository;
    protected $orderServingRepository;
    protected $orderServingFoodItemRepository;
    protected $vnpayService;
    protected $payOsService;
    
    public function __construct(
        FoodItemRepository $foodItemRepository,
        FoodCategoryRepository $foodCategoryRepository,
        UserRepository $userRepository,
        OrderRepository $orderRepository,
        OrderServingRepository $orderServingRepository,
        OrderServingFoodItemRepository $orderServingFoodItemRepository,
        VnpayService $vnpayService,
        PayOsService $payOsService,
    )
    {
        $this->foodItemRepository = $foodItemRepository;
        $this->foodCategoryRepository = $foodCategoryRepository;
        $this->userRepository = $userRepository;
        $this->orderRepository = $orderRepository;
        $this->orderServingRepository = $orderServingRepository;
        $this->orderServingFoodItemRepository = $orderServingFoodItemRepository;
        $this->vnpayService = $vnpayService;
        $this->payOsService = $payOsService;
    }

    public function getTotalOrder($items)
    {
        $total = 0;
        $foodItems = $this->foodItemRepository->query()->whereIn('id', $items)->get()->keyBy('id');
        $foodCategories = $this->foodCategoryRepository->getAll()->keyBy('id');

        $foodCategory = null;
        foreach ($items as $item) {
            if (!empty($foodItems[$item])) {
                if (empty($foodCategory)) {
                    $foodCategory = $foodItems[$item]->food_category_id ?? null;

                    if ($foodItems[$item]->type == 4) {
                        return response()->json([
                            'status' => false,
                            'message' => 'Vui lòng chọn món trước khi thêm topping',
                        ], 400);
                    }
                } else {
                    if ($foodCategory != $foodItems[$item]->food_category_id) {
                        return response()->json([
                            'status' => false,
                            'message' => 'Vui lòng chọn món cùng danh mục',
                        ], 400);
                    }

                    if (empty($foodItems[$item]->type)) {
                        $nameCategory = $foodCategories[$foodCategory]->name ?? '';
                        return response()->json([
                            'status' => false,
                            'message' => $nameCategory .' không thể chọn nhiều loại',
                        ], 400);
                    }
                }
                $total += $foodItems[$item]->price;
            }
        }
        $priceCategory = $foodCategories[$foodCategory]->price ?? 0;
        $total += $priceCategory;

        return response()->json([
            'status' => true,
            'total' => $total
        ], 200);
    }

    public function addToOrder($data)
    {
        $items = $data['items'] ?? [];

        if ($data['type_user'] == 1) { // has account
            $user_id = $data['user_id'] ?? null;
            $user = $this->userRepository->findOrFail($user_id);
        } else {
            $user = $this->userRepository->create([
                'name' => $data['user_name'],
                'floor_id' => $data['floor_id'],
                'is_from_client' => true
            ]);
        }

        $foodItems = $this->foodItemRepository->query()->whereIn('id', $items)->get()->keyBy('id');
        $foodCategories = $this->foodCategoryRepository->getAll()->keyBy('id');

        $cart = session()->get('cart', []);

        $foodCategory = null;
        $dataItems = [];
        $total = 0;
        $tip = $data['tip'] ?? 0;
        if($tip < 0) $tip = 0;
        $total += $tip;
        foreach ($items as $item) {
            if (!empty($foodItems[$item])) {
                if (empty($foodCategory)) {
                    $foodCategory = $foodItems[$item]->food_category_id ?? null;

                    if ($foodItems[$item]->type == 4) {
                        return response()->json([
                            'status' => false,
                            'message' => 'Vui lòng chọn món trước khi thêm topping',
                        ], 400);
                    }
                } else {
                    if ($foodCategory != $foodItems[$item]->food_category_id) {
                        return response()->json([
                            'status' => false,
                            'message' => 'Vui lòng chọn món cùng danh mục',
                        ], 400);
                    }

                    if (empty($foodItems[$item]->type)) {
                        $nameCategory = $foodCategories[$foodCategory]->name ?? '';
                        return response()->json([
                            'status' => false,
                            'message' => $nameCategory .' không thể chọn nhiều loại',
                        ], 400);
                    }
                }

                $dataItems[] = [
                    'id' => $foodItems[$item]->id,
                    'title' => $foodItems[$item]->name,
                    'quantity' => 1,
                    'price' => $foodItems[$item]->price,
                ];
                $total += $foodItems[$item]->price;
            }
        }
        $priceCategory = $foodCategories[$foodCategory]->price ?? 0;
        $total += $priceCategory;

        if (!empty($foodCategories[$foodCategory]->key) && $foodCategories[$foodCategory]->key == 'com' && $total < 40) {
            return response()->json([
                'status' => false,
                'message' => 'Vui lòng chọn thêm món. Suất ăn tối thiểu là 40.000 VNĐ',
            ], 400);
        }
        
        $cart[] = [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'items' => $dataItems,
            'total' => $total,
            'price_category' => $priceCategory,
            'name_category' => $foodCategories[$foodCategory]->name ?? '',
            'created_at' => now(),
        ];

        session()->put('cart', $cart);
        session()->put('tip', $tip);

        return response()->json([
            'status' => true,
            'message' => 'Add to cart successfully',
            'data' => [
                'view' => view('client.order.order-list', ['cart' => $cart])->render(),
                'count' => count($cart),
                'tip' => $tip,
            ],
        ], 200);
    }

    public function removeItemToOrder($data)
    {
        $id = $data['id'] ?? null;
        $cart = session()->get('cart', []);
        
        $tip = $data['tip'] ?? 0;
        if($tip < 0) $tip = 0;

        unset($cart[$id]);
        session()->put('cart', $cart);
        if(count($cart) == 0) session()->forget('tip');

        return response()->json([
            'status' => true,
            'message' => 'Add to cart successfully',
            'data' => [
                'view' => view('client.order.order-list', ['cart' => $cart])->render(),
                'count' => count($cart),
                'tip' => $tip,
            ],
        ], 200);
    }

    public function checkoutOrder($data)
    {
        $cart = session()->get('cart', []);
        // $user = $this->userRepository->find($user->id);
        $total = array_sum(array_column($cart, 'total'));
		$orderCode = now()->timestamp;
        if(!isset($data['tip']) || $data['tip'] < 0) $data['tip'] = 0;
        $total += $data['tip'];

        try {
            DB::beginTransaction();
            $user = $this->userRepository->create([
                'guest_token' => Str::uuid(),
            ]);

            $order = $this->orderRepository->create([
                'user_id' => $user->id ?? null,
                'floor_id' => $user->floor_id ?? 0,
                'total' => $total,
                'status' => config('constants.ORDER_STATUS_PENDING'),
                'order_code' => $orderCode,
                'tip' => $data['tip'],
            ]);

            foreach ($cart as $item) {
                $orderServings = $this->orderServingRepository->create([
                    'order_id' => $order->id,
                    'amount' => $item['total'],
                    'user_id' => $item['user_id'],
                ]);

                $lineItems = [];
                foreach ($item['items'] as $item) {
                    $lineItems[] = [
                        'order_serving_id' => $orderServings->id,
                        'food_item_id' => $item['id'],
                        'amount' => $item['price'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                $this->orderServingFoodItemRepository->createMultiple($lineItems);
            }
            session()->forget('cart');
            session()->forget('tip');
            DB::commit();

			//redirect to payment page - PayOS
			$request = new PayOsPaymentRequest([
				'amount' => ($total ?? 0) * 1000,
				'description' => $orderCode,
                'code' => $orderCode,
			]);
			return redirect()->away($this->payOsService->processPayment($request));

            // return response()->json([
            //     'status' => true,
            //     'message' => 'Save order successfully'
            // ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 400);
        }
        
    }

    public function getListAnalytics($data)
    {
        {
            $items = $this->orderServingRepository->query();

            if (!empty($data['status'])) {
                $items->whereHas('order', function ($query) use ($data) {
                    $query->where('status', $data['status']);
                });
            }
    
            if (!empty($data['search_date'])) {
                $items->whereDate('created_at', $data['search_date']);
            }

            if (!empty($data['search_floor'])) {
                $items->whereHas('user', function ($query) use ($data) {
                    $query->where('floor_id', $data['search_floor']);
                });
            }
    
            $items->orderBy('id', 'desc')->get();
    
            return DataTables::of($items)
            ->addColumn('user', function ($item) {
                return $item->user->name ?? 'Chưa xác định';
            })
            ->addColumn('created_at', function ($item) {
                return date('d/m/Y', strtotime($item->created_at));
            })
            ->addColumn('floor', function ($item) {
                return $item->user->floor->name ?? 'Chưa xác định';
            })
            ->addColumn('amount', function ($item) {
                return $item->amount.',000';
            })
            ->addColumn('status', function ($item) {
                return config("constants.ORDER_STATUS_TEXT." . $item->order->status);
            })
            ->addColumn('action', function ($item) {
                return '<a class="btn btn-danger btn-detail btn-sm mr-1" data-id="'.$item->id.'"><i class="fa fa-eye text-white"></i></a>';
            })
            ->rawColumns(['user', 'action', 'floor', 'created_at'])
            ->make(true);
        }
    }

    public function exportAnalytics($data)
    {
        return Excel::download(new OrderExport($data), 'order.xlsx');
    }

    public function detailAnalytics($data)
    {
        $orderServing = $this->orderServingRepository->find($data['id']);

        return response()->json([
            'status' => true,
            'data' => [
                'view' => view('admin.analytics.detail', ['orderServing' => $orderServing])->render(),
                'orderServing' => $orderServing,
            ]
        ], 200);
    }

    public function getListOrder()
    {
        $items = $this->orderServingRepository->query();

        $items->whereHas('order', function ($query) {
            $query->where('status', config('constants.ORDER_STATUS_SUCCESS'));
        });
    
        $items->whereDate('created_at', date('Y-m-d', time()));
    
        $items = $items->with(['orderServingFoodItems', 'orderServingFoodItems.foodItem', 'orderServingFoodItems.foodItem.foodCategory', 'user'])->orderBy('id', 'asc')->get();

        return $items;
    }
}