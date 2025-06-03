<?php

namespace App\Services;

use App\Libraries\Upload;
use App\Models\Order;
use App\Repositories\OrderDetailRepository;
use App\Repositories\OrderRepository;
use Illuminate\Support\Facades\Auth;

class OrderService
{
    const ROOT_PATH_STORAGE = '/orders';

    protected $orderRepository;
    protected $orderDetailRepository;

    public function __construct(
        OrderRepository $orderRepository,
        OrderDetailRepository $orderDetailRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderDetailRepository = $orderDetailRepository;
    }

    public function checkout($data, $cart)
    {
        $userId = auth()->check() ? auth()->id() : null;

        $order = $this->orderRepository->create([
            'user_id' => $userId,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'status' => Order::STATUS['PENDING'],
            'type' => Order::TYPE['SYSTEM'],
            'total' => array_sum(array_column($cart, 'price')),
            'payment_method' => $data['type'] ?? null,
        ]);

        if (!empty($data['image'])) {
            $id = !empty($order) ? $order->id : '';
            $folder = self::ROOT_PATH_STORAGE . '/' . $id;
            $fileName = Upload::storeFile($data['image'], $folder);
            if ($fileName) {
                $order->update([
                    'bill_of_customer' => $fileName,
                ]);
            }
        }

        foreach ($cart as $item) {
            $this->orderDetailRepository->create([
                'order_id' => $order->id,
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total' => $item['price'] * $item['quantity'],
                'size' => $item['size'] ?? null,
                'color' => $item['color'] ?? null,
            ]);
        }

        // Clear the cart session
        session()->forget('cart');

        return response()->json([
            'status' => true,
            'message' => 'Order successfully',
            'data' => [
                'order_id' => $order->id,
                'total' => $order->total,
            ],
        ], 200);
    }
}