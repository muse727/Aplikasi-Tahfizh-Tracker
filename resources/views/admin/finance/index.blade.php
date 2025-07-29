<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manajemen Keuangan
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            {{-- 1. KARTU REKAPITULASI --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-green-500">
                    <h3 class="text-sm font-medium text-gray-500">Pemasukan Bulan Ini</h3>
                    <p class="mt-2 text-3xl font-bold text-gray-900">Rp {{ number_format($monthlyIncome, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-blue-500">
                    <h3 class="text-sm font-medium text-gray-500">Total Tagihan Bulan Ini</h3>
                    <p class="mt-2 text-3xl font-bold text-gray-900">Rp {{ number_format($monthlyTotal, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-red-500">
                    <h3 class="text-sm font-medium text-gray-500">Santri Menunggak</h3>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $overdueCount }} Santri</p>
                </div>
            </div>

            {{-- 2. FORM GENERATOR TAGIHAN --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800">Buat Tagihan SPP Bulanan</h3>
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
                            <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                Buat Tagihan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- 3. TABEL DAFTAR TAGIHAN --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800">Daftar Semua Tagihan</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Santri</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tagihan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($invoices as $invoice)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $invoice->student->name ?? 'Santri Dihapus' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $invoice->title }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td>
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
                                            <a href="#" class="text-indigo-600 hover:text-indigo-900">Bayar/Detail</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Belum ada tagihan. Silakan buat tagihan baru di atas.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $invoices->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>