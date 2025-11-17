<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\MarketCourierStatus;
use Illuminate\Http\JsonResponse;

class MarketCourierStatusController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $status = MarketCourierStatus::current();

        return response()->json($status->toArrayForDisplay());
    }
}
