<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function getStudentsByCourse(Course $course)
    {
        $students = $course->students()->orderBy('name')->get();
        return response()->json($students);
    }
}