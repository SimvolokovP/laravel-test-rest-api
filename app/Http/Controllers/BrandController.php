<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBrandRequest;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
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

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        //
    }
}
