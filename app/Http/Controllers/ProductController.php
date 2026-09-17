<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes as OA;

class ProductController extends Controller
{
    #[OA\Get(
        path: "/api/products",
        summary: "Получить список всех товаров",
        description: "Возвращает массив товаров со связанными объектами категорий и брендов",
        tags: ["Products"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Успешный возврат списка товаров",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(
                        type: "object",
                        properties: [
                            new OA\Property(property: "id", type: "integer", example: 1),
                            new OA\Property(property: "title", type: "string", example: "iPhone 15 Pro"),
                            new OA\Property(property: "price", type: "number", format: "float", example: 999.99)
                        ]
                    )
                )
            )
        ]
    )]
    public function index(): AnonymousResourceCollection
    {
        $products = Product::with(['category', 'brand'])->get();
        return ProductResource::collection($products);
    }

    #[OA\Post(
        path: "/api/products",
        summary: "Создать новый товар",
        tags: ["Products"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["title", "price", "category_id", "brand_id"],
                properties: [
                    new OA\Property(property: "title", type: "string", example: "MacBook Pro M3"),
                    new OA\Property(property: "description", type: "string", example: "Мощный ноутбук"),
                    new OA\Property(property: "price", type: "number", format: "float", example: 1999.99),
                    new OA\Property(property: "category_id", type: "integer", example: 1),
                    new OA\Property(property: "brand_id", type: "integer", example: 1)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Товар успешно создан"),
            new OA\Response(response: 422, description: "Ошибка валидации")
        ]
    )]
    public function store(StoreProductRequest $request): JsonResponse
    {
        $validatedProduct = $request->validated();
        $product = Product::create($validatedProduct);

        return (new ProductResource($product->load(['category', 'brand'])))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Product $product): ProductResource
    {
        return new ProductResource($product->load(['category', 'brand']));
    }

    public function update(Request $request, Product $product): ProductResource
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'category_id' => 'sometimes|integer|exists:categories,id',
            'brand_id' => 'sometimes|integer|exists:brands,id',
        ]);

        $product->update($validated);
        return new ProductResource($product->load(['category', 'brand']));
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();
        return response()->json(['message' => 'Product deleted successfully'], 200);
    }
}
