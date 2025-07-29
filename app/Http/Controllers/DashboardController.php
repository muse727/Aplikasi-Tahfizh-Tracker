<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\ProgressRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // =======================================================
        // JIKA YANG LOGIN ADALAH ADMIN ATAU GURU
        // =======================================================
        if (in_array($user->role, ['admin', 'guru'])) {

        $viewData = []; // Siapkan array untuk menampung semua data
        
        // [DIPERBAIKI] Inisialisasi variabel $teachers di sini
        // agar selalu ada, meskipun yang login adalah guru.
        $viewData['teachers'] = collect(); 

        // Logika KHUSUS untuk Admin
        if($user->role == 'admin') {
            $viewData['totalStudents'] = User::where('role', 'siswa')->count();
            $viewData['totalCourses'] = Course::count();
            // Query ini akan mengisi variabel $teachers yang tadi kosong
            $viewData['teachers'] = User::where('role', 'guru')->withCount('studentsAsTeacher')->get();
        }

        // Logika KHUSUS untuk Guru
        if($user->role == 'guru') {
            $myStudents = User::where('role', 'siswa')->where('teacher_id', $user->id)->get();
            $myStudentIds = $myStudents->pluck('id');
            $viewData['myStudents'] = $myStudents;
            $viewData['recentActivities'] = ProgressRecord::whereIn('student_id', $myStudentIds)->with('student', 'learningModule')->latest('created_at')->take(5)->get();
        }

            // Logika untuk Chart (sama untuk Admin & Guru)
            $activityQuery = ProgressRecord::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
                ->where('created_at', '>=', now()->subDays(7));

            // Jika yang login guru, filter chart hanya untuk siswanya
            if ($user->role == 'guru') {
                $activityQuery->whereIn('student_id', $myStudentIds ?? []);
            }

            $activityData = $activityQuery->groupBy('date')->orderBy('date', 'asc')->get();
            $viewData['chartLabels'] = $activityData->pluck('date')->map(fn($date) => \Carbon\Carbon::parse($date)->format('d M'));
            $viewData['chartData'] = $activityData->pluck('count');

            return view('dashboard-pro', $viewData);
        }

        
        // =======================================================
        // JIKA YANG LOGIN ADALAH SISWA (DENGAN PERINGKAT BENAR)
        // =======================================================
        $activeCourse = $user->current_course_id ? Course::find($user->current_course_id) : null;
        $leaderboard = collect();
        $latestProgress = collect();
        $totalPoinSiswa = 0;
        $peringkatSiswa = 'N/A';
        $chartData = ['labels' => [], 'data' => []];

        if ($activeCourse) {
            // 1. (QUERY BARU) Query ini lebih kuat untuk menghitung dan mengurutkan
            $allRankedStudents = User::where('role', 'siswa')
                ->where('current_course_id', $activeCourse->id)
                ->select('users.*') // Ambil semua kolom dari tabel users
                ->selectSub(function ($query) use ($activeCourse) {
                    $query->from('progress_records')
                        ->join('learning_modules', 'progress_records.learning_module_id', '=', 'learning_modules.id')
                        ->whereColumn('progress_records.student_id', 'users.id')
                        ->where('learning_modules.course_id', $activeCourse->id)
                        ->whereIn('progress_records.assessment', ['lulus', 'lancar'])
                        ->selectRaw('sum(progress_records.progress_count)');
                }, 'total_score') // Buat kolom virtual bernama 'total_score'
                ->orderBy('total_score', 'desc') // Urutkan berdasarkan 'total_score'
                ->get();

            // 2. Cari peringkat siswa yang login dari daftar yang sudah terurut
            $userRank = $allRankedStudents->search(fn($student) => $student->id === $user->id);
            $peringkatSiswa = ($userRank !== false) ? $userRank + 1 : 'N/A';

            // 3. Ambil 5 teratas untuk ditampilkan di papan peringkat
            $leaderboard = $allRankedStudents->take(5);

            // 4. Siapkan data untuk chart
            $chartData['labels'] = $leaderboard->pluck('name');
            $chartData['data'] = $leaderboard->pluck('total_score');

            // 5. Hitung total poin siswa di kelas aktif
            $studentData = $allRankedStudents->firstWhere('id', $user->id);
            if ($studentData) {
                $totalPoinSiswa = $studentData->total_score;
            }
            
            // 6. Ambil riwayat progress terakhir
            $latestProgress = ProgressRecord::where('student_id', $user->id)
                ->whereHas('learningModule', fn($q) => $q->where('course_id', $activeCourse->id))
                ->with('teacher', 'learningModule')
                ->latest('record_date')->take(10)->get();
        }
        
        $totalSetoranSiswa = ProgressRecord::where('student_id', $user->id)->count();

        return view('dashboard', compact('user', 'activeCourse', 'leaderboard', 'latestProgress', 'totalPoinSiswa', 'chartData', 'totalSetoranSiswa', 'peringkatSiswa'));
    }
}
