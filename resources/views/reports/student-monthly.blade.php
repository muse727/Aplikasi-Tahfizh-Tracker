<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Progress {{ $student->name }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; }
        .header p { margin: 0; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #dddddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .no-data { text-align: center; padding: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Progress Santri</h1>
        <p>NgajiTracker</p>
    </div>

    <h3>Nama Santri: {{ $student->name }}</h3>
    <p><strong>Periode Laporan:</strong> {{ $monthName }} {{ $year }}</p>
    <hr>

    <h4>Detail Aktivitas</h4>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Kelas</th>
                <th>Materi</th>
                <th>Penilaian</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($records as $record)
                <tr>
                    <td>{{ $record->record_date->format('d M Y') }}</td>
                    <td>{{ $record->learningModule->course->name }}</td>
                    <td>{{ $record->learningModule->module_name }}</td>
                    <td>{{ ucfirst($record->assessment) }}</td>
                    <td>{{ $record->notes ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="no-data">Tidak ada data progress untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>