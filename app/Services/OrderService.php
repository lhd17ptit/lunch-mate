<?php

namespace App\Services;

use App\Repositories\FoodCategoryRepository;
use App\Repositories\FoodItemRepository;
use App\Repositories\OrderRepository;
use App\Repositories\OrderServingFoodItemRepository;
use App\Repositories\OrderServingRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{

    protected $foodItemRepository;
    protected $foodCategoryRepository;
    protected $userRepository;
    protected $orderRepository;
    protected $orderServingRepository;
    protected $orderServingFoodItemRepository;
    
    public function __construct(
        FoodItemRepository $foodItemRepository,
        FoodCategoryRepository $foodCategoryRepository,
        UserRepository $userRepository,
        OrderRepository $orderRepository,
        OrderServingRepository $orderServingRepository,
        OrderServingFoodItemRepository $orderServingFoodItemRepository
    )
    {
        $this->foodItemRepository = $foodItemRepository;
        $this->foodCategoryRepository = $foodCategoryRepository;
        $this->userRepository = $userRepository;
        $this->orderRepository = $orderRepository;
        $this->orderServingRepository = $orderServingRepository;
        $this->orderServingFoodItemRepository = $orderServingFoodItemRepository;
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
        $user_id = $data['user_id'] ?? null;
        $user = $this->userRepository->findOrFail($user_id);

        $foodItems = $this->foodItemRepository->query()->whereIn('id', $items)->get()->keyBy('id');
        $foodCategories = $this->foodCategoryRepository->getAll()->keyBy('id');

        $cart = session()->get('cart', []);

        $foodCategory = null;
        $dataItems = [];
        $total = 0;
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
        
        $cart[] = [
            'user_id' => $user_id,
            'user_name' => $user->name,
            'items' => $dataItems,
            'total' => $total,
            'price_category' => $priceCategory,
            'name_category' => $foodCategories[$foodCategory]->name ?? '',
            'created_at' => now(),
        ];

        session()->put('cart', $cart);

        return response()->json([
            'status' => true,
            'message' => 'Add to cart successfully',
            'data' => [
                'view' => view('client.order.order-list', ['cart' => $cart])->render(),
                'count' => count($cart),
            ],
        ], 200);
    }

    public function removeItemToOrder($data)
    {
        $id = $data['id'] ?? null;
        $cart = session()->get('cart', []);
        unset($cart[$id]);
        session()->put('cart', $cart);

        return response()->json([
            'status' => true,
            'message' => 'Add to cart successfully',
            'data' => [
                'view' => view('client.order.order-list', ['cart' => $cart])->render(),
                'count' => count($cart),
            ],
        ], 200);
    }

    public function checkoutOrder($data)
    {
        $cart = session()->get('cart', []);
        // $user = Auth::guard('user')->user ?? null;
        // $user = $this->userRepository->find($user->id);

        $total = array_sum(array_column($cart, 'total'));

        try {
            DB::beginTransaction();
            $order = $this->orderRepository->create([
                'user_id' => $user->id ?? null,
                'floor_id' => $user->floor_id ?? null,
                'total' => $total,
                'status' => config('constants.ORDER_STATUS_PENDING'),
                'order_code' => 'ORD-'. Str::uuid(),
            ]);

            foreach ($cart as $item) {
                $orderServings = $this->orderServingRepository->create([
                    'order_id' => $order->id,
                    'total' => $item['total'],
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
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Save order successfully'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 400);
        }
        
    }
}