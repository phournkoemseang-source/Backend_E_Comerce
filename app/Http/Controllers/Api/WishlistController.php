<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

class WishlistController extends Controller
{
    #[OA\Get(
        path: '/api/wishlist',
        operationId: 'listWishlistItems',
        tags: ['Wishlist'],
        summary: 'List wishlist items',
        description: 'Get all items in the authenticated user\'s wishlist',
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Wishlist items retrieved',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Wishlist')),
                    ]
                )
            ),
        ]
    )]
    public function index()
    {
        $wishlistItems = Wishlist::with('product.category')
            ->where('user_id', Auth::id())
            ->get();

        return response()->json([
            'success' => true,
            'data' => $wishlistItems,
        ]);
    }

    #[OA\Post(
        path: '/api/wishlist',
        operationId: 'addToWishlist',
        tags: ['Wishlist'],
        summary: 'Add item to wishlist',
        description: 'Add a product to the authenticated user\'s wishlist',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['product_id'],
                properties: [
                    new OA\Property(property: 'product_id', type: 'integer', example: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Product added to wishlist successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Product added to wishlist successfully'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Wishlist'),
                    ]
                )
            ),
            new OA\Response(response: 409, description: 'Product already in wishlist'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $exists = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Product is already in your wishlist',
            ], 409);
        }

        $wishlistItem = Wishlist::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
        ]);

        $wishlistItem->load('product.category');

        return response()->json([
            'success' => true,
            'message' => 'Product added to wishlist successfully',
            'data' => $wishlistItem,
        ]);
    }

    #[OA\Delete(
        path: '/api/wishlist/{id}',
        operationId: 'deleteWishlistItem',
        tags: ['Wishlist'],
        summary: 'Remove item from wishlist',
        description: 'Remove an item from the authenticated user\'s wishlist',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Wishlist item ID', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Product removed from wishlist successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Product removed from wishlist successfully'),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Wishlist item not found'),
        ]
    )]
    public function destroy(string $id)
    {
        $wishlistItem = Wishlist::where('user_id', Auth::id())
            ->where('id', $id)
            ->first();

        if (!$wishlistItem) {
            return response()->json([
                'success' => false,
                'message' => 'Wishlist item not found',
            ], 404);
        }

        $wishlistItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product removed from wishlist successfully',
        ]);
    }
}
