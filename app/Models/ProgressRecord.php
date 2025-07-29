<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgressRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'teacher_id',
        'learning_module_id',
        'assessment',
        'notes',
        'record_date',
        'details',
        'progress_count',
        'submission_type',
        'page_number', // <-- Pastikan ini ada
        'annotations', // <-- TAMBAHKAN INI
    ];

    protected $casts = [
        'details' => 'array',
        'record_date' => 'datetime',
        'annotations' => 'array', // <-- TAMBAHKAN INI
    ];

    public function learningModule()
    {
        return $this->belongsTo(LearningModule::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}