<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningModule extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'course_id',
        'module_name',
        'type',
        'order_column',
    ];

    // TAMBAHKAN FUNGSI RELASI INI
    /**
     * Mendapatkan data kelas (course) yang memiliki materi (module) ini.
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}