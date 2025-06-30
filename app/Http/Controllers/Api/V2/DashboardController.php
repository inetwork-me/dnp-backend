<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function overview(DashboardService $dashboardService): JsonResponse
    {
        return response()->json($dashboardService->getOverview());
    }
}
