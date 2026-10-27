<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{

    protected $fillable = [
        'customer_id', 'name', 'gender', 'state', 'occupation', 'customer_segment', 'annual_income', 'join_date',
    ];

    protected $casts = [
        'join_date' => 'date',
        'annual_income' => 'decimal:2',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'customer_id', 'customer_id');
    }
}