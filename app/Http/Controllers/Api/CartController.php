<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

class CartController extends Controller
{
    #[OA\Get(
        path: '/api/cart',
        operationId: 'listCartItems',
        tags: ['Cart'],
        summary: 'List cart items',
        description: 'Get all items in the authenticated user\'s cart',
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Cart items retrieved',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Cart')),
                    ]
                )
            ),
        ]
    )]
    public function index()
    {
        $cartItems = Cart::with('product.category')
            ->where('user_id', Auth::id())
            ->get();

        return response()->json([
            'success' => true,
            'data' => $cartItems,
        ]);
    }

    #[OA\Post(
        path: '/api/cart',
        operationId: 'addToCart',
        tags: ['Cart'],
        summary: 'Add item to cart',
        description: 'Add a product to the authenticated user\'s cart',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['product_id', 'quantity'],
                properties: [
                    new OA\Property(property: 'product_id', type: 'integer', example: 1),
                    new OA\Property(property: 'quantity', type: 'integer', example: 2),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Item added to cart successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Item added to cart successfully'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Cart'),
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $cartItem = Cart::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'product_id' => $request->product_id,
            ],
            [
                'quantity' => $request->quantity,
            ]
        );

        $cartItem->load('product.category');

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart successfully',
            'data' => $cartItem,
        ]);
    }

    #[OA\Put(
        path: '/api/cart/{id}',
        operationId: 'updateCartItem',
        tags: ['Cart'],
        summary: 'Update cart item quantity',
        description: 'Update the quantity of an item in the authenticated user\'s cart',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Cart item ID', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['quantity'],
                properties: [
                    new OA\Property(property: 'quantity', type: 'integer', example: 3),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Cart item updated successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Cart item updated successfully'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Cart'),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Cart item not found'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function update(Request $request, string $id)
    {
        $cartItem = Cart::where('user_id', Auth::id())
            ->where('id', $id)
            ->first();

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Cart item not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $cartItem->update([
            'quantity' => $request->quantity,
        ]);

        $cartItem->load('product.category');

        return response()->json([
            'success' => true,
            'message' => 'Cart item updated successfully',
            'data' => $cartItem,
        ]);
    }

    #[OA\Delete(
        path: '/api/cart/{id}',
        operationId: 'deleteCartItem',
        tags: ['Cart'],
        summary: 'Remove item from cart',
        description: 'Remove an item from the authenticated user\'s cart',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Cart item ID', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Item removed from cart successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Item removed from cart successfully'),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Cart item not found'),
        ]
    )]
    public function destroy(string $id)
    {
        $cartItem = Cart::where('user_id', Auth::id())
            ->where('id', $id)
            ->first();

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Cart item not found',
            ], 404);
        }

        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart successfully',
        ]);
    }
}
