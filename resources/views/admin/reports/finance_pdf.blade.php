<!DOCTYPE html>
<html>
<head>
    <title>Laporan Keuangan</title>
    <style>
        body { font-family: 'Helvetica', 'sans-serif'; font-size: 10px; }
        .page-break { page-break-after: always; }
        .invoice-card { 
            border: 1px solid #eee; 
            margin-bottom: 20px; 
            padding: 15px;
            border-radius: 5px;
        }
        .invoice-header { 
            border-bottom: 1px solid #eee; 
            padding-bottom: 10px; 
            margin-bottom: 10px; 
        }
        .invoice-header h3 { margin: 0; font-size: 14px; }
        .invoice-header p { margin: 0; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f9f9f9; font-size: 9px; }
        .summary { margin-top: 10px; text-align: right; font-weight: bold; }
    </style>
</head>
<body>
    <h1 style="text-align: center;">Laporan Keuangan</h1>
    <p style="text-align: center; margin-top:-10px; font-size: 11px;">Dicetak pada: {{ now()->format('d F Y') }}</p>

    @foreach($invoices as $invoice)
        <div class="invoice-card">
            <div class="invoice-header">
                <h3>{{ $invoice->title }}</h3>
                <p>
                    <strong>Santri:</strong> {{ $invoice->student->name ?? 'N/A' }} | 
                    <strong>Status:</strong> <span style="font-weight: bold;">{{ ucfirst($invoice->status) }}</span> | 
                    <strong>Total:</strong> Rp {{ number_format($invoice->amount, 0, ',', '.') }}
                </p>
            </div>
            
            @if($invoice->payments->isNotEmpty())
                <p style="font-size: 11px; font-weight: bold; margin-bottom: 5px;">Detail Pembayaran:</p>
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal Bayar</th>
                            <th>Jumlah</th>
                            <th>Metode</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->payments as $payment)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d-m-Y') }}</td>
                            <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                            <td>{{ ucfirst($payment->payment_method) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <p class="summary">Total Dibayar: Rp {{ number_format($invoice->amount_paid, 0, ',', '.') }} | Sisa: Rp {{ number_format($invoice->amount - $invoice->amount_paid, 0, ',', '.') }}</p>
            @else
                <p style="font-style: italic; color: #777;">Belum ada pembayaran untuk tagihan ini.</p>
            @endif
        </div>
    @endforeach
</body>
</html>