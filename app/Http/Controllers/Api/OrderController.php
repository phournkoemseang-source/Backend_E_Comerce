<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

class OrderController extends Controller
{
    #[OA\Get(
        path: '/api/orders',
        operationId: 'listOrders',
        tags: ['Orders'],
        summary: 'List user orders',
        description: 'Get all orders for the authenticated user',
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Orders retrieved',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Order')),
                    ]
                )
            ),
        ]
    )]
    public function index()
    {
        $orders = Order::with('items.product.category')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    #[OA\Get(
        path: '/api/orders/{id}',
        operationId: 'showOrder',
        tags: ['Orders'],
        summary: 'Get order details',
        description: 'Get detailed information about a specific order',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Order ID', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Order retrieved',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Order'),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Order not found'),
        ]
    )]
    public function show(string $id)
    {
        $order = Order::with('items.product.category')
            ->where('user_id', Auth::id())
            ->where('id', $id)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $order,
        ]);
    }

    #[OA\Post(
        path: '/api/checkout',
        operationId: 'checkout',
        tags: ['Orders'],
        summary: 'Checkout and place order',
        description: 'Place an order from the authenticated user\'s cart items',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['shipping_address'],
                properties: [
                    new OA\Property(property: 'shipping_address', type: 'string', example: '123 Main St, Phnom Penh'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Order placed successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Order placed successfully'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Order'),
                    ]
                )
            ),
            new OA\Response(response: 400, description: 'Cart is empty'),
            new OA\Response(response: 422, description: 'Validation error'),
            new OA\Response(response: 500, description: 'Failed to place order'),
        ]
    )]
    public function checkout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shipping_address' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $userId = Auth::id();
        $cartItems = Cart::with('product')
            ->where('user_id', $userId)
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Your cart is empty',
            ], 400);
        }

        $totalAmount = $cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        try {
            DB::beginTransaction();

            $order = Order::create([
                'user_id' => $userId,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'shipping_address' => $request->shipping_address,
            ]);

            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->product->price,
                ]);
            }

            Cart::where('user_id', $userId)->delete();

            DB::commit();

            $order->load('items.product.category');

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'data' => $order,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to place order. Please try again.',
            ], 500);
        }
    }
}
