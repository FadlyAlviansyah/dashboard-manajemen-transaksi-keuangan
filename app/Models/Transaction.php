<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'bulan',
        'pendapatan',
        'pengeluaran',
        'm1_pen',
        'm1_peng',
        'm2_pen',
        'm2_peng',
        'm3_pen',
        'm3_peng',
        'm4_pen',
        'm4_peng',
    ];

    protected $casts = [
        'pendapatan' => 'decimal:2',
        'pengeluaran' => 'decimal:2',
        'm1_pen' => 'decimal:2',
        'm1_peng' => 'decimal:2',
        'm2_pen' => 'decimal:2',
        'm2_peng' => 'decimal:2',
        'm3_pen' => 'decimal:2',
        'm3_peng' => 'decimal:2',
        'm4_pen' => 'decimal:2',
        'm4_peng' => 'decimal:2',
    ];
}
