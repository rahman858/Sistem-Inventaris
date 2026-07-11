<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function inventaris()
    {
        $items = Item::orderBy('id')->get();
        return view('user.inventaris', compact('items'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');

        $items = Item::when($query, function ($builder, $search) {
            return $builder->where('nama', 'like', "%{$search}%")
                ->orWhere('kode', 'like', "%{$search}%");
        })->orderBy('id')->get();

        return response()->json(['items' => $items]);
    }
}