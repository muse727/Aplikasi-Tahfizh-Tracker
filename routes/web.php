<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TahfizhController;
use App\Http\Controllers\StudentReportController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Halaman utama, bisa diakses semua orang
Route::get('/', function () {
    return view('welcome');
});

// Halaman dashboard, bisa diakses setelah login
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

// GRUP UNTUK SEMUA HALAMAN YANG BUTUH LOGIN (SISWA, GURU, ADMIN)
Route::middleware('auth')->group(function () {
    // Halaman Profil (bawaan Laravel Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/students/{user}', [StudentReportController::class, 'show'])->name('students.show');
    Route::get('/students/{user}/report', [App\Http\Controllers\StudentReportController::class, 'generatePdfReport'])->name('students.report.pdf');
    // (TAMBAHKAN INI) Route untuk memproses aksi pindah kelas
    Route::post('/students/{user}/move-class', [App\Http\Controllers\StudentReportController::class, 'moveClass'])->name('students.moveClass');
    Route::get('/modules/{course}', [ProgressController::class, 'getModulesByCourse'])->name('modules.get');
    Route::get('/courses/{course}/students', [\App\Http\Controllers\CourseController::class, 'getStudentsByCourse'])->name('courses.students');


    // Halaman Input Progress (yang baru kita buat)
    Route::get('/progress/create', [ProgressController::class, 'create'])->name('progress.create');
    Route::post('/progress', [ProgressController::class, 'store'])->name('progress.store');
    Route::get('/tahfizh/create', [TahfizhController::class, 'create'])->name('tahfizh.create');
    Route::post('/tahfizh', [TahfizhController::class, 'store'])->name('tahfizh.store');
});


// GRUP UNTUK HALAMAN YANG HANYA BISA DIAKSES ADMIN
    Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    // Halaman Manajemen User
    Route::resource('users', UserController::class);
    // routes/web.php -> di dalam grup admin
    Route::get('/finance', [App\Http\Controllers\Admin\FinanceController::class, 'index'])->name('finance.index');
    Route::post('/finance/generate', [App\Http\Controllers\Admin\FinanceController::class, 'generateInvoices'])->name('finance.generate');
    Route::post('/finance/invoices/{invoice}/payments', [App\Http\Controllers\Admin\FinanceController::class, 'recordPayment'])->name('finance.recordPayment');
    Route::get('/finance/export', [App\Http\Controllers\Admin\FinanceController::class, 'export'])->name('finance.export');
    Route::post('/finance/invoices/{invoice}/payments', [App\Http\Controllers\Admin\FinanceController::class, 'recordPayment'])->name('finance.recordPayment');
    Route::delete('/finance/invoices/{invoice}', [App\Http\Controllers\Admin\FinanceController::class, 'destroy'])->name('finance.destroy');
    Route::delete('/finance/invoices', [App\Http\Controllers\Admin\FinanceController::class, 'bulkDestroy'])->name('finance.bulkDestroy');
    Route::get('/finance/export', [App\Http\Controllers\Admin\FinanceController::class, 'export'])->name('finance.export');
    Route::post('/finance/custom-invoice', [App\Http\Controllers\Admin\FinanceController::class, 'storeCustomInvoice'])->name('finance.storeCustom');

});

// Ini untuk route-route autentikasi (login, register, dll)
require __DIR__.'/auth.php';

