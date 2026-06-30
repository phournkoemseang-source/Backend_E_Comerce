<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;

class CategoryController extends Controller
{
    #[OA\Get(
        path: '/api/categories',
        operationId: 'listCategories',
        tags: ['Categories'],
        summary: 'List all categories',
        description: 'Get a paginated list of all categories with product counts',
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', description: 'Page number', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Categories retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Categories retrieved successfully'),
                        new OA\Property(property: 'data', type: 'object'),
                    ]
                )
            ),
        ]
    )]
    public function index()
    {
        $categories = Category::withCount('products')->paginate(15);
        return response()->json([
            'success' => true,
            'message' => 'Categories retrieved successfully',
            'data' => $categories,
        ]);
    }

    #[OA\Post(
        path: '/api/categories',
        operationId: 'createCategory',
        tags: ['Categories'],
        summary: 'Create a new category',
        description: 'Create a new category (Admin only)',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Electronics'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Category created successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Category created successfully'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Category'),
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $category = Category::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully',
            'data' => $category->loadCount('products'),
        ], 201);
    }

    #[OA\Get(
        path: '/api/categories/{category}',
        operationId: 'showCategory',
        tags: ['Categories'],
        summary: 'Get a single category',
        description: 'Get detailed information about a specific category',
        parameters: [
            new OA\Parameter(name: 'category', in: 'path', required: true, description: 'Category ID', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Category retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Category retrieved successfully'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Category'),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Category not found'),
        ]
    )]
    public function show(Category $category)
    {
        return response()->json([
            'success' => true,
            'message' => 'Category retrieved successfully',
            'data' => $category->loadCount('products'),
        ]);
    }

    #[OA\Put(
        path: '/api/categories/{category}',
        operationId: 'updateCategory',
        tags: ['Categories'],
        summary: 'Update a category',
        description: 'Update an existing category (Admin only)',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'category', in: 'path', required: true, description: 'Category ID', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Home Appliances'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Category updated successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Category updated successfully'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Category'),
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255|unique:categories,name,' . $category->id,
        ]);

        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully',
            'data' => $category->loadCount('products'),
        ]);
    }

    #[OA\Delete(
        path: '/api/categories/{category}',
        operationId: 'deleteCategory',
        tags: ['Categories'],
        summary: 'Delete a category',
        description: 'Delete an existing category (Admin only). Cannot delete categories with products.',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'category', in: 'path', required: true, description: 'Category ID', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Category deleted successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Category deleted successfully'),
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Cannot delete category with products',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Cannot delete category with products'),
                    ]
                )
            ),
        ]
    )]
    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category with products',
            ], 400);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully',
        ]);
    }
}
