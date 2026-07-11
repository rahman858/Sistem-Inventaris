<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function items(Request $request): JsonResponse
    {
        $query = $request->query('q', '');

        $items = Item::when($query, function ($builder, $value) {
            return $builder->where('nama', 'like', "%{$value}%")
                ->orWhere('kode', 'like', "%{$value}%");
        })->orderBy('id')->get(['id', 'nama', 'kode', 'stok']);

        return response()->json([
            'status' => 'success',
            'data' => $items,
            'query' => $query,
        ]);
    }

    public function show($id): JsonResponse
    {
        $item = Item::find($id);

        if (!$item) {
            return response()->json([
                'status' => 'error',
                'message' => 'Item tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $item,
        ]);
    }
}
