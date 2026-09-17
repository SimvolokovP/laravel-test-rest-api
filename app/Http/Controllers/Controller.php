<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(title: "Market API", version: "1.0.0", description: "Документация REST API для магазина товаров")]
#[OA\Server(url: "http://127.0.0.1:8000", description: "Локальный сервер разработки")]
abstract class Controller
{
    //
}
