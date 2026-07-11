<?php

namespace App\Models;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    protected $fillable = [
        'item_id',
        'jumlah',
        'tipe',
        'keterangan',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
