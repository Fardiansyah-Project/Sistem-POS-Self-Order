<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\OrderSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderAvailabilityController extends Controller
{
    public function show(): JsonResponse
    {
        return response()->json(['is_open' => OrderSetting::current()->is_open]);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate(['is_open' => ['required', 'boolean']]);
        $setting = OrderSetting::current();
        $setting->update(['is_open' => $validated['is_open']]);

        return response()->json([
            'success' => true,
            'is_open' => $setting->fresh()->is_open,
            'message' => $setting->is_open ? 'Pemesanan dibuka.' : 'Pemesanan ditutup.',
        ]);
    }
}
