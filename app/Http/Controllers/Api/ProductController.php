<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;

class ProductController extends Controller
{
    #[OA\Get(
        path: '/api/products',
        operationId: 'listProducts',
        tags: ['Products'],
        summary: 'List all products',
        description: 'Get a paginated list of all products with their categories',
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', description: 'Page number', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Products retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Products retrieved successfully'),
                        new OA\Property(property: 'data', type: 'object'),
                    ]
                )
            ),
        ]
    )]
    public function index()
    {
        $products = Product::with('category')->paginate(15);
        return response()->json([
            'success' => true,
            'message' => 'Products retrieved successfully',
            'data' => $products,
        ]);
    }

    #[OA\Post(
        path: '/api/products',
        operationId: 'createProduct',
        tags: ['Products'],
        summary: 'Create a new product',
        description: 'Create a new product (Admin only)',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    required: ['category_id', 'name', 'description', 'price', 'stock'],
                    properties: [
                        new OA\Property(property: 'category_id', type: 'integer', example: 1),
                        new OA\Property(property: 'name', type: 'string', example: 'Smartphone'),
                        new OA\Property(property: 'description', type: 'string', example: 'Latest smartphone with great features'),
                        new OA\Property(property: 'price', type: 'number', example: 599.99),
                        new OA\Property(property: 'stock', type: 'integer', example: 50),
                        new OA\Property(property: 'image', type: 'string', format: 'binary', description: 'Image file (jpeg, png, jpg, gif, webp)'),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Product created successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Product created successfully'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Product'),
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        } elseif ($request->filled('image') && $this->isBase64Image($request->input('image'))) {
            $path = $this->saveBase64Image($request->input('image'), 'products');
            if ($path) {
                $validated['image'] = $path;
            }
        }

        $product = Product::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $product->load('category'),
        ], 201);
    }

    #[OA\Get(
        path: '/api/products/{product}',
        operationId: 'showProduct',
        tags: ['Products'],
        summary: 'Get a single product',
        description: 'Get detailed information about a specific product',
        parameters: [
            new OA\Parameter(name: 'product', in: 'path', required: true, description: 'Product ID', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Product retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Product retrieved successfully'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Product'),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Product not found'),
        ]
    )]
    public function show(Product $product)
    {
        return response()->json([
            'success' => true,
            'message' => 'Product retrieved successfully',
            'data' => $product->load('category'),
        ]);
    }

    #[OA\Put(
        path: '/api/products/{product}',
        operationId: 'updateProduct',
        tags: ['Products'],
        summary: 'Update a product',
        description: 'Update an existing product (Admin only)',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'product', in: 'path', required: true, description: 'Product ID', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: 'category_id', type: 'integer', example: 1),
                        new OA\Property(property: 'name', type: 'string', example: 'Smartphone Pro'),
                        new OA\Property(property: 'description', type: 'string', example: 'Updated description'),
                        new OA\Property(property: 'price', type: 'number', example: 699.99),
                        new OA\Property(property: 'stock', type: 'integer', example: 30),
                        new OA\Property(property: 'image', type: 'string', format: 'binary', description: 'Image file'),
                        new OA\Property(property: 'remove_image', type: 'boolean', example: false),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Product updated successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Product updated successfully'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Product'),
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
            'stock' => 'sometimes|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'remove_image' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        } elseif ($request->filled('image') && $this->isBase64Image($request->input('image'))) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $path = $this->saveBase64Image($request->input('image'), 'products');
            $validated['image'] = $path;
        } elseif ($request->filled('remove_image')) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = null;
        } else {
            unset($validated['image']);
        }

        $product->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product->load('category'),
        ]);
    }

    #[OA\Delete(
        path: '/api/products/{product}',
        operationId: 'deleteProduct',
        tags: ['Products'],
        summary: 'Delete a product',
        description: 'Delete an existing product (Admin only)',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'product', in: 'path', required: true, description: 'Product ID', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Product deleted successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Product deleted successfully'),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Product not found'),
        ]
    )]
    public function destroy(Product $product)
    {
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully',
        ]);
    }

    private function isBase64Image(?string $string): bool
    {
        if (empty($string)) {
            return false;
        }
        return preg_match('/^data:image\/(\w+);base64,/', $string) === 1;
    }

    private function saveBase64Image(string $base64, string $dir = 'products'): ?string
    {
        if (!preg_match('/^data:image\/(\w+);base64,/', $base64, $matches)) {
            return null;
        }

        $extension = $matches[1];
        $data = substr($base64, strpos($base64, ',') + 1);
        $decoded = base64_decode($data);
        if ($decoded === false) {
            return null;
        }

        $filename = rtrim($dir, '/') . '/' . Str::random(40) . '.' . $extension;
        Storage::disk('public')->put($filename, $decoded);

        return $filename;
    }
}
