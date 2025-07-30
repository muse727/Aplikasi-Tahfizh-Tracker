<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manajemen Keuangan
        </h2>
    </x-slot>

    {{-- x-data untuk mengontrol modal dan checklist hapus massal --}}
    <div class="py-12" x-data="{ 
        openModal: false, 
        selectedInvoice: null, 
        selectedInvoices: [], 
        get allVisibleInvoices() { 
            return Array.from(document.querySelectorAll('.invoice-checkbox')).map(el => el.value) 
        } 
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md shadow-sm" role="alert">
                    <p class="font-bold">Sukses!</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            {{-- 1. KARTU REKAPITULASI --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-gradient-to-br from-green-500 to-emerald-600 text-white p-6 rounded-xl shadow-lg">
                    <h3 class="text-sm font-medium text-green-100">Pemasukan Bulan Ini</h3>
                    <p class="mt-2 text-3xl font-bold">Rp {{ number_format($monthlyIncome, 0, ',', '.') }}</p>
                </div>
                <div class="bg-gradient-to-br from-orange-400 to-amber-500 text-white p-6 rounded-xl shadow-lg">
                    <h3 class="text-sm font-medium text-orange-100">Sisa Tagihan Bulan Ini</h3>
                    <p class="mt-2 text-3xl font-bold">Rp {{ number_format($monthlyRemaining, 0, ',', '.') }}</p>
                </div>
                <div class="bg-gradient-to-br from-red-500 to-rose-600 text-white p-6 rounded-xl shadow-lg">
                    <h3 class="text-sm font-medium text-red-100">Santri Menunggak</h3>
                    <p class="mt-2 text-3xl font-bold">{{ $overdueCount }} Santri</p>
                </div>
            </div>

            {{-- 2. FORM GENERATOR TAGIHAN --}}
            {{-- Ganti form ini di resources/views/admin/finance/index.blade.php --}}

<div class="bg-white overflow-hidden shadow-md sm:rounded-xl mb-8">
    <div class="p-6 bg-white border-b border-gray-200">
        <h3 class="text-lg font-semibold mb-4 text-gray-800">Buat Tagihan SPP Bulanan</h3>

        {{-- [BARU] Menampilkan pesan error validasi --}}
        @if ($errors->any())
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md" role="alert">
                <strong class="font-bold">Oops! Terjadi kesalahan:</strong>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.finance.generate') }}" method="POST" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
            @csrf
            <div>
                <label for="month" class="block text-sm font-medium text-gray-700">Bulan</label>
                <select name="month" id="month" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    @for ($m=1; $m<=12; $m++)
                        <option value="{{ $m }}" {{ $m == date('m') ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m, 1)) }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label for="year" class="block text-sm font-medium text-gray-700">Tahun</label>
                <input type="number" name="year" id="year" value="{{ date('Y') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            </div>
            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700">Jumlah (Rp)</label>
                <input type="number" name="amount" id="amount" placeholder="Contoh: 150000" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            </div>
            <div>
                <label for="due_date" class="block text-sm font-medium text-gray-700">Jatuh Tempo</label>
                <input type="date" name="due_date" id="due_date" value="{{ date('Y-m-10', strtotime('+1 month')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            </div>
            <div class="md:col-span-1">
                <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition-colors">
                    Buat Tagihan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- 3. FORM TAGIHAN CUSTOM --}}
<div class="bg-white overflow-hidden shadow-md sm:rounded-xl mb-8">
    <div class="p-6 bg-white border-b border-gray-200">
        <h3 class="text-lg font-semibold mb-4 text-gray-800">Buat Tagihan Custom (Uang Masuk, dll)</h3>
        <form action="{{ route('admin.finance.storeCustom') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            @csrf
            {{-- Pilih Santri --}}
            <div class="md:col-span-1">
                <label for="student_id" class="block text-sm font-medium text-gray-700">Pilih Santri</label>
                <select name="student_id" id="student_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    <option value="">-- Pilih --</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}">{{ $student->name }}</option>
                    @endforeach
                </select>
            </div>
            {{-- Judul Tagihan --}}
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Judul Tagihan</label>
                <input type="text" name="title" id="title" placeholder="Contoh: Pembayaran Al-Qur'an" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            </div>
            {{-- Jumlah --}}
            <div>
                <label for="amount_custom" class="block text-sm font-medium text-gray-700">Jumlah (Rp)</label>
                <input type="number" name="amount" id="amount_custom" placeholder="Contoh: 200000" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            </div>
            {{-- Jatuh Tempo --}}
            <div>
                <label for="due_date_custom" class="block text-sm font-medium text-gray-700">Jatuh Tempo</label>
                <input type="date" name="due_date" id="due_date_custom" value="{{ date('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            </div>
            <div class="md:col-span-4">
                <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-teal-600 hover:bg-teal-700 transition-colors">
                    Buat Tagihan Custom
                </button>
            </div>
        </form>
    </div>
</div>

            {{-- 3. TABEL DAFTAR TAGIHAN --}}
            <div class="bg-white overflow-hidden shadow-md sm:rounded-xl">
                <div class="p-6 bg-white">
                    <div class="flex justify-between items-center mb-4 flex-wrap gap-4">
                        <h3 class="text-lg font-semibold text-gray-800">Daftar Semua Tagihan</h3>
                        <div class="flex items-center space-x-2">
                            <form action="{{ route('admin.finance.export') }}" method="GET" @submit="$event.target.ids.value = selectedInvoices.join(',')">
                                <input type="hidden" name="ids">
                                <input type="hidden" name="type" value="excel">
                                <button type="submit" class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-xs font-bold rounded-md hover:bg-green-700">
                                    <x-icons.excel class="w-4 h-4 mr-2"/> Excel
                                </button>
                            </form>
                            <form action="{{ route('admin.finance.export') }}" method="GET" @submit="$event.target.ids.value = selectedInvoices.join(',')">
                                <input type="hidden" name="ids">
                                <input type="hidden" name="type" value="pdf">
                                <button type="submit" class="inline-flex items-center px-3 py-2 bg-red-600 text-white text-xs font-bold rounded-md hover:bg-red-700">
                                    <x-icons.pdf class="w-4 h-4 mr-2"/> PDF
                                </button>
                            </form>
                            <form x-show="selectedInvoices.length > 0" x-cloak action="{{ route('admin.finance.bulkDestroy') }}" method="POST" @submit="$event.target.ids.value = selectedInvoices.join(',')" onsubmit="return confirm('Yakin hapus ' + selectedInvoices.length + ' tagihan?');">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="ids">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-rose-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase hover:bg-rose-500">
                                    Hapus (<span x-text="selectedInvoices.length"></span>)
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3">
                                        <input type="checkbox" @click="selectedInvoices = $event.target.checked ? allVisibleInvoices : []" class="rounded border-gray-300 text-indigo-600">
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Santri</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tagihan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
    @forelse ($invoices as $invoice)
        <tr class="hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4">
                <input type="checkbox" x-model="selectedInvoices" value="{{ $invoice->id }}" class="rounded border-gray-300 text-indigo-600 shadow-sm invoice-checkbox">
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $invoice->student->name ?? 'Santri Dihapus' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $invoice->title }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <div class="text-gray-900">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</div>
                @if($invoice->status == 'partial')
                    <div class="text-xs text-yellow-600">Sisa Rp {{ number_format($invoice->amount - $invoice->amount_paid, 0, ',', '.') }}</div>
                @endif
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                @if($invoice->status == 'paid')
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Lunas</span>
                @elseif($invoice->status == 'partial')
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Sebagian</span>
                @else
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Belum Bayar</span>
                @endif
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                <div class="flex items-center justify-center space-x-4">
                    @if($invoice->status != 'paid')
                        <button type="button" class="text-indigo-600 hover:text-indigo-900"
                            @click="openModal = true; selectedInvoice = {{ $invoice->load('student') }}">
                            Bayar/Cicil
                        </button>
                    @else
                        <span class="text-sm text-gray-400">Lunas</span>
                    @endif

                    {{-- [BARU] Tombol Kirim Notifikasi WhatsApp --}}
                    @if($invoice->student && $invoice->student->phone_number)
                        @php
                            $studentName = $invoice->student->name;
                            $invoiceTitle = $invoice->title;
                            $amount = 'Rp ' . number_format($invoice->amount - $invoice->amount_paid, 0, ',', '.');
                            $dueDate = \Carbon\Carbon::parse($invoice->due_date)->translatedFormat('d F Y');
                            $phoneNumber = $invoice->student->phone_number;

                            $message = "Assalamu'alaikum wr. wb. orang tua dari ananda *$studentName*,\n\nKami informasikan mengenai tagihan *$invoiceTitle* dengan sisa pembayaran sebesar *$amount* yang akan jatuh tempo pada tanggal *$dueDate*.\n\nTerima kasih.\n_[Nama Lembaga-mu]_";
                            
                            $waLink = 'https://wa.me/' . $phoneNumber . '?text=' . urlencode($message);
                        @endphp
                        <a href="{{ $waLink }}" target="_blank" class="text-green-600 hover:text-green-900" title="Kirim Notifikasi WA">
                            <x-icons.whatsapp class="w-5 h-5"/>
                        </a>
                    @endif
                </div>
            </td>
        </tr>
    @empty
        <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Belum ada tagihan.</td></tr>
    @endforelse
</tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $invoices->links() }}</div>
                </div>
            </div>
        </div>

        {{-- MODAL PEMBAYARAN --}}
        <div x-show="openModal" @keydown.escape.window="openModal = false" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" x-cloak>
            <div class="bg-white rounded-lg shadow-xl max-w-lg w-full" @click.outside="openModal = false">
                <template x-if="selectedInvoice">
                    <form :action="`/admin/finance/invoices/${selectedInvoice.id}/payments`" method="POST">
                        @csrf
                        <div class="p-6">
                            <div class="flex justify-between items-center border-b pb-2 mb-4">
                                <h3 class="text-lg font-semibold">Catat Pembayaran</h3>
                                <button type="button" @click="openModal = false" class="text-2xl leading-none">&times;</button>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-sm text-gray-600">Santri: <strong x-text="selectedInvoice.student.name"></strong></p>
                                    <p class="text-sm text-gray-600">Tagihan: <strong x-text="selectedInvoice.title"></strong></p>
                                    <p class="text-sm text-gray-600">Sisa Tagihan: <strong x-text="`Rp ${new Intl.NumberFormat('id-ID').format(selectedInvoice.amount - selectedInvoice.amount_paid)}`"></strong></p>
                                </div>
                                <div>
                                    <label for="amount" class="block text-sm font-medium text-gray-700">Jumlah Bayar (Rp)</label>
                                    <input type="number" name="amount" id="amount" :max="selectedInvoice.amount - selectedInvoice.amount_paid" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                </div>
                                <div>
                                    <label for="payment_date" class="block text-sm font-medium text-gray-700">Tanggal Bayar</label>
                                    <input type="date" name="payment_date" id="payment_date" value="{{ date('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                </div>
                                <div>
                                    <label for="payment_method" class="block text-sm font-medium text-gray-700">Metode Bayar</label>
                                    <select name="payment_method" id="payment_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                        <option value="cash">Tunai (Cash)</option>
                                        <option value="transfer">Transfer</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="notes" class="block text-sm font-medium text-gray-700">Catatan (Opsional)</label>
                                    <textarea name="notes" id="notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-6 py-3 text-right">
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                Simpan Pembayaran
                            </button>
                        </div>
                    </form>
                </template>
            </div>
        </div>
    </div>
</x-app-layout>