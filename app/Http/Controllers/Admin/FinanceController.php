<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\User;
use Carbon\Carbon;

class FinanceController extends Controller
{
    /**
     * Menampilkan dashboard keuangan utama.
     */
    public function index()
    {
        // Data untuk kartu rekapitulasi bulan ini
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $monthlyIncome = Invoice::where('month', $currentMonth)
            ->where('year', $currentYear)
            ->sum('amount_paid');

        $monthlyTotal = Invoice::where('month', $currentMonth)
            ->where('year', $currentYear)
            ->sum('amount');
            
        // [INI DIA VARIABEL YANG HILANG]
        // Query ini menghitung jumlah siswa unik yang punya tagihan belum lunas & sudah jatuh tempo
        $overdueCount = Invoice::whereIn('status', ['unpaid', 'partial'])
            ->where('due_date', '<', Carbon::now())
            ->distinct('student_id')
            ->count();

        // Data untuk tabel tagihan, diurutkan dari yang terbaru
        $invoices = Invoice::with('student')
            ->latest()
            ->paginate(15);

        // Pastikan 'overdueCount' ikut dikirim ke view
        return view('admin.finance.index', compact(
            'invoices', 
            'monthlyIncome', 
            'monthlyTotal',
            'overdueCount'
        ));
    }

    /**
     * Membuat tagihan untuk semua siswa aktif.
     */
    public function generateInvoices(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2024',
            'amount' => 'required|numeric|min:1',
            'due_date' => 'required|date'
        ]);

        // Perbaikan Carbon dari error sebelumnya
        $monthName = Carbon::createFromFormat('!m', $request->month)->translatedFormat('F');

        $students = User::where('role', 'siswa')->get();
        $generatedCount = 0;

        foreach ($students as $student) {
            $invoice = Invoice::firstOrCreate(
                [
                    'student_id' => $student->id,
                    'month' => $request->month,
                    'year' => $request->year,
                ],
                [
                    'title' => 'SPP Bulan ' . $monthName . ' ' . $request->year,
                    'amount' => $request->amount,
                    'due_date' => $request->due_date,
                    'status' => 'unpaid',
                ]
            );

            if ($invoice->wasRecentlyCreated) {
                $generatedCount++;
            }
        }

        return redirect()->route('admin.finance.index')
            ->with('success', "$generatedCount tagihan baru untuk bulan $monthName berhasil dibuat.");
    }
}