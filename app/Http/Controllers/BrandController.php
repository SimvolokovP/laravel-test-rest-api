<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBrandRequest;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class BrandController extends Controller
{
    #[OA\Get(
        path: "/api/brands",
        summary: "Получить список всех брендов",
        tags: ["Brands"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Список брендов получен",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(
                        type: "object",
                        properties: [
                            new OA\Property(property: "id", type: "integer", example: 1),
                            new OA\Property(property: "title", type: "string", example: "Apple")
                        ]
                    )
                )
            )
        ]
    )]
    public function index()
    {
        return BrandResource::collection(Brand::all());
    }

    #[OA\Post(
        path: "/api/brands",
        summary: "Создать новую категорию",
        tags: ["Brands"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["title"],
                properties: [
                    new OA\Property(property: "title", type: "string", example: "Apple")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Бренд успешно создан"),
            new OA\Response(response: 422, description: "Ошибка валидации (например, имя уже занято)")
        ]
    )]
    public function store(StoreBrandRequest $request)
    {
        $validated = $request->validated();
        $brand = Brand::create($validated);

        return (new BrandResource($brand))
            ->response()
            ->setStatusCode(201);
    }

    #[OA\Get(
        path: "/api/brands/{id}",
        summary: "Получить конкретный бренд по ID",
        tags: ["Brands"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Бренд найден"),
            new OA\Response(response: 404, description: "Бренд не найден")
        ]
    )]
    public function show(Brand $brand): BrandResource
    {
        return new BrandResource($brand);
    }

    #[OA\Put(
        path: "/api/brands/{id}",
        summary: "Обновить название бренда",
        tags: ["Brands"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["title"],
                properties: [
                    new OA\Property(property: "title", type: "string", example: "Новое название бренда")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Бренд успешно обновлен"),
            new OA\Response(response: 422, description: "Ошибка валидации")
        ]
    )]
    public function update(Request $request, Brand $brand): BrandResource
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:categories,title,' . $brand->id,
        ]);

        $brand->update($validated);

        return new BrandResource($brand);
    }

    #[OA\Delete(
        path: "/api/brands/{id}",
        summary: "Удалить бренд",
        tags: ["Brands"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Бренд успешно удален"),
            new OA\Response(response: 400, description: "Ошибка: бренд нельзя удалить, так как к ней привязаны товары")
        ]
    )]
    public function destroy(Brand $brand): JsonResponse
    {
        if ($brand->products()->exists()) {
            return response()->json([
                'error' => 'Cannot delete category',
                'message' => 'Невозможно удалить бренд "' . $brand->title . '", так как к ней привязаны товары. Сначала удалите или перенесите эти товары.'
            ], 400);
        }

        $brand->delete();

        return response()->json([
            'message' => 'Brand deleted successfully'
        ], 200);
    }
}
