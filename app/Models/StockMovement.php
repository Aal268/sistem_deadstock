<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'type', 'status', 'quantity', 'price_at_transaction', 'reference_id', 'note', 'movement_date', 'user_id'];

    protected $casts = [
        'movement_date' => 'datetime',
        'price_at_transaction' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
