<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User; // <-- Pastikan ini ada


class Course extends Model
{
    use HasFactory;

    // TAMBAHKAN FUNGSI DI BAWAH INI
    public function learningModules()
    {
        return $this->hasMany(LearningModule::class, 'course_id');
    }

    public function students()
    {
        return $this->hasMany(User::class, 'current_course_id');
    }
}
