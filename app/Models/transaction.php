<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class transaction extends Model
{
    protected $table = 'transactions';
    protected $casts = [
        'balance' => 'float',
    ];

    protected $fillable = ['order_id', 'paid_amount', 'balance', 'transac_date', 'transac_amount', 'user_id', 'payment_method'];


    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
