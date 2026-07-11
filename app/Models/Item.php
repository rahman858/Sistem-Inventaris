<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;
    
    // Sesuaikan dengan nama tabel
    protected $table = 'items';
    
    // Sesuaikan dengan nama kolom yang sebenarnya di database
    protected $fillable = [
        'nama',
        'kode',
        'stok',
    ];
}