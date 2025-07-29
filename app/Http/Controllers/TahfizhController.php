<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Course;
use App\Models\ProgressRecord;
use Illuminate\Support\Facades\Auth;

class TahfizhController extends Controller
{
    public function create()
    {
        $students = User::where('role', 'siswa')->orderBy('name')->get();
        $tahfizhCourse = Course::where('name', 'LIKE', '%Tahfizh%')->first();
        $tahfizhModules = $tahfizhCourse ? $tahfizhCourse->learningModules()->orderBy('order_column')->get() : [];

        return view('tahfizh.create', compact('students', 'tahfizhModules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'learning_module_id' => 'required|exists:learning_modules,id',
            'submission_type' => 'required|in:ziyadah,murojaah',
            'assessment' => 'required|in:lancar,tidak lancar',
            'progress_count' => 'required_if:submission_type,ziyadah|nullable|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        // Jumlah ayat hanya dihitung jika jenisnya Ziyadah dan statusnya Lancar
        $ayatDihitung = ($request->submission_type == 'ziyadah' && $request->assessment == 'lancar') 
                        ? $request->progress_count 
                        : 0;

        ProgressRecord::create([
            'student_id' => $request->student_id,
            'teacher_id' => Auth::id(),
            'learning_module_id' => $request->learning_module_id,
            'assessment' => $request->assessment,
            'submission_type' => $request->submission_type,
            'progress_count' => $ayatDihitung,
            'notes' => $request->notes,
            'record_date' => now(),
        ]);

        return redirect()->route('tahfizh.create')
                         ->with('success', 'Setoran hafalan santri berhasil disimpan!');
    }
}
