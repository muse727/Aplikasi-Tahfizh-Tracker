<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\ProgressRecord; // Sebaiknya tambahkan ini untuk kejelasan
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class StudentReportController extends Controller
{
    /**
     * Menampilkan halaman detail progress untuk satu siswa.
     * Kode ini sudah benar dan siap untuk menampilkan anotasi.
     */
    public function show(User $user)
    {
        // Pastikan user yang diakses adalah siswa
        if ($user->role !== 'siswa') {
            abort(404);
        }

        // Ambil semua data progress milik siswa ini.
        // Data 'annotations' akan otomatis ikut terbawa di sini.
        $progressRecords = $user->progressRecords()
                            ->with('learningModule.course', 'teacher')
                            ->latest('record_date')
                            ->paginate(20);

        // Ambil semua data kelas untuk dropdown (untuk fitur pindah kelas)
        $courses = Course::orderBy('name')->get();

        // Kirim semua data yang dibutuhkan ke view 'students.show'
        return view('students.show', compact('user', 'progressRecords', 'courses'));
    }

    /**
     * (TIDAK DIUBAH) Fungsi untuk memproses aksi pindah kelas.
     * Saya tambahkan ini sebagai placeholder sesuai route kamu.
     */
    public function moveClass(Request $request, User $user)
    {
        // ... Logika untuk memindahkan kelas siswa ada di sini ...
        // Jangan hapus logika yang sudah ada di file aslimu.
        
        return back()->with('success', 'Siswa berhasil dipindahkan kelas.');
    }

    /**
     * (TIDAK DIUBAH) Membuat dan mengunduh laporan progress dalam format PDF.
     * Fungsi ini tidak saya sentuh sama sekali karena sudah penting.
     */
    public function generatePdfReport(Request $request, User $user)
    {
        // Validasi input bulan dan tahun
        $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2020',
        ]);

        $month = (int) $request->month;
        $year = (int) $request->year;

        // Ambil semua data progress siswa untuk bulan dan tahun yang dipilih
        $progressRecords = $user->progressRecords()
            ->with('learningModule.course', 'teacher')
            ->whereYear('record_date', $year)
            ->whereMonth('record_date', $month)
            ->orderBy('record_date', 'asc')
            ->get();
        
        // Data yang akan dikirim ke view PDF
        $data = [
            'student' => $user,
            'records' => $progressRecords,
            'monthName' => Carbon::create()->month($month)->format('F'),
            'year' => $year,
        ];

        // Buat PDF dari view 'reports.student-monthly'
        $pdf = Pdf::loadView('reports.student-monthly', $data);

        // Unduh file PDF dengan nama file yang dinamis
        return $pdf->download('laporan-progress-'.$user->name.'-'.$month.'-'.$year.'.pdf');
    }
}