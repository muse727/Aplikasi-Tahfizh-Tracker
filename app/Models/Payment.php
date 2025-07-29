<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id', 'processed_by_user_id', 'amount', 
        'payment_date', 'payment_method', 'notes'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}