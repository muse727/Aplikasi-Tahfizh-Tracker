<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use App\Models\ProgressRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgressController extends Controller
{
    public function create()
    {
        $students = User::where('role', 'siswa')->orderBy('name')->get();
        $courses = Course::where('name', 'not like', '%Tahfizh%')->orderBy('name')->get();
        return view('progress.create', compact('students', 'courses'));
    }

    /**
     * [FINAL & LENGKAP] Menyimpan data progress baru ke database.
     */
    public function store(Request $request)
    {

        $request->validate([
            'student_id' => 'required|exists:users,id',
            'learning_module_id' => 'required|exists:learning_modules,id',
            'assessment' => 'required|in:lulus,mengulang,lancar',
            'notes' => 'nullable|string',
            'page_number' => 'nullable|integer',
            'annotations' => 'nullable|json',
        ]);

        ProgressRecord::create([
            'student_id' => $request->student_id,
            'teacher_id' => Auth::id(),
            'learning_module_id' => $request->learning_module_id,
            'assessment' => $request->assessment,
            'notes' => $request->notes,
            'record_date' => now(),
            'page_number' => $request->page_number,
            'annotations' => json_decode($request->annotations),
            'progress_count' => 1, // <-- INI YANG TERLEWAT KEMARIN
        ]);

        return redirect()->route('progress.create')
                         ->with('success', 'Progress santri berhasil disimpan!');
    }

    // Fungsi getModulesByCourse tidak perlu diubah
    public function getModulesByCourse(Course $course)
    {
        return response()->json($course->learningModules()->orderBy('order_column')->get());
    }
}