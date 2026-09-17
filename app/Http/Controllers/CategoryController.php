<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
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

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        //
    }
}
