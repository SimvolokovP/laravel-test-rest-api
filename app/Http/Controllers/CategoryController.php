<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class CategoryController extends Controller
{
    #[OA\Get(
        path: "/api/categories",
        summary: "Получить список всех категорий",
        tags: ["Categories"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Список категорий получен",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(
                        type: "object",
                        properties: [
                            new OA\Property(property: "id", type: "integer", example: 1),
                            new OA\Property(property: "title", type: "string", example: "Электроника")
                        ]
                    )
                )
            )
        ]
    )]
    public function index()
    {
        return CategoryResource::collection(Category::all());
    }

    #[OA\Post(
        path: "/api/categories",
        summary: "Создать новую категорию",
        tags: ["Categories"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["title"],
                properties: [
                    new OA\Property(property: "title", type: "string", example: "Бытовая техника")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Категория успешно создана"),
            new OA\Response(response: 422, description: "Ошибка валидации (например, имя уже занято)")
        ]
    )]
    public function store(StoreCategoryRequest $request)
    {
        $validated = $request->validated();
        $category = Category::create($validated);

        return (new CategoryResource($category))
            ->response()
            ->setStatusCode(201);
    }

    #[OA\Get(
        path: "/api/categories/{id}",
        summary: "Получить конкретную категорию по ID",
        tags: ["Categories"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Категория найдена"),
            new OA\Response(response: 404, description: "Категория не найдена")
        ]
    )]
    public function show(Category $category): CategoryResource
    {
        return new CategoryResource($category);
    }

    #[OA\Put(
        path: "/api/categories/{id}",
        summary: "Обновить название категории",
        tags: ["Categories"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["title"],
                properties: [
                    new OA\Property(property: "title", type: "string", example: "Новое название категории")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Категория успешно обновлена"),
            new OA\Response(response: 422, description: "Ошибка валидации")
        ]
    )]
    public function update(Request $request, Category $category): CategoryResource
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:categories,title,' . $category->id,
        ]);

        $category->update($validated);

        return new CategoryResource($category);
    }

    #[OA\Delete(
        path: "/api/categories/{id}",
        summary: "Удалить категорию",
        tags: ["Categories"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Категория успешно удалена"),
            new OA\Response(response: 400, description: "Ошибка: категорию нельзя удалить, так как к ней привязаны товары")
        ]
    )]
    public function destroy(Category $category): JsonResponse
    {
        if ($category->products()->exists()) {
            return response()->json([
                'error' => 'Cannot delete category',
                'message' => 'Невозможно удалить категорию "' . $category->title . '", так как к ней привязаны товары. Сначала удалите или перенесите эти товары.'
            ], 400);
        }

        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully'
        ], 200);
    }
}
