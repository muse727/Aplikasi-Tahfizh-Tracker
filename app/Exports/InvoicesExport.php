<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InvoicesExport implements FromQuery, WithHeadings, WithMapping
{
    protected $ids;

    public function __construct(array $ids = null)
    {
        $this->ids = $ids;
    }

    public function query()
    {
        $query = Invoice::query()->with('student');

        if ($this->ids) {
            return $query->whereIn('id', $this->ids);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID Tagihan',
            'Nama Santri',
            'Tagihan Untuk',
            'Jumlah',
            'Sudah Dibayar',
            'Sisa',
            'Status',
            'Tanggal Lunas',
        ];
    }

    public function map($invoice): array
    {
        return [
            $invoice->id,
            $invoice->student->name ?? 'N/A',
            $invoice->title,
            $invoice->amount,
            $invoice->amount_paid,
            $invoice->amount - $invoice->amount_paid,
            ucfirst($invoice->status),
            $invoice->paid_at ? \Carbon\Carbon::parse($invoice->paid_at)->format('d-m-Y') : 'Belum Lunas',
        ];
    }
}