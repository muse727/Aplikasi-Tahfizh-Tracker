<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Exports\InvoicesExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

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

    $monthlyIncome = Payment::whereMonth('payment_date', $currentMonth)
        ->whereYear('payment_date', $currentYear)
        ->sum('amount');

    $monthlyRemaining = Invoice::where('month', $currentMonth)
        ->where('year', $currentYear)
        ->sum(DB::raw('amount - amount_paid'));
        
    $overdueCount = Invoice::whereIn('status', ['unpaid', 'partial'])
        ->where('due_date', '<', Carbon::now())
        ->distinct('student_id')
        ->count();

    // [INI YANG HILANG] Ambil data semua siswa untuk form tagihan custom
    $students = User::where('role', 'siswa')->orderBy('name')->get();

    // Data untuk tabel tagihan, diurutkan dari yang terbaru
    $invoices = Invoice::with('student')
        ->latest()
        ->paginate(15);

    // Kirim semua variabel yang dibutuhkan, termasuk 'students'
    return view('admin.finance.index', compact(
        'invoices', 
        'monthlyIncome', 
        'monthlyRemaining',
        'overdueCount',
        'students' // Sekarang variabel ini sudah ada
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
    public function recordPayment(Request $request, Invoice $invoice)
    {
        $sisaTagihan = $invoice->amount - $invoice->amount_paid;

        $request->validate([
            'amount' => "required|numeric|min:1|max:{$sisaTagihan}",
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        // 1. Catat transaksi di tabel 'payments'
        $invoice->payments()->create([
            'processed_by_user_id' => Auth::id(),
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'notes' => $request->notes,
        ]);

        // 2. Update total yang sudah dibayar di tagihan
        $invoice->amount_paid += $request->amount;

        // 3. Update status tagihan
        if ($invoice->amount_paid >= $invoice->amount) {
            $invoice->status = 'paid';
            $invoice->paid_at = Carbon::now();
        } else {
            $invoice->status = 'partial';
        }

        $invoice->save();

        return redirect()->route('admin.finance.index')
            ->with('success', 'Pembayaran berhasil dicatat.');
    }
    public function destroy(Invoice $invoice)
{
    // Hapus tagihan. Pembayaran yang terhubung akan otomatis terhapus
    // karena kita sudah set 'onDelete('cascade')' di migrasi.
    $invoice->delete();

    return redirect()->route('admin.finance.index')
        ->with('success', 'Tagihan berhasil dihapus.');
}
public function bulkDestroy(Request $request)
{
    $request->validate([
        'ids' => 'required|string', // Terima sebagai string
    ]);

    // Ubah string "1,2,3" menjadi array [1, 2, 3]
    $invoiceIds = explode(',', $request->ids);

    Invoice::whereIn('id', $invoiceIds)->delete();

    return redirect()->route('admin.finance.index')
        ->with('success', 'Tagihan yang dipilih berhasil dihapus.');
}
public function export(Request $request)
{
    $ids = $request->input('ids') ? explode(',', $request->input('ids')) : null;
    $type = $request->input('type');

    $extension = $type === 'excel' ? 'xlsx' : 'pdf';
    $filename = 'laporan-keuangan-' . now()->format('d-m-Y') . '.' . $extension;

    if ($type === 'excel') {
        return Excel::download(new InvoicesExport($ids), $filename);
    }

    if ($type === 'pdf') {
        // [DIPERBAIKI] Ambil data invoice LENGKAP dengan detail pembayarannya
        $invoicesQuery = Invoice::query()->with(['student', 'payments']);

        if ($ids) {
            $invoicesQuery->whereIn('id', $ids);
        }
        $invoices = $invoicesQuery->get();
        
        $pdf = Pdf::loadView('admin.reports.finance_pdf', ['invoices' => $invoices]);
        return $pdf->download($filename);
    }

    return redirect()->back()->with('error', 'Tipe ekspor tidak valid.');
}
public function storeCustomInvoice(Request $request)
{
    $request->validate([
        'student_id' => 'required|exists:users,id',
        'title' => 'required|string|max:255',
        'amount' => 'required|numeric|min:1',
        'due_date' => 'required|date',
    ]);

    Invoice::create([
        'student_id' => $request->student_id,
        'title' => $request->title,
        'amount' => $request->amount,
        'due_date' => $request->due_date,
        'type' => 'custom', // Tandai sebagai tagihan custom
        // Kolom month & year bisa kita biarkan null untuk tagihan custom
        'month' => now()->month, // Atau isi dengan bulan saat ini
        'year' => now()->year,
        'status' => 'unpaid',
    ]);

    return redirect()->route('admin.finance.index')
        ->with('success', 'Tagihan custom berhasil dibuat.');
}
}