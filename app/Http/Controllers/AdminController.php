<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::check()) {
                return redirect('/login');
            }
            if (Auth::user()->role !== 'admin') {
                abort(403, 'Forbidden - Halaman ini hanya untuk Admin!');
            }
            return $next($request);
        });
    }

    // Dashboard - Menampilkan semua data
    public function dashboard()
    {
        $items = DB::table('items')->get();
        return view('admin.dashboard', compact('items'));
    }

    // AJAX: ambil data item untuk dashboard
    public function ajaxIndex(Request $request)
    {
        $search = $request->get('search', '');

        $items = DB::table('items')
            ->when($search, function ($query, $value) {
                return $query->where('nama', 'like', "%{$value}%")
                    ->orWhere('kode', 'like', "%{$value}%");
            })
            ->orderBy('id')
            ->get();

        return response()->json(['items' => $items]);
    }

    // AJAX: simpan data item baru
    public function ajaxStore(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|max:255|unique:items,kode',
            'stok' => 'required|integer|min:0',
        ]);

        $itemId = DB::table('items')->insertGetId([
            'nama' => $validated['nama'],
            'kode' => $validated['kode'],
            'stok' => $validated['stok'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Item berhasil ditambahkan!',
            'item' => DB::table('items')->where('id', $itemId)->first(),
        ], 201);
    }

    // AJAX: update data item
    public function ajaxUpdate(Request $request, $id)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|max:255|unique:items,kode,' . $id,
            'stok' => 'required|integer|min:0',
        ]);

        $updated = DB::table('items')->where('id', $id)->update([
            'nama' => $validated['nama'],
            'kode' => $validated['kode'],
            'stok' => $validated['stok'],
            'updated_at' => now(),
        ]);

        if (!$updated) {
            return response()->json(['message' => 'Item tidak ditemukan.'], 404);
        }

        return response()->json(['message' => 'Item berhasil diupdate!']);
    }

    // AJAX: hapus data item
    public function ajaxDestroy($id)
    {
        $deleted = DB::table('items')->where('id', $id)->delete();

        if (!$deleted) {
            return response()->json(['message' => 'Item tidak ditemukan.'], 404);
        }

        return response()->json(['message' => 'Item berhasil dihapus!']);
    }

    // Menampilkan form create
    public function create()
    {
        return view('admin.items.create');
    }

    // Menyimpan data baru
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|max:255|unique:items,kode',
            'stok' => 'required|integer|min:0',
        ]);

        DB::table('items')->insert([
            'nama' => $request->nama,
            'kode' => $request->kode,
            'stok' => $request->stok,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Item berhasil ditambahkan!');
    }

    // Menampilkan form edit
    public function edit($id)
    {
        $item = DB::table('items')->where('id', $id)->first();
        
        if (!$item) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Item tidak ditemukan!');
        }
        
        return view('admin.items.edit', compact('item'));
    }

    // Mengupdate data
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|max:255|unique:items,kode,' . $id,
            'stok' => 'required|integer|min:0',
        ]);

        DB::table('items')
            ->where('id', $id)
            ->update([
                'nama' => $request->nama,
                'kode' => $request->kode,
                'stok' => $request->stok,
                'updated_at' => now(),
            ]);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Item berhasil diupdate!');
    }

    // Menghapus data
    public function destroy($id)
    {
        DB::table('items')->where('id', $id)->delete();

        return redirect()->route('admin.dashboard')
            ->with('success', 'Item berhasil dihapus!');
    }
}