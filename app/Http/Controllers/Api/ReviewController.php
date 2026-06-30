<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

class ReviewController extends Controller
{
    #[OA\Get(
        path: '/api/reviews',
        operationId: 'listReviews',
        tags: ['Reviews'],
        summary: 'List all reviews',
        description: 'Get all reviews, optionally filtered by product',
        parameters: [
            new OA\Parameter(name: 'product_id', in: 'query', description: 'Filter by product ID', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Reviews retrieved',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Review')),
                    ]
                )
            ),
        ]
    )]
    public function index(Request $request)
    {
        $query = Review::with('user:id,name');

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        $reviews = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $reviews,
        ]);
    }

    #[OA\Get(
        path: '/api/reviews/{id}',
        operationId: 'showReview',
        tags: ['Reviews'],
        summary: 'Get a single review',
        description: 'Get detailed information about a specific review',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Review ID', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Review retrieved',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Review'),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Review not found'),
        ]
    )]
    public function show(string $id)
    {
        $review = Review::with('user:id,name')->find($id);

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $review,
        ]);
    }

    #[OA\Post(
        path: '/api/reviews',
        operationId: 'createReview',
        tags: ['Reviews'],
        summary: 'Create a review',
        description: 'Submit a review for a product (one review per product per user)',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['product_id', 'rating'],
                properties: [
                    new OA\Property(property: 'product_id', type: 'integer', example: 1),
                    new OA\Property(property: 'rating', type: 'integer', example: 5, description: 'Rating from 1 to 5'),
                    new OA\Property(property: 'comment', type: 'string', example: 'Great product!'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Review submitted successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Review submitted successfully'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Review'),
                    ]
                )
            ),
            new OA\Response(response: 409, description: 'Already reviewed this product'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $existing = Review::where('user_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reviewed this product',
            ], 409);
        }

        $review = Review::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        $review->load('user:id,name');

        return response()->json([
            'success' => true,
            'message' => 'Review submitted successfully',
            'data' => $review,
        ], 201);
    }

    #[OA\Put(
        path: '/api/reviews/{id}',
        operationId: 'updateReview',
        tags: ['Reviews'],
        summary: 'Update a review',
        description: 'Update an existing review (must be the review owner)',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Review ID', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['rating'],
                properties: [
                    new OA\Property(property: 'rating', type: 'integer', example: 4, description: 'Rating from 1 to 5'),
                    new OA\Property(property: 'comment', type: 'string', example: 'Updated review comment'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Review updated successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Review updated successfully'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Review'),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Review not found'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function update(Request $request, string $id)
    {
        $review = Review::where('user_id', Auth::id())
            ->where('id', $id)
            ->first();

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        $review->load('user:id,name');

        return response()->json([
            'success' => true,
            'message' => 'Review updated successfully',
            'data' => $review,
        ]);
    }

    #[OA\Delete(
        path: '/api/reviews/{id}',
        operationId: 'deleteReview',
        tags: ['Reviews'],
        summary: 'Delete a review',
        description: 'Delete an existing review (must be the review owner)',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Review ID', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Review deleted successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Review deleted successfully'),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Review not found'),
        ]
    )]
    public function destroy(string $id)
    {
        $review = Review::where('user_id', Auth::id())
            ->where('id', $id)
            ->first();

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found',
            ], 404);
        }

        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Review deleted successfully',
        ]);
    }
}
