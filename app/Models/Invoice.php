<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'title', 'month', 'year', 'amount', 
        'amount_paid', 'due_date', 'status', 'paid_at','type'
    ];

    /**
     * [BARU] Menentukan tipe data kolom secara otomatis.
     * Ini akan mengubah 'paid_at' dan 'due_date' menjadi objek Carbon.
     */
    protected $casts = [
        'paid_at' => 'datetime',
        'due_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}