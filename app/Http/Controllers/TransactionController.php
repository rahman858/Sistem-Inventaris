<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('item')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.transactions.index', compact('transactions'));
    }

    public function create()
    {
        $items = Item::orderBy('nama')->get();

        return view('admin.transactions.create', compact('items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $item = Item::findOrFail($request->item_id);

        if ($request->jumlah > $item->stok) {
            return back()->withInput()->withErrors(['jumlah' => 'Jumlah transaksi tidak boleh melebihi stok tersedia.']);
        }

        DB::transaction(function () use ($item, $request) {
            $item->decrement('stok', $request->jumlah);

            Transaction::create([
                'item_id' => $item->id,
                'jumlah' => $request->jumlah,
                'tipe' => 'keluar',
                'keterangan' => $request->keterangan,
            ]);
        });

        return redirect()->route('admin.transactions.index')
            ->with('success', 'Transaksi berhasil disimpan dan stok berhasil diperbarui.');
    }
}
