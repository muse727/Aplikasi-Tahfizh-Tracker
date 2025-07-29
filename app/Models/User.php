<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'current_course_id',
        'teacher_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Mendapatkan semua catatan progress yang dimiliki oleh User ini (sebagai siswa).
     */
    public function progressRecords()
    {
        return $this->hasMany(ProgressRecord::class, 'student_id');
    }

    /**
     * Mendapatkan data kelas aktif yang diikuti oleh user (jika user adalah siswa).
     */
    public function currentCourse()
    {
        return $this->belongsTo(Course::class, 'current_course_id');
    }

    /**
     * Mendapatkan data guru pembimbing dari user (jika user adalah siswa).
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function studentsAsTeacher()
    {
    return $this->hasMany(User::class, 'teacher_id');
   }

  
    
}

